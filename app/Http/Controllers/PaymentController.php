<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\EmailService;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private MidtransService $midtransService,
        private EmailService    $emailService,
    ) {}

    public function show(string $bookingCode)
    {
        $booking = Booking::with(['schedule.film', 'schedule.cinema', 'bookingSeats', 'user'])
            ->where('booking_code', $bookingCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $clientKey = config('midtrans.client_key');
        $snapJsUrl = config('midtrans.snap_js_url');

        return view('checkout.payment', compact('booking', 'clientKey', 'snapJsUrl'));
    }

    public function requestSnapToken(Request $request)
    {
        $request->validate(['booking_code' => 'required|string|exists:bookings,booking_code']);

        $booking = Booking::with(['user', 'schedule.film'])
            ->where('booking_code', $request->booking_code)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!in_array($booking->status, ['waiting_payment', 'pending'])) {
            return response()->json(['success' => false, 'message' => 'Status booking tidak valid.'], 422);
        }

        // Reuse existing snap token if still valid
        if ($booking->snap_token && $booking->status === 'waiting_payment') {
            return response()->json([
                'success'    => true,
                'snap_token' => $booking->snap_token,
                'order_id'   => $booking->midtrans_order_id,
            ]);
        }

        $result = $this->midtransService->requestSnapToken($booking);

        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 500);
        }

        $booking->update([
            'snap_token'        => $result['snap_token'],
            'midtrans_order_id' => $result['midtrans_order_id'],
        ]);

        return response()->json([
            'success'    => true,
            'snap_token' => $result['snap_token'],
            'order_id'   => $result['midtrans_order_id'],
        ]);
    }

    /**
     * Callback dari Riplabs setelah pembayaran Midtrans
     * POST /payment/midtrans/notification  (dari Riplabs)
     *
     * Riplabs mengirim: transaction_status, payment_type, order_id
     * Tanpa field 'key' — identifikasi cukup dari prefix order_id "INVTIKS"
     */
    public function callback(Request $request)
    {
        $transactionStatus = $request->input('transaction_status', '');
        $paymentType       = $request->input('payment_type', '');
        $midtransOrderId   = $request->input('order_id', '');

        // Validasi: order_id harus berawalan prefix TIKS
        if (!$transactionStatus || !$midtransOrderId) {
            return response()->json(['success' => false, 'message' => 'Parameter tidak lengkap'], 422);
        }

        if (!str_starts_with($midtransOrderId, 'TIKS')) {
            Log::warning('TIKS Callback: order_id prefix tidak dikenal', ['order_id' => $midtransOrderId]);
            return response()->json(['success' => false, 'message' => 'Order tidak dikenal'], 400);
        }

        $booking = Booking::with(['user', 'schedule.film', 'schedule.cinema', 'bookingSeats'])
            ->where('midtrans_order_id', $midtransOrderId)
            ->first();

        if (!$booking) {
            Log::warning('TIKS Callback: booking not found', ['order_id' => $midtransOrderId]);
            return response()->json(['success' => false, 'message' => 'Booking tidak ditemukan'], 404);
        }

        switch ($transactionStatus) {
            case 'settlement':
            case 'capture':
                if ($booking->status === 'paid') {
                    return response()->json(['success' => true, 'message' => 'Sudah diproses']);
                }
                DB::beginTransaction();
                try {
                    $booking->update([
                        'status'       => 'paid',
                        'payment_type' => $paymentType,
                        'paid_at'      => now(),
                    ]);
                    DB::commit();

                    try {
                        set_time_limit(120);
                        $this->emailService->sendTicketConfirmation($booking);
                    } catch (\Throwable $e) {
                        Log::warning('Email tiket gagal: ' . $e->getMessage());
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Callback error: ' . $e->getMessage());
                    return response()->json(['success' => false, 'message' => 'DB error'], 500);
                }
                return response()->json(['success' => true, 'message' => 'Pembayaran berhasil']);

            case 'pending':
                $booking->update(['status' => 'waiting_payment', 'payment_type' => $paymentType]);
                return response()->json(['success' => true, 'message' => 'Menunggu pembayaran']);

            case 'deny':
            case 'cancel':
                $booking->update(['status' => 'failed']);
                $this->restoreSeats($booking);
                return response()->json(['success' => true, 'message' => 'Dibatalkan']);

            case 'expire':
                $booking->update(['status' => 'expired']);
                $this->restoreSeats($booking);
                return response()->json(['success' => true, 'message' => 'Kadaluarsa']);

            default:
                return response()->json(['success' => true, 'message' => 'Status tidak dikenal']);
        }
    }

    /**
     * Fallback JS success
     */
    public function midtransSuccess(Request $request)
    {
        $orderId   = $request->input('order_id', '');
        $snapToken = $request->input('snap_token', '');

        $booking = Booking::with(['user', 'schedule.film', 'schedule.cinema', 'bookingSeats'])
            ->when($orderId, fn($q) => $q->where('midtrans_order_id', $orderId))
            ->when(!$orderId && $snapToken, fn($q) => $q->where('snap_token', $snapToken))
            ->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking tidak ditemukan'], 404);
        }

        if ($booking->status === 'paid') {
            return response()->json([
                'success'     => true,
                'redirect_to' => route('checkout.finish', $booking->booking_code),
            ]);
        }

        DB::beginTransaction();
        try {
            $booking->update(['status' => 'paid', 'paid_at' => now()]);
            DB::commit();

            try {
                set_time_limit(120);
                $this->emailService->sendTicketConfirmation($booking);
            } catch (\Throwable $e) {
                Log::warning('Email JS fallback gagal: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'DB error'], 500);
        }

        return response()->json([
            'success'     => true,
            'redirect_to' => route('checkout.finish', $booking->booking_code),
        ]);
    }

    /**
     * Redirect setelah Midtrans (lama — tidak dipakai Riplabs TIKS)
     */
    public function finish(Request $request)
    {
        $midtransOrderId = $request->query('order_id', '');
        if (!$midtransOrderId) return redirect()->route('home');

        $booking = Booking::where('midtrans_order_id', $midtransOrderId)->first();
        if (!$booking) return redirect()->route('home')->with('error', 'Booking tidak ditemukan.');

        return redirect()->route('checkout.finish', $booking->booking_code);
    }

    /**
     * Finish redirect dari Riplabs
     * GET /payment/finish-redirect?order_id=INVTIKS...&status_code=200&transaction_status=settlement
     *
     * Format Riplabs: redirect("https://tiks.arifsiddikm.com/payment/finish-redirect?order_id=...&status_code=...&transaction_status=...")
     */
    public function finishRedirect(Request $request)
    {
        $midtransOrderId   = $request->query('order_id', '');
        $transactionStatus = $request->query('transaction_status', '');

        if (!$midtransOrderId) {
            return redirect()->route('home')->with('error', 'Parameter tidak valid.');
        }

        $booking = Booking::where('midtrans_order_id', $midtransOrderId)->first();

        if (!$booking) {
            return redirect()->route('home')->with('error', 'Booking tidak ditemukan.');
        }

        // Jika callback Riplabs belum sempat jalan (race condition), update status di sini
        if ($transactionStatus === 'settlement' && $booking->status !== 'paid') {
            $booking->update(['status' => 'paid', 'paid_at' => now()]);
            try {
                app(EmailService::class)->sendTicketConfirmation($booking->load(['user','schedule.film','schedule.cinema','bookingSeats']));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Email finish-redirect gagal: ' . $e->getMessage());
            }
        }

        return redirect()->route('checkout.finish', $booking->booking_code);
    }

    private function restoreSeats(Booking $booking): void
    {
        $seatIds = $booking->bookingSeats->pluck('seat_id');
        \App\Models\Seat::whereIn('id', $seatIds)->update(['status' => 'available']);
        $booking->schedule->increment('available_seats', $booking->qty);
    }
}

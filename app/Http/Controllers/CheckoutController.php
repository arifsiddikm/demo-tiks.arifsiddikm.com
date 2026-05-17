<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\FilmSchedule;
use App\Models\Seat;
use App\Services\MidtransService;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    public function __construct(
        private MidtransService $midtransService,
        private EmailService    $emailService,
    ) {}

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:film_schedules,id',
            'seats'       => 'required|array|min:1|max:6',
            'seats.*'     => 'required|string',
        ], [
            'seats.required' => 'Pilih minimal 1 kursi.',
            'seats.max'      => 'Maksimal 6 kursi dalam satu transaksi.',
        ]);

        $schedule = FilmSchedule::with(['film', 'cinema'])->findOrFail($request->schedule_id);

        if (!$schedule->is_active) {
            return response()->json(['success' => false, 'message' => 'Jadwal ini tidak tersedia.'], 422);
        }

        // Check all requested seats are still available
        $requestedSeats = Seat::where('schedule_id', $schedule->id)
            ->whereIn('code', $request->seats)
            ->get();

        if ($requestedSeats->count() !== count($request->seats)) {
            return response()->json(['success' => false, 'message' => 'Beberapa kursi tidak ditemukan.'], 422);
        }

        $alreadyBooked = $requestedSeats->where('status', 'booked')->pluck('code');
        if ($alreadyBooked->isNotEmpty()) {
            return response()->json(['success' => false, 'message' => 'Kursi ' . $alreadyBooked->join(', ') . ' sudah dipesan.'], 422);
        }

        DB::beginTransaction();
        try {
            $qty        = count($request->seats);
            $totalPrice = $schedule->price * $qty;
            // Kode booking 5 digit angka — cocok untuk input numpad di kiosk redeem
            do {
                $bookingCode = (string) random_int(10000, 99999);
            } while (\App\Models\Booking::where('booking_code', $bookingCode)->exists());

            $booking = Booking::create([
                'booking_code'  => $bookingCode,
                'user_id'       => Auth::id(),
                'schedule_id'   => $schedule->id,
                'qty'           => $qty,
                'total_price'   => $totalPrice,
                'status'        => 'waiting_payment',
                'expired_at'    => now()->addHours(2),
            ]);

            foreach ($requestedSeats as $seat) {
                BookingSeat::create([
                    'booking_id' => $booking->id,
                    'seat_id'    => $seat->id,
                    'seat_code'  => $seat->code,
                ]);
                $seat->update(['status' => 'booked']);
            }

            // Update available seats
            $schedule->decrement('available_seats', $qty);

            DB::commit();

            return response()->json([
                'success'      => true,
                'redirect_to'  => route('payment.show', $booking->booking_code),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan. Coba lagi.'], 500);
        }
    }

    public function finish(string $bookingCode)
    {
        $booking = Booking::with(['schedule.film', 'schedule.cinema', 'bookingSeats', 'user'])
            ->where('booking_code', $bookingCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('checkout.finish', compact('booking'));
    }
}

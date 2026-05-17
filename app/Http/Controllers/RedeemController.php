<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\News;
use Illuminate\Http\Request;

class RedeemController extends Controller
{
    public function index()
    {
        return view('redeem.index');
    }

    public function check(Request $request)
    {
        $request->validate([
            'phone'        => 'required|string',
            'booking_code' => 'required|digits:5',
        ]);

        $phone = $request->phone;
        if (strpos($phone, '62') === 0) {
            $phone = '0' . substr($phone, 2);
        }

        $booking = Booking::with(['schedule.film', 'schedule.cinema', 'bookingSeats', 'user'])
            ->whereHas('user', fn($q) => $q->where('phone', $phone))
            ->where('booking_code', $request->booking_code)
            ->where('status', 'paid')
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan atau belum dibayar. Pastikan nomor HP dan kode booking benar.',
            ]);
        }

        if ($booking->is_redeemed) {
            return response()->json([
                'success'     => false,
                'already'     => true,
                'message'     => 'Tiket sudah diredeem pada ' . $booking->redeemed_at?->format('d M Y H:i') . '.',
                'booking'     => $this->formatBooking($booking),
            ]);
        }

        return response()->json([
            'success' => true,
            'booking' => $this->formatBooking($booking),
        ]);
    }

    public function redeem(Request $request)
    {
        $request->validate(['booking_code' => 'required|digits:5|exists:bookings,booking_code']);

        $booking = Booking::where('booking_code', $request->booking_code)
            ->where('status', 'paid')
            ->firstOrFail();

        if ($booking->is_redeemed) {
            return response()->json(['success' => false, 'message' => 'Tiket sudah diredeem sebelumnya.']);
        }

        $booking->update(['is_redeemed' => true, 'redeemed_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Tiket berhasil diredeem!']);
    }

    private function formatBooking(Booking $booking): array
    {
        return [
            'booking_code' => $booking->booking_code,
            'film'         => $booking->schedule->film->title,
            'cinema'       => $booking->schedule->cinema->name,
            'date'         => $booking->schedule->show_date->format('d M Y'),
            'time'         => $booking->schedule->formatted_time,
            'studio'       => $booking->schedule->studio,
            'seats'        => $booking->bookingSeats->pluck('seat_code')->join(', '),
            'qty'          => $booking->qty,
            'total'        => $booking->formatted_total,
            'is_redeemed'  => $booking->is_redeemed,
            'poster'       => $booking->schedule->film->poster_url,
        ];
    }
}


class NewsController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $news = News::where('is_published', true)
            ->when($category, fn($q) => $q->where('category', $category))
            ->latest('published_at')
            ->paginate(9);
        $categories = News::where('is_published', true)->distinct()->pluck('category');
        return view('news.index', compact('news', 'categories', 'category'));
    }

    public function show(string $slug)
    {
        $article = News::where('slug', $slug)->where('is_published', true)->firstOrFail();
        $related = News::where('is_published', true)->where('id', '!=', $article->id)->latest('published_at')->take(3)->get();
        return view('news.show', compact('article', 'related'));
    }
}

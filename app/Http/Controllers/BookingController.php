<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\TicketPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['schedule.film', 'schedule.cinema', 'bookingSeats'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('tickets.index', compact('bookings'));
    }

    public function show(string $bookingCode)
    {
        $booking = Booking::with(['schedule.film', 'schedule.cinema', 'bookingSeats'])
            ->where('booking_code', $bookingCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('tickets.show', compact('booking'));
    }

    public function downloadPdf(string $bookingCode, TicketPdfService $pdfService)
    {
        $booking = Booking::with(['schedule.film', 'schedule.cinema', 'bookingSeats', 'user'])
            ->where('booking_code', $bookingCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status !== 'paid') {
            abort(403, 'Tiket hanya tersedia setelah pembayaran lunas.');
        }

        return $pdfService->download($booking);
    }
}

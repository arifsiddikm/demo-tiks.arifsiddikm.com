<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class TicketPdfService
{
    /**
     * Generate PDF and return the temp file path
     */
    public function generate(Booking $booking): string
    {
        $booking->load(['user', 'schedule.film', 'schedule.cinema', 'bookingSeats']);

        $pdf = Pdf::loadView('tickets.pdf', compact('booking'))
            ->setPaper([0, 0, 226.77, 624], 'portrait') // ~8cm x 22cm — cinema ticket size
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'DejaVu Sans',
                'dpi'                  => 150,
            ]);

        $filename = 'ticket_' . $booking->booking_code . '_' . time() . '.pdf';
        $tempPath = sys_get_temp_dir() . '/' . $filename;
        file_put_contents($tempPath, $pdf->output());

        return $tempPath;
    }

    /**
     * Stream download to browser
     */
    public function download(Booking $booking)
    {
        $booking->load(['user', 'schedule.film', 'schedule.cinema', 'bookingSeats']);

        $pdf = Pdf::loadView('tickets.pdf', compact('booking'))
            ->setPaper([0, 0, 226.77, 624], 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'DejaVu Sans',
                'dpi'                  => 150,
            ]);

        return $pdf->download("Tiket-TIKS-{$booking->booking_code}.pdf");
    }
}

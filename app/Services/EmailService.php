<?php

namespace App\Services;

use App\Models\Booking;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class EmailService
{
    private function mailer(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply@arifsiddikm.com';
        $mail->Password   = 'SatuDua345!!';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom('noreply@arifsiddikm.com', 'TIKS Cinema');
        return $mail;
    }

    public function sendTicketConfirmation(Booking $booking): void
    {
        $booking->load(['user', 'schedule.film', 'schedule.cinema', 'bookingSeats']);
        $user     = $booking->user;
        $schedule = $booking->schedule;
        $film     = $schedule->film;

        // Generate PDF
        $pdfService = app(TicketPdfService::class);
        $pdfPath    = $pdfService->generate($booking);

        $mail = $this->mailer();

        // To user
        $mail->addAddress($user->phone . '@mail.com', $user->name); // fallback since no email

        // Also notify admin
        $mail->addBCC(config('midtrans.admin_email'), 'Admin TIKS');

        $mail->isHTML(true);
        $mail->Subject = "🎬 Tiket TIKS - {$film->title} | {$booking->booking_code}";

        $seatsStr  = $booking->bookingSeats->pluck('seat_code')->join(', ');
        $dateStr   = $schedule->show_date->format('d M Y');
        $timeStr   = $schedule->formatted_time;

        $mail->Body = $this->ticketEmailHtml($booking, $film, $schedule, $seatsStr, $dateStr, $timeStr);

        if ($pdfPath && file_exists($pdfPath)) {
            $mail->addAttachment($pdfPath, "Tiket-{$booking->booking_code}.pdf");
        }

        $mail->send();
        Log::info("Email tiket terkirim: {$booking->booking_code}");

        // Clean up temp PDF
        if ($pdfPath && file_exists($pdfPath)) {
            @unlink($pdfPath);
        }
    }

    private function ticketEmailHtml(Booking $booking, $film, $schedule, $seatsStr, $dateStr, $timeStr): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  body { font-family: Arial, sans-serif; background: #f5f0eb; margin: 0; padding: 20px; }
  .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; }
  .header { background: linear-gradient(135deg, #78350f, #92400e); color: white; padding: 32px; text-align: center; }
  .header h1 { margin: 0; font-size: 32px; letter-spacing: 4px; }
  .header p { margin: 8px 0 0; opacity: 0.8; }
  .ticket { background: #fffbf7; border: 2px dashed #d97706; margin: 20px; border-radius: 12px; padding: 24px; }
  .film-title { font-size: 22px; font-weight: bold; color: #78350f; margin-bottom: 16px; }
  .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #fef3c7; }
  .info-label { color: #92400e; font-size: 13px; }
  .info-value { font-weight: bold; color: #1c1917; }
  .code-box { background: #78350f; color: white; text-align: center; padding: 20px; margin: 20px; border-radius: 12px; }
  .code-box .code { font-size: 28px; font-weight: bold; letter-spacing: 4px; }
  .footer { text-align: center; padding: 20px; color: #78350f; font-size: 12px; }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h1>TIKS</h1>
    <p>Tiket Bioskop Digitalmu</p>
  </div>
  <div style="padding: 20px; text-align: center;">
    <h2 style="color: #78350f;">🎉 Pembayaran Berhasil!</h2>
    <p style="color: #57534e;">Tiketmu sudah siap. Tunjukkan kode booking di lobby bioskop.</p>
  </div>
  <div class="ticket">
    <div class="film-title">🎬 {$film->title}</div>
    <div class="info-row"><span class="info-label">Bioskop</span><span class="info-value">{$schedule->cinema->name}</span></div>
    <div class="info-row"><span class="info-label">Tanggal</span><span class="info-value">{$dateStr}</span></div>
    <div class="info-row"><span class="info-label">Jam Tayang</span><span class="info-value">{$timeStr} WIB</span></div>
    <div class="info-row"><span class="info-label">Studio</span><span class="info-value">{$schedule->studio} ({$schedule->film_type})</span></div>
    <div class="info-row"><span class="info-label">Kursi</span><span class="info-value">{$seatsStr}</span></div>
    <div class="info-row"><span class="info-label">Jumlah Tiket</span><span class="info-value">{$booking->qty} tiket</span></div>
    <div class="info-row"><span class="info-label">Total Bayar</span><span class="info-value">{$booking->formatted_total}</span></div>
  </div>
  <div class="code-box">
    <div style="font-size: 13px; opacity: 0.8; margin-bottom: 8px;">KODE BOOKING</div>
    <div class="code">{$booking->booking_code}</div>
    <div style="font-size: 12px; opacity: 0.7; margin-top: 8px;">Tunjukkan kode ini di mesin redeem bioskop</div>
  </div>
  <div class="footer">
    <p>File PDF tiket terlampir. Simpan baik-baik!</p>
    <p>© 2026 TIKS Cinema · Dibuat dengan ❤️</p>
  </div>
</div>
</body>
</html>
HTML;
    }
}

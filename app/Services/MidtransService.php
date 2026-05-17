<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class MidtransService
{
    public function requestSnapToken(Booking $booking): array
    {
        try {
            $user     = $booking->user;
            $schedule = $booking->schedule;
            $film     = $schedule->film;

            // Order ID harus diawali "TIKS" agar routing Riplabs cocok
            $orderId     = 'TIKS-' . $booking->id . '-' . time();
            $productName = $film->title . ' — ' . $schedule->show_date->format('d M Y') . ' ' . $schedule->formatted_time;

            // Format nomor HP ke 62xxx
            $phone = preg_replace('/\D/', '', $user->phone);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            } elseif (!str_starts_with($phone, '62')) {
                $phone = '62' . $phone;
            }

            // Riplabs Snaptokentiks.php membaca $_POST — harus kirim sebagai form (bukan JSON)
            // Required fields: order_id, total_harga, nama, email, namaproduk
            // Optional: notelp, key
            $response = Http::timeout(30)
                ->asForm()  // ← application/x-www-form-urlencoded, dibaca $_POST
                ->post(config('midtrans.riplabs_snaptoken_url'), [
                    'key'         => config('midtrans.riplabs_key'),
                    'order_id'    => $orderId,
                    'total_harga' => (int) $booking->total_price,
                    'nama'        => $user->name,
                    'email'       => $phone . '@tiks.id', // tidak ada email asli, pakai fallback
                    'notelp'      => $phone,
                    'namaproduk'  => $productName,
                ]);

            $data = $response->json();

            Log::info('Riplabs snaptoken response', ['data' => $data, 'order_id' => $orderId]);

            if (empty($data['status'])) {
                Log::error('Riplabs snap token gagal', ['response' => $data]);
                return [
                    'success' => false,
                    'message' => $data['message'] ?? 'Gagal mendapatkan snap token dari Riplabs.',
                ];
            }

            $booking->update(['midtrans_order_id' => $orderId]);

            return [
                'success'           => true,
                'snap_token'        => $data['snaptoken'],
                'midtrans_order_id' => $orderId,
            ];

        } catch (\Throwable $e) {
            Log::error('MidtransService error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat proses pembayaran. Coba lagi.'];
        }
    }
}

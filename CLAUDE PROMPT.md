# CLAUDE PROMPT — TIKS: Website Jual Tiket Bioskop Online

> Gunakan prompt ini untuk membangun ulang website TIKS dari awal di sesi baru. Upload prompt ini + berikan konteks singkat, dan Claude siap membangun semua fiturnya.

---

## Identitas Proyek

- **Nama:** TIKS
- **Konsep:** Website jual tiket bioskop online (mirip TIX.ID / Cineplex21)
- **Demo:** https://demo-tiks.arifsiddikm.com
- **Stack:** Laravel 12 + PHP 8.3 + SQLite/MySQL + Tailwind CSS CDN + Alpine.js
- **Payment:** Midtrans via Riplabs
- **PDF Tiket:** DomPDF

---

## Struktur Database

### Tabel Utama

```
users          — id, name, phone, password, role(admin|user), is_active
cities         — id, name, slug, is_active
cinemas        — id, city_id, name, slug, address, is_active
genres         — id, name, slug
films          — id, title, slug, synopsis, poster, trailer_url, duration, rating(SU|13+|17+), language, director, cast, release_date, status(now_showing|coming_soon), is_active
film_genres    — film_id, genre_id (pivot)
film_schedules — id, film_id, cinema_id, show_date, show_time, studio, film_type(2D|3D|IMAX), total_seats, available_seats, price, is_active
seats          — id, schedule_id, seat_code, is_booked
bookings       — id, user_id, schedule_id, booking_code, status(pending|paid|cancelled|expired), total_price, payment_method, snap_token, midtrans_order_id, paid_at, redeemed_at
booking_seats  — booking_id, seat_id, seat_code
news           — id, title, slug, excerpt, content, thumbnail, author_id, category, is_published, published_at
```

---

## Fitur Frontend (Public)

### Halaman Beranda (`/`)
- Hero section dengan headline + CTA button "Lihat Film" dan "Redeem Tiket"
- Statistik dinamis: jumlah film tayang, bioskop, kota
- Grid film "Sedang Tayang" dengan filter genre (Semua, Horor, Komedi, Drama, Aksi)
- Grid film "Segera Hadir" (status coming_soon)
- Section berita terbaru (3 artikel)
- Design: dark amber/brown color scheme, Tailwind CSS

### Halaman Detail Film (`/film/{slug}`)
- Info lengkap film: poster, judul, rating, durasi, genre, sutradara, pemeran
- Pemilihan Kota → filter jadwal berdasarkan kota yang dipilih (session)
- Pemilihan tanggal jadwal (horizontal date picker 14 hari ke depan)
- Pemilihan jadwal (daftar bioskop + jam tayang + harga + tipe studio)
- Klik jadwal → tampil seat map interaktif
- Seat map: grid 12 kolom × 8 baris (A-H), warna: hijau=tersedia, merah=terisi, biru=dipilih
- Klik kursi untuk pilih/deselect, maks 8 kursi per transaksi
- Summary: kursi dipilih + total harga
- Tombol "Lanjutkan ke Pembayaran" → POST /checkout

### Checkout & Payment
- `POST /checkout` → simpan booking status pending, redirect ke `/payment/{bookingCode}`
- Halaman payment: tampil detail booking + tombol "Bayar Sekarang"
- Request Midtrans Snap Token via Riplabs API
- Modal Midtrans muncul, user bayar
- `POST /payment/midtrans-success` → update status paid
- Callback notifikasi dari Riplabs: `POST /payment/midtrans/notification`
- Redirect finish: `/checkout/finish/{bookingCode}`

### Halaman Tiket (`/tickets`)
- List booking milik user yang sedang login
- Badge status: pending (kuning), paid (hijau), cancelled (merah)
- Klik → detail tiket per booking

### Detail Tiket (`/tickets/{bookingCode}`)
- Info lengkap: film, bioskop, tanggal, jam, studio, kursi, kode booking
- QR Code sederhana atau kode teks untuk redeem di loket
- Download tiket PDF

### Redeem Tiket (`/redeem`)
- Form input kode booking (untuk kiosk lobby)
- `POST /redeem/check` → tampil info tiket
- `POST /redeem/confirm` → tandai tiket sebagai redeemed

### Halaman Berita (`/news`)
- Grid artikel dengan thumbnail, kategori, excerpt
- Klik → detail artikel (`/news/{slug}`)

---

## Fitur Admin Panel (`/admin`)

### Dashboard
- Statistik: total film aktif, total penonton hari ini, total pendapatan, booking pending
- Chart booking 7 hari terakhir (opsional)

### Manajemen Film (`/admin/films`)
- CRUD film: judul, sinopsis, poster (upload file atau URL), durasi, rating, bahasa, sutradara, cast, tanggal rilis, status, genre (multi-select)
- Sub-halaman jadwal per film: tambah/hapus jadwal (bioskop, tanggal, jam, studio, tipe, harga, kapasitas)

### Manajemen Berita (`/admin/news`)
- CRUD artikel: judul, excerpt, konten (rich text), thumbnail, kategori, status publikasi

### Manajemen Pesanan (`/admin/orders`)
- List semua booking dengan filter status
- Detail booking: konfirmasi manual atau batalkan

### Manajemen Pengguna (`/admin/users`)
- List pengguna
- Toggle aktif/nonaktif
- Tambah user baru
- Hapus user

---

## Routes Penting

```
GET  /                              → HomeController@index
GET  /film/{slug}                   → FilmController@show
GET  /api/schedules                 → FilmController@schedulesByCityDate
GET  /api/seats/{scheduleId}        → FilmController@seats
POST /checkout                      → CheckoutController@store (auth)
GET  /payment/{bookingCode}         → PaymentController@show (auth)
POST /payment/snap-token            → PaymentController@requestSnapToken (auth)
POST /payment/midtrans-success      → PaymentController@midtransSuccess (auth)
POST /payment/midtrans/notification → PaymentController@callback (no CSRF)
GET  /payment/finish-redirect       → PaymentController@finishRedirect
GET  /tickets                       → BookingController@index (auth)
GET  /tickets/{code}                → BookingController@show (auth)
GET  /tickets/{code}/pdf            → BookingController@downloadPdf (auth)
GET  /redeem                        → RedeemController@index (public)
POST /redeem/check                  → RedeemController@check (public)
POST /redeem/confirm                → RedeemController@redeem (public)
GET  /news                          → NewsController@index (public)
GET  /news/{slug}                   → NewsController@show (public)
GET  /login                         → LoginController@showLogin (guest)
POST /login                         → LoginController@login
GET  /register                      → RegisterController@showRegister (guest)
POST /register                      → RegisterController@register
POST /logout                        → LoginController@logout (auth)
GET  /admin                         → Admin\DashboardController@index (auth+admin)
--- semua admin routes prefix /admin dengan middleware auth+admin ---
```

---

## Services

### MidtransService
- `createSnapToken(Booking $booking)` → POST ke Riplabs endpoint
- `verifyCallback(array $payload)` → verifikasi signature notifikasi

### EmailService
- `sendBookingConfirmation(Booking $booking)` → kirim email konfirmasi dengan detail tiket
- `sendTicketPdf(Booking $booking)` → kirim PDF tiket via email

### TicketPdfService
- `generate(Booking $booking)` → generate PDF tiket menggunakan DomPDF
- View: `resources/views/tickets/pdf.blade.php`

---

## Models & Accessor Penting

### Film
```php
// Mendukung poster berupa URL eksternal (Unsplash, dll) ATAU path lokal storage
public function getPosterUrlAttribute(): string {
    if ($this->poster) {
        if (str_starts_with($this->poster, 'http://') || str_starts_with($this->poster, 'https://')) {
            return $this->poster;
        }
        return asset('storage/' . $this->poster);
    }
    return asset('images/default-poster.jpg');
}
```

### News
```php
// Mendukung thumbnail berupa URL eksternal ATAU path lokal storage
public function getThumbnailUrlAttribute(): string {
    if ($this->thumbnail) {
        if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
            return $this->thumbnail;
        }
        if (Storage::disk('public')->exists($this->thumbnail)) {
            return asset('storage/' . $this->thumbnail);
        }
    }
    return asset('images/default-news.jpg');
}
```

---

## DatabaseSeeder

Seeder utama (`DatabaseSeeder.php`) harus isi:
1. **Users:** 1 admin (`admin123`) + 1 sample user (`user123`)
2. **Cities:** Cilegon, Jakarta, Bandung, Surabaya, Tangerang, Bekasi
3. **Cinemas:** 9 bioskop XXI tersebar di 6 kota
4. **Genres:** Drama, Horor, Aksi, Komedi, Thriller, Animasi, Sci-Fi, Romantis, Petualangan
5. **Films:** Minimal 20 film 2026 (campuran Indonesia + internasional), poster menggunakan Unsplash URL format: `https://images.unsplash.com/photo-{ID}?w=400&h=600&fit=crop`
6. **Film Genres:** pivot many-to-many film × genre
7. **Film Schedules:** Generate otomatis 14 hari ke depan, 4–6 bioskop per film, 3–5 jam tayang per hari, harga 50–65 ribu
8. **News:** Minimal 6 artikel dengan thumbnail Unsplash format: `https://images.unsplash.com/photo-{ID}?w=800&h=500&fit=crop`

---

## Konfigurasi .env Penting

```env
APP_NAME=TIKS
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# DB_CONNECTION=mysql (uncomment + isi untuk MySQL)

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=noreply@domain.com
MAIL_PASSWORD=password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@domain.com
MAIL_FROM_NAME="TIKS"

MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false
RIPLABS_MIDTRANS_URL=https://riplabs.id/api/midtrans
```

---

## Middleware

- `AdminMiddleware` — cek `auth()->user()->role === 'admin'`, redirect ke `/` jika bukan admin
- Semua route admin: `middleware(['auth', 'admin'])`
- Checkout & tiket: `middleware('auth')`
- Login/register: `middleware('guest')`

---

## Design System

- **Warna utama:** amber-500 (CTA), brown-700–900 (header/dark bg), stone-100–800 (body)
- **Font:** Inter atau sistem sans-serif
- **Border radius:** rounded-xl, rounded-2xl untuk card
- **Shadow:** shadow-sm default, hover:shadow-xl untuk card film
- **Animasi:** hover:-translate-y-1, scale-105 untuk poster film
- **Layout:** max-w-7xl mx-auto px-4 sm:px-6

---

## Catatan Implementasi

- Login menggunakan **nomor telepon**, bukan email
- Booking code format: `TIKS-XXXXXXXX` (uppercase random 8 karakter)
- Seat code format: `A1`, `B3`, `H12` (baris-kolom)
- Session kota dipilih tersimpan di `session('selected_city_id')`
- Seat map di-render via Alpine.js + AJAX (`GET /api/seats/{scheduleId}`)
- Jadwal di-filter via AJAX (`GET /api/schedules?film_id=&city_id=&date=`)
- PDF tiket menggunakan view `tickets/pdf.blade.php` dengan inline CSS (DomPDF-compatible)
- Riplabs callback tidak melalui CSRF: gunakan `withoutMiddleware([VerifyCsrfToken::class])`

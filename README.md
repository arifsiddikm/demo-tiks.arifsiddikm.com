# TIKS — Beli Tiket Bioskop Online

Website pembelian tiket bioskop online modern, dilengkapi admin panel (CMS) untuk mengelola film, jadwal, berita, dan pesanan.

🌐 **Live Demo:** [demo-tiks.arifsiddikm.com](https://demo-tiks.arifsiddikm.com)

---

## Tech Stack

- **Backend:** PHP 8.3 + Laravel 12
- **Database:** SQLite / MySQL
- **Frontend:** Tailwind CSS CDN · Alpine.js
- **Payment:** Midtrans via Riplabs
- **Email:** SMTP (PHPMailer)
- **PDF:** DomPDF (tiket PDF)
- **Font:** Inter (Google Fonts)

---

## Fitur

**Frontend Publik**
- Halaman beranda dengan daftar film sedang tayang & coming soon
- Filter film berdasarkan genre
- Halaman detail film + pemilihan jadwal & kota
- Pemilihan kursi interaktif (real-time seat map)
- Proses checkout & pembayaran via Midtrans
- Halaman berita / review film
- Redeem tiket (lobby kiosk)

**Akun Pengguna**
- Register & login via nomor telepon
- Riwayat pemesanan tiket (`/tickets`)
- Download tiket PDF
- Detail e-tiket per booking

**Admin Panel** (`/admin`)
- Dashboard ringkasan statistik
- CRUD Film + kelola jadwal tayang
- CRUD Berita / Artikel
- Kelola pesanan & konfirmasi pembayaran manual
- Manajemen pengguna (aktif/nonaktif)

---

## Instalasi

```bash
# 1. Clone repo
git clone https://github.com/arifsiddikm/tiks.git
cd tiks

# 2. Install dependencies
composer install

# 3. Copy dan konfigurasi .env
cp file env to .env and setting your password
php artisan key:generate

# 4. Setup database (SQLite default)
touch database/database.sqlite
php artisan migrate
php artisan db:seed

# 5. Storage link
php artisan storage:link

# 6. Jalankan server
php artisan serve
```

Akses di `http://localhost:8000`

---

## Login Admin

```
URL   : http://localhost:8000/admin
Phone : (role admin dari seeder)
Pass  : admin123
```

---

## Konfigurasi MySQL (opsional)

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tiks
DB_USERNAME=root
DB_PASSWORD=
```

Lalu jalankan ulang:

```bash
php artisan migrate
php artisan db:seed
```

---

## Konfigurasi Midtrans

Edit `.env`:

```env
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false
```

---

### Support me on

<a href="https://saweria.co/arifsiddikm" target="_blank"><img src="https://user-images.githubusercontent.com/26188697/180601310-e82c63e4-412b-4c36-b7b5-7ba713c80380.png" alt="Sawer me" height="41" width="174"></a>

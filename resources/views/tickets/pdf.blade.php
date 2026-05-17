<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    background: #ffffff;
    color: #1c1917;
    width: 226px;
    font-size: 10px;
  }

  .ticket {
    background: #fff;
    border: 2px solid #78350f;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 20px;
    page-break-after: always;
  }

  .ticket-header {
    background: linear-gradient(135deg, #78350f, #92400e);
    color: white;
    padding: 10px 12px;
    text-align: center;
  }

  .ticket-header .logo {
    font-size: 22px;
    font-weight: bold;
    letter-spacing: 4px;
  }

  .ticket-header .tagline {
    font-size: 7px;
    opacity: 0.7;
    margin-top: 2px;
    letter-spacing: 2px;
  }

  .film-title {
    background: #fef3c7;
    padding: 8px 12px;
    border-bottom: 1px dashed #d97706;
  }

  .film-title h2 {
    font-size: 11px;
    font-weight: bold;
    color: #78350f;
    line-height: 1.3;
  }

  .film-title .rating {
    display: inline-block;
    background: #78350f;
    color: white;
    font-size: 8px;
    font-weight: bold;
    padding: 1px 5px;
    border-radius: 3px;
    margin-top: 3px;
  }

  .info-section {
    padding: 8px 12px;
    border-bottom: 1px dashed #e7d5c0;
  }

  .info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 5px;
    gap: 6px;
  }

  .info-label {
    color: #a0522d;
    font-size: 8px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    flex-shrink: 0;
    width: 65px;
  }

  .info-value {
    color: #1c1917;
    font-size: 9px;
    font-weight: bold;
    text-align: right;
    flex: 1;
  }

  .seats-section {
    padding: 8px 12px;
    background: #fffbf5;
    border-bottom: 1px dashed #e7d5c0;
  }

  .seats-label {
    font-size: 8px;
    color: #a0522d;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
  }

  .seats-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 3px;
  }

  .seat-chip {
    background: #78350f;
    color: white;
    font-size: 9px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 3px;
    letter-spacing: 0.5px;
  }

  .code-section {
    padding: 10px 12px;
    text-align: center;
    background: #fff;
  }

  .code-label {
    font-size: 7px;
    color: #a0522d;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 4px;
  }

  .booking-code {
    font-size: 16px;
    font-weight: bold;
    color: #78350f;
    letter-spacing: 3px;
    border: 2px solid #78350f;
    padding: 6px 10px;
    border-radius: 6px;
    display: inline-block;
  }

  .barcode-placeholder {
    text-align: center;
    padding: 8px 12px 6px;
    border-top: 1px dashed #e7d5c0;
  }

  .barcode-line {
    height: 28px;
    background: repeating-linear-gradient(
      90deg,
      #1c1917 0px, #1c1917 1px,
      #fff 1px, #fff 2px,
      #1c1917 2px, #1c1917 4px,
      #fff 4px, #fff 5px,
      #1c1917 5px, #1c1917 7px,
      #fff 7px, #fff 9px,
      #1c1917 9px, #1c1917 10px,
      #fff 10px, #fff 12px
    );
    border-radius: 1px;
    margin-bottom: 3px;
  }

  .barcode-text {
    font-size: 7px;
    color: #78350f;
    font-weight: bold;
    letter-spacing: 1px;
  }

  .ticket-footer {
    background: #78350f;
    color: rgba(255,255,255,0.7);
    text-align: center;
    padding: 5px 12px;
    font-size: 7px;
    letter-spacing: 0.5px;
  }

  .total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fef3c7;
    padding: 5px 12px;
    border-top: 1px solid #fde68a;
    border-bottom: 1px solid #fde68a;
  }

  .total-label { font-size: 8px; color: #92400e; font-weight: bold; }
  .total-value { font-size: 11px; color: #78350f; font-weight: bold; }
</style>
</head>
<body>

@php
  $schedule = $booking->schedule;
  $film     = $schedule->film;
  $cinema   = $schedule->cinema;
  $seats    = $booking->bookingSeats;
@endphp

@foreach($seats as $idx => $seat)
<div class="ticket">
  {{-- Header --}}
  <div class="ticket-header">
    <div class="logo">TIKS</div>
    <div class="tagline">TIKET BIOSKOP DIGITAL</div>
  </div>

  {{-- Film --}}
  <div class="film-title">
    <h2>{{ $film->title }}</h2>
    <span class="rating">{{ $film->rating }}</span>
  </div>

  {{-- Info --}}
  <div class="info-section">
    <div class="info-row">
      <span class="info-label">Bioskop</span>
      <span class="info-value">{{ $cinema->name }}</span>
    </div>
    <div class="info-row">
      <span class="info-label">Kota</span>
      <span class="info-value">{{ $cinema->city->name }}</span>
    </div>
    <div class="info-row">
      <span class="info-label">Tanggal</span>
      <span class="info-value">{{ $schedule->show_date->format('d M Y') }}</span>
    </div>
    <div class="info-row">
      <span class="info-label">Jam Tayang</span>
      <span class="info-value">{{ $schedule->formatted_time }} WIB</span>
    </div>
    <div class="info-row">
      <span class="info-label">Studio</span>
      <span class="info-value">{{ $schedule->studio }} · {{ $schedule->film_type }}</span>
    </div>
    <div class="info-row">
      <span class="info-label">Penonton</span>
      <span class="info-value">{{ $booking->user->name }}</span>
    </div>
  </div>

  {{-- This specific seat --}}
  <div class="seats-section">
    <div class="seats-label">Kursi</div>
    <div class="seats-grid">
      <span class="seat-chip">{{ $seat->seat_code }}</span>
    </div>
    <div style="margin-top:4px; font-size:8px; color:#78350f;">Tiket {{ $idx + 1 }} dari {{ $seats->count() }}</div>
  </div>

  {{-- Total --}}
  <div class="total-row">
    <span class="total-label">Harga Tiket</span>
    <span class="total-value">Rp {{ number_format($schedule->price, 0, ',', '.') }}</span>
  </div>

  {{-- Booking Code --}}
  <div class="code-section">
    <div class="code-label">Kode Booking</div>
    <div class="booking-code">{{ $booking->booking_code }}</div>
  </div>

  {{-- Barcode simulation --}}
  <div class="barcode-placeholder">
    <div class="barcode-line"></div>
    <div class="barcode-text">{{ $booking->booking_code }}-{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</div>
  </div>

  {{-- Footer --}}
  <div class="ticket-footer">
    Tunjukkan kode ini di mesin redeem lobby bioskop · TIKS © {{ date('Y') }}
  </div>
</div>
@endforeach

</body>
</html>

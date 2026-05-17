@extends('layouts.app')
@section('title', 'Tiket Berhasil! - TIKS')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-10">

    {{-- Success Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex w-20 h-20 bg-green-100 rounded-3xl items-center justify-center text-4xl mb-4 animate-bounce">🎉</div>
        <h1 class="text-2xl font-bold text-stone-800">
            {{ $booking->status === 'paid' ? 'Pembayaran Berhasil!' : 'Booking Dibuat!' }}
        </h1>
        <p class="text-stone-500 mt-2">
            {{ $booking->status === 'paid' ? 'Tiketmu sudah aktif. Selamat menonton!' : 'Selesaikan pembayaranmu untuk mengaktifkan tiket.' }}
        </p>
    </div>

    {{-- Ticket Card --}}
    <div class="bg-white rounded-3xl border-2 border-dashed border-brown-300 overflow-hidden shadow-lg mb-6">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-brown-800 to-brown-900 text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-brown-300 text-xs font-semibold uppercase tracking-wider mb-1">TIKS · E-Ticket</p>
                    <h2 class="font-display text-xl font-bold">{{ $booking->schedule->film->title }}</h2>
                </div>
                <div class="w-12 h-12 bg-brown-700 rounded-xl flex items-center justify-center">
                    <span class="text-white font-display font-bold text-lg">T</span>
                </div>
            </div>
        </div>

        {{-- Tear line --}}
        <div class="flex items-center px-4">
            <div class="w-6 h-6 bg-cream rounded-full -ml-6 border-r border-brown-200"></div>
            <div class="flex-1 border-t-2 border-dashed border-brown-200 mx-2"></div>
            <div class="w-6 h-6 bg-cream rounded-full -mr-6 border-l border-brown-200"></div>
        </div>

        {{-- Details --}}
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-1">Bioskop</p>
                    <p class="font-semibold text-stone-800 text-sm">{{ $booking->schedule->cinema->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-1">Tanggal</p>
                    <p class="font-semibold text-stone-800 text-sm">{{ $booking->schedule->show_date->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-1">Jam Tayang</p>
                    <p class="font-semibold text-stone-800 text-sm">{{ $booking->schedule->formatted_time }} WIB</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-1">Studio</p>
                    <p class="font-semibold text-stone-800 text-sm">{{ $booking->schedule->studio }} ({{ $booking->schedule->film_type }})</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-1">Kursi</p>
                    <p class="font-semibold text-brown-700 text-sm">{{ $booking->seat_codes }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-1">Jumlah Tiket</p>
                    <p class="font-semibold text-stone-800 text-sm">{{ $booking->qty }} tiket</p>
                </div>
            </div>

            {{-- Tear line --}}
            <div class="flex items-center -mx-6 mb-5">
                <div class="w-6 h-6 bg-cream rounded-full -ml-3 border-r border-brown-200"></div>
                <div class="flex-1 border-t-2 border-dashed border-brown-200 mx-2"></div>
                <div class="w-6 h-6 bg-cream rounded-full -mr-3 border-l border-brown-200"></div>
            </div>

            {{-- Booking Code --}}
            <div class="bg-brown-800 rounded-2xl p-5 text-center">
                <p class="text-brown-300 text-xs font-semibold uppercase tracking-widest mb-2">Kode Booking</p>
                <p class="font-display text-3xl font-bold text-white tracking-widest">{{ $booking->booking_code }}</p>
                <p class="text-brown-300 text-xs mt-2">Tunjukkan kode ini di mesin redeem lobby bioskop</p>
            </div>

            {{-- Status badge --}}
            <div class="mt-4 flex justify-between items-center text-sm">
                <span class="text-stone-500">Status:</span>
                <span class="font-bold px-3 py-1 rounded-full text-xs
                    {{ $booking->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ $booking->status_label }}
                </span>
            </div>
            <div class="flex justify-between items-center text-sm mt-2">
                <span class="text-stone-500">Total Bayar:</span>
                <span class="font-bold text-brown-700">{{ $booking->formatted_total }}</span>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-col sm:flex-row gap-3">
        @if($booking->status === 'paid')
        <a href="{{ route('tickets.pdf', $booking->booking_code) }}"
           class="flex-1 bg-brown-700 hover:bg-brown-800 text-white font-bold py-3.5 rounded-xl text-center transition-colors text-sm flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Download Tiket PDF
        </a>
        @else
        <a href="{{ route('payment.show', $booking->booking_code) }}"
           class="flex-1 bg-brown-700 hover:bg-brown-800 text-white font-bold py-3.5 rounded-xl text-center transition-colors text-sm">
            ⚡ Bayar Sekarang
        </a>
        @endif
        <a href="{{ route('tickets.index') }}"
           class="flex-1 border-2 border-stone-200 text-stone-700 hover:bg-stone-50 font-semibold py-3.5 rounded-xl text-center transition-colors text-sm">
            Lihat Semua Tiket
        </a>
    </div>

    @if($booking->status === 'paid')
    <div class="mt-4 bg-amber-50 border border-amber-200 rounded-2xl p-4 text-sm text-amber-700">
        <p class="font-semibold mb-1">📧 Cek Emailmu!</p>
        <p class="text-xs">Tiket PDF sudah dikirim ke emailmu. Kamu juga bisa download langsung dari halaman ini.</p>
    </div>
    @endif
</div>
@endsection

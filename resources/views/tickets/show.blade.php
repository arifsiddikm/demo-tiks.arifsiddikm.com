@extends('layouts.app')
@section('title', 'Detail Tiket - ' . $booking->booking_code)

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-10">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('tickets.index') }}" class="p-2 rounded-xl hover:bg-stone-100 text-stone-500 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="font-bold text-stone-800">Detail Tiket</h1>
    </div>

    {{-- Ticket Card (same as finish page) --}}
    <div class="bg-white rounded-3xl border-2 border-dashed border-brown-300 overflow-hidden shadow-lg mb-6">
        <div class="bg-gradient-to-r from-brown-800 to-brown-900 text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-brown-300 text-xs font-semibold uppercase tracking-wider mb-1">TIKS · E-Ticket</p>
                    <h2 class="font-display text-xl font-bold">{{ $booking->schedule->film->title }}</h2>
                </div>
                <img src="{{ $booking->schedule->film->poster_url }}" alt=""
                     class="w-14 h-20 object-cover rounded-xl shadow-md"
                     onerror="this.style.display='none'">
            </div>
        </div>

        <div class="flex items-center px-4">
            <div class="w-6 h-6 bg-cream rounded-full -ml-6"></div>
            <div class="flex-1 border-t-2 border-dashed border-brown-200 mx-2"></div>
            <div class="w-6 h-6 bg-cream rounded-full -mr-6"></div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 gap-4 mb-5 text-sm">
                <div><p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-1">Bioskop</p><p class="font-semibold text-stone-800">{{ $booking->schedule->cinema->name }}</p></div>
                <div><p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-1">Tanggal</p><p class="font-semibold text-stone-800">{{ $booking->schedule->show_date->format('d M Y') }}</p></div>
                <div><p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-1">Jam Tayang</p><p class="font-semibold text-stone-800">{{ $booking->schedule->formatted_time }} WIB</p></div>
                <div><p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-1">Studio</p><p class="font-semibold text-stone-800">{{ $booking->schedule->studio }} ({{ $booking->schedule->film_type }})</p></div>
                <div><p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-1">Kursi</p>
                    <div class="flex flex-wrap gap-1">
                        @foreach($booking->bookingSeats as $seat)
                        <span class="bg-brown-700 text-white text-xs font-bold px-2 py-0.5 rounded-md">{{ $seat->seat_code }}</span>
                        @endforeach
                    </div>
                </div>
                <div><p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-1">Total Bayar</p><p class="font-bold text-brown-700">{{ $booking->formatted_total }}</p></div>
            </div>

            <div class="flex items-center -mx-6 mb-5">
                <div class="w-6 h-6 bg-cream rounded-full -ml-3"></div>
                <div class="flex-1 border-t-2 border-dashed border-brown-200 mx-2"></div>
                <div class="w-6 h-6 bg-cream rounded-full -mr-3"></div>
            </div>

            <div class="bg-brown-800 rounded-2xl p-5 text-center">
                <p class="text-brown-300 text-xs font-semibold uppercase tracking-widest mb-2">Kode Booking</p>
                <p class="font-display text-3xl font-bold text-white tracking-widest">{{ $booking->booking_code }}</p>
                @if($booking->is_redeemed)
                <div class="mt-3 bg-green-500/20 border border-green-400/30 rounded-xl px-3 py-1.5">
                    <p class="text-green-300 text-xs font-semibold">✅ Sudah Diredeem · {{ $booking->redeemed_at?->format('d M Y H:i') }}</p>
                </div>
                @else
                <p class="text-brown-300 text-xs mt-2">Tunjukkan kode ini di mesin redeem lobby bioskop</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-3">
        @if($booking->status === 'paid')
        <a href="{{ route('tickets.pdf', $booking->booking_code) }}"
           class="flex-1 bg-brown-700 hover:bg-brown-800 text-white font-bold py-3.5 rounded-xl text-center transition-colors text-sm flex items-center justify-center gap-2">
            📄 Download PDF Tiket
        </a>
        @elseif(in_array($booking->status, ['waiting_payment','pending']))
        <a href="{{ route('payment.show', $booking->booking_code) }}"
           class="flex-1 bg-amber-500 hover:bg-amber-600 text-stone-900 font-bold py-3.5 rounded-xl text-center transition-colors text-sm">
            ⚡ Selesaikan Pembayaran
        </a>
        @endif
        <a href="{{ route('tickets.index') }}"
           class="flex-1 border-2 border-stone-200 text-stone-700 hover:bg-stone-50 font-semibold py-3.5 rounded-xl text-center transition-colors text-sm">
            ← Kembali
        </a>
    </div>
</div>
@endsection

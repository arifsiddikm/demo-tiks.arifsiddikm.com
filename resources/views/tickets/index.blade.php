@extends('layouts.app')
@section('title', 'Tiket Saya - TIKS')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-display text-2xl font-bold text-stone-800">🎟️ Tiket Saya</h1>
            <p class="text-stone-500 text-sm mt-1">Riwayat pembelian tiket bioskopmu</p>
        </div>
    </div>

    @if($bookings->isEmpty())
    <div class="bg-white rounded-3xl border border-stone-200 p-12 text-center">
        <div class="text-5xl mb-4">🎬</div>
        <h3 class="font-bold text-stone-700 text-lg mb-2">Belum Ada Tiket</h3>
        <p class="text-stone-400 text-sm mb-6">Yuk beli tiket film favoritmu sekarang!</p>
        <a href="{{ route('home') }}" class="inline-block bg-brown-700 hover:bg-brown-800 text-white font-bold px-8 py-3 rounded-xl transition-colors">
            Lihat Film Sekarang
        </a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($bookings as $booking)
        <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="flex gap-4 p-4">
                {{-- Poster --}}
                <img src="{{ $booking->schedule->film->poster_url }}" loading="lazy"
                     alt="{{ $booking->schedule->film->title }}"
                     class="w-16 h-24 object-cover rounded-xl flex-shrink-0"
                     onerror="this.onerror=null;this.src='{{ asset('images/default-poster.jpg') }}'">

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <h3 class="font-bold text-stone-800 truncate">{{ $booking->schedule->film->title }}</h3>
                        <span class="flex-shrink-0 text-xs font-bold px-2.5 py-1 rounded-full
                            {{ $booking->status === 'paid' ? 'bg-green-100 text-green-700' :
                               ($booking->status === 'waiting_payment' ? 'bg-amber-100 text-amber-700' :
                               ($booking->status === 'pending' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-600')) }}">
                            {{ $booking->status_label }}
                        </span>
                    </div>
                    <div class="text-xs text-stone-500 space-y-0.5">
                        <p>📍 {{ $booking->schedule->cinema->name }}</p>
                        <p>📅 {{ $booking->schedule->show_date->format('d M Y') }} · {{ $booking->schedule->formatted_time }}</p>
                        <p>💺 {{ $booking->seat_codes }} · {{ $booking->qty }} tiket</p>
                        <p class="font-semibold text-brown-700">{{ $booking->formatted_total }}</p>
                    </div>
                    <p class="text-xs text-stone-400 mt-1">{{ $booking->booking_code }}</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="border-t border-stone-100 px-4 py-3 flex gap-2 flex-wrap">
                <a href="{{ route('tickets.show', $booking->booking_code) }}"
                   class="text-xs font-semibold text-brown-700 hover:text-brown-900 bg-brown-50 hover:bg-brown-100 px-3 py-1.5 rounded-lg transition-colors">
                    Lihat Detail
                </a>
                @if($booking->status === 'waiting_payment' || $booking->status === 'pending')
                <a href="{{ route('payment.show', $booking->booking_code) }}"
                   class="text-xs font-semibold text-white bg-amber-500 hover:bg-amber-600 px-3 py-1.5 rounded-lg transition-colors">
                    ⚡ Bayar Sekarang
                </a>
                @endif
                @if($booking->status === 'paid')
                <a href="{{ route('tickets.pdf', $booking->booking_code) }}"
                   class="text-xs font-semibold text-stone-600 hover:text-stone-800 bg-stone-100 hover:bg-stone-200 px-3 py-1.5 rounded-lg transition-colors">
                    📄 Download PDF
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    {{ $bookings->links() }}
    @endif
</div>
@endsection

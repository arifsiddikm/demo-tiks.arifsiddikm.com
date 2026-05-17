@extends('layouts.app')
@section('title', 'Pembayaran - ' . $booking->booking_code . ' - TIKS')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-10">

    <div class="text-center mb-8">
        <div class="inline-flex w-14 h-14 bg-brown-100 rounded-2xl items-center justify-center text-2xl mb-3">💳</div>
        <h1 class="text-2xl font-bold text-stone-800">Selesaikan Pembayaran</h1>
        <p class="text-stone-500 mt-1 text-sm">Kode Booking: <span class="font-bold text-brown-700">{{ $booking->booking_code }}</span></p>
    </div>

    {{-- Already Paid --}}
    @if($booking->status === 'paid')
    <div class="bg-green-50 border border-green-200 rounded-2xl p-8 text-center">
        <div class="text-4xl mb-3">✅</div>
        <h3 class="font-bold text-green-800 text-lg">Pembayaran Sudah Lunas!</h3>
        <p class="text-green-600 text-sm mt-1">Tiketmu sudah aktif dan dapat digunakan.</p>
        <a href="{{ route('checkout.finish', $booking->booking_code) }}"
           class="inline-block mt-4 bg-green-600 hover:bg-green-700 text-white font-bold px-8 py-3 rounded-xl transition-colors">
            Lihat Tiket →
        </a>
    </div>

    @elseif(in_array($booking->status, ['failed', 'expired', 'cancelled']))
    <div class="bg-red-50 border border-red-200 rounded-2xl p-8 text-center">
        <div class="text-4xl mb-3">❌</div>
        <h3 class="font-bold text-red-800 text-lg">Pembayaran {{ ucfirst($booking->status) }}</h3>
        <p class="text-red-600 text-sm mt-1">Booking ini sudah tidak bisa dilanjutkan.</p>
        <a href="{{ route('home') }}" class="inline-block mt-4 bg-brown-700 hover:bg-brown-800 text-white font-bold px-8 py-3 rounded-xl transition-colors">
            Kembali ke Beranda
        </a>
    </div>

    @else
    {{-- Order Summary --}}
    <div class="bg-white rounded-2xl border border-stone-200 p-6 mb-5">
        <h3 class="font-bold text-stone-800 mb-4 flex items-center gap-2">
            🎬 Ringkasan Pesanan
        </h3>

        <div class="flex gap-4">
            <img src="{{ $booking->schedule->film->poster_url }}" alt="{{ $booking->schedule->film->title }}"
                 class="w-16 h-24 object-cover rounded-xl flex-shrink-0 shadow-sm"
                 onerror="this.onerror=null;this.src='{{ asset('images/default-poster.jpg') }}'">
            <div class="flex-1 space-y-2 text-sm">
                <p class="font-bold text-stone-800 text-base">{{ $booking->schedule->film->title }}</p>
                <div class="text-stone-500 space-y-1">
                    <p>📍 {{ $booking->schedule->cinema->name }}</p>
                    <p>📅 {{ $booking->schedule->show_date->format('d M Y') }} · {{ $booking->schedule->formatted_time }}</p>
                    <p>🎭 {{ $booking->schedule->studio }} · {{ $booking->schedule->film_type }}</p>
                    <p>💺 Kursi: <span class="font-semibold text-stone-700">{{ $booking->seat_codes }}</span></p>
                    <p>🎟️ {{ $booking->qty }} tiket</p>
                </div>
            </div>
        </div>

        <div class="border-t border-stone-100 mt-4 pt-4 flex justify-between items-center">
            <span class="font-semibold text-stone-700">Total Pembayaran</span>
            <span class="font-bold text-xl text-brown-700">{{ $booking->formatted_total }}</span>
        </div>

        {{-- Expiry --}}
        @if($booking->expired_at)
        <div class="mt-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-2.5 text-xs text-amber-700 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Selesaikan pembayaran sebelum <strong>{{ $booking->expired_at->format('d M Y H:i') }}</strong>
        </div>
        @endif
    </div>

    {{-- Midtrans Payment --}}
    <div class="bg-white rounded-2xl border border-stone-200 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-xl">⚡</div>
            <div>
                <h3 class="font-bold text-stone-800">Bayar via Midtrans</h3>
                <p class="text-xs text-stone-500">GoPay, QRIS, Transfer VA, Kartu Kredit, dan lainnya</p>
            </div>
        </div>

        <div id="midtrans-status" class="hidden mb-4 p-3 rounded-xl text-sm font-medium"></div>

        <button id="pay-btn" onclick="openMidtrans()"
            class="w-full bg-brown-700 hover:bg-brown-800 text-white font-bold py-4 rounded-xl transition-all shadow-md hover:shadow-brown-700/30 flex items-center justify-center gap-2 text-base">
            <span id="pay-btn-text">⚡ Bayar {{ $booking->formatted_total }}</span>
            <svg id="pay-spinner" class="hidden w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
        </button>
        <p class="text-xs text-stone-400 text-center mt-3">🔒 Pembayaran aman diproses oleh Midtrans</p>

        {{-- Payment method logos --}}
        <div class="flex justify-center gap-3 mt-4 flex-wrap">
            @foreach(['GoPay','QRIS','BCA','Mandiri','BNI','BRI','OVO'] as $pm)
            <span class="text-xs bg-stone-50 border border-stone-200 text-stone-500 px-2.5 py-1 rounded-lg font-medium">{{ $pm }}</span>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
@if(!in_array($booking->status, ['paid','failed','expired','cancelled']))
<script src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>
<script>
let snapToken = '{{ $booking->snap_token ?? '' }}';
const bookingCode = '{{ $booking->booking_code }}';

async function openMidtrans() {
    const btn     = document.getElementById('pay-btn');
    const spinner = document.getElementById('pay-spinner');
    const btnText = document.getElementById('pay-btn-text');

    btn.disabled = true;
    spinner.classList.remove('hidden');
    btnText.textContent = 'Memuat...';

    if (!snapToken) {
        try {
            const res = await fetch('{{ route("payment.snap-token") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ booking_code: bookingCode })
            });
            const data = await res.json();
            if (!data.success) {
                showStatus('error', data.message || 'Gagal memuat pembayaran. Coba lagi.');
                btn.disabled = false; spinner.classList.add('hidden');
                btnText.textContent = '⚡ Coba Lagi';
                return;
            }
            snapToken = data.snap_token;
        } catch(e) {
            showStatus('error', 'Koneksi gagal. Periksa internet dan coba lagi.');
            btn.disabled = false; spinner.classList.add('hidden');
            btnText.textContent = '⚡ Coba Lagi';
            return;
        }
    }

    btn.disabled = false;
    spinner.classList.add('hidden');
    btnText.textContent = '⚡ Bayar {{ $booking->formatted_total }}';

    snap.pay(snapToken, {
        onSuccess: async function(result) {
            showStatus('success', '✅ Pembayaran berhasil! Mengalihkan ke halaman tiket...');
            btn.disabled = true;
            try {
                const r = await fetch('{{ route("payment.midtrans-success") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ order_id: result.order_id, snap_token: snapToken })
                });
                const d = await r.json();
                if (d.redirect_to) window.location = d.redirect_to;
                else window.location = '{{ route("checkout.finish", $booking->booking_code) }}';
            } catch(e) {
                window.location = '{{ route("checkout.finish", $booking->booking_code) }}';
            }
        },
        onPending: function(result) {
            showStatus('info', '⏳ Pembayaran sedang diproses. Cek kembali nanti.');
        },
        onError: function(result) {
            showStatus('error', '❌ Pembayaran gagal. Coba lagi.');
            btn.disabled = false;
        },
        onClose: function() {
            btn.disabled = false;
            spinner.classList.add('hidden');
            btnText.textContent = '⚡ Bayar {{ $booking->formatted_total }}';
        }
    });
}

function showStatus(type, msg) {
    const el = document.getElementById('midtrans-status');
    const cls = {
        success: 'bg-green-50 text-green-700 border border-green-200',
        error:   'bg-red-50 text-red-700 border border-red-200',
        info:    'bg-blue-50 text-blue-700 border border-blue-200',
    };
    el.className = 'mb-4 p-3 rounded-xl text-sm font-medium ' + cls[type];
    el.textContent = msg;
    el.classList.remove('hidden');
}
</script>
@endif
@endpush
@endsection

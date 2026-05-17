@extends('layouts.app')
@section('title', 'Redeem Tiket - TIKS')
@section('meta_description', 'Redeem tiket bioskop TIKS di lobby. Masukkan nomor HP dan kode booking.')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-brown-900 via-brown-800 to-stone-900 flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-sm">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 bg-amber-500 rounded-2xl items-center justify-center text-3xl mb-3 shadow-lg">🎟️</div>
            <h1 class="font-display text-3xl font-bold text-white">TIKS Redeem</h1>
            <p class="text-brown-300 text-sm mt-1">Masukkan data tiket untuk redeem di lobby</p>
        </div>

        {{-- Panel --}}
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">

            {{-- Step 1: Input --}}
            <div id="panel-input" class="p-6">
                <div class="space-y-4 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Nomor HP</label>
                        <input type="text" id="inp-phone" placeholder="08xxxxxxxxxx" maxlength="15"
                               class="w-full border-2 border-stone-200 rounded-xl px-4 py-3 text-stone-800 text-base font-mono focus:outline-none focus:border-brown-500 focus:ring-2 focus:ring-brown-100 transition-all text-center tracking-widest">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Kode Booking</label>
                        <input type="text" id="inp-code" placeholder="12345" maxlength="5"
                               class="w-full border-2 border-stone-200 rounded-xl px-4 py-3 text-stone-800 text-base font-mono focus:outline-none focus:border-brown-500 focus:ring-2 focus:ring-brown-100 transition-all text-center tracking-widest"
                               oninput="this.value = this.value.replace(/\D/g,'').slice(0,5)">
                    </div>
                </div>

                {{-- Numpad --}}
                <div id="active-field" class="hidden">
                    <p class="text-xs text-stone-400 text-center mb-3">Ketuk field di atas untuk mulai mengetik, atau gunakan numpad</p>
                </div>
                <div class="grid grid-cols-3 gap-2 mb-4">
                    @foreach(['1','2','3','4','5','6','7','8','9','*','0','⌫'] as $key)
                    <button onclick="numpadPress('{{ $key }}')"
                        class="bg-stone-50 hover:bg-brown-50 hover:text-brown-700 border border-stone-200 rounded-xl py-4 text-lg font-bold text-stone-700 transition-all active:scale-95 active:bg-brown-100">
                        {{ $key }}
                    </button>
                    @endforeach
                </div>

                <button onclick="checkTicket()" id="check-btn"
                    class="w-full bg-brown-700 hover:bg-brown-800 text-white font-bold py-4 rounded-xl transition-all shadow-lg text-base flex items-center justify-center gap-2">
                    <span id="check-btn-text">🔍 Cari Tiket</span>
                    <svg id="check-spinner" class="hidden w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </button>

                <div id="error-msg" class="hidden mt-3 bg-red-50 border border-red-200 rounded-xl p-3 text-sm text-red-700 text-center font-medium"></div>
            </div>

            {{-- Step 2: Ticket Found --}}
            <div id="panel-ticket" class="hidden">
                <div class="bg-gradient-to-r from-brown-800 to-brown-900 text-white p-5">
                    <p class="text-brown-300 text-xs font-semibold uppercase tracking-wider">Tiket Ditemukan</p>
                    <h2 id="t-film" class="font-display text-lg font-bold mt-1"></h2>
                </div>
                <div class="p-5 space-y-3 text-sm border-b border-stone-100">
                    <div class="flex justify-between"><span class="text-stone-400">Bioskop</span><span id="t-cinema" class="font-semibold text-right"></span></div>
                    <div class="flex justify-between"><span class="text-stone-400">Tanggal</span><span id="t-date" class="font-semibold"></span></div>
                    <div class="flex justify-between"><span class="text-stone-400">Jam</span><span id="t-time" class="font-semibold"></span></div>
                    <div class="flex justify-between"><span class="text-stone-400">Studio</span><span id="t-studio" class="font-semibold"></span></div>
                    <div class="flex justify-between"><span class="text-stone-400">Kursi</span><span id="t-seats" class="font-bold text-brown-700"></span></div>
                    <div class="flex justify-between"><span class="text-stone-400">Jumlah</span><span id="t-qty" class="font-semibold"></span></div>
                    <div class="flex justify-between"><span class="text-stone-400">Total</span><span id="t-total" class="font-bold text-brown-700"></span></div>
                </div>
                <div class="p-5">
                    <div id="already-redeemed" class="hidden bg-amber-50 border border-amber-300 rounded-2xl p-4 text-center mb-4">
                        <div class="text-2xl mb-1">⚠️</div>
                        <p class="font-bold text-amber-700 text-sm">Tiket Sudah Diredeem</p>
                        <p id="redeemed-time" class="text-amber-600 text-xs mt-1"></p>
                    </div>
                    <div id="redeem-actions" class="space-y-2">
                        <button onclick="confirmRedeem()" id="redeem-btn"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl transition-all text-base shadow-md">
                            ✅ Redeem Tiket Sekarang
                        </button>
                        <button onclick="resetPanel()"
                            class="w-full border-2 border-stone-200 text-stone-600 font-semibold py-3 rounded-xl transition-all text-sm hover:bg-stone-50">
                            ← Kembali
                        </button>
                    </div>
                </div>
            </div>

            {{-- Step 3: Success --}}
            <div id="panel-success" class="hidden p-8 text-center">
                <div class="text-6xl mb-4 animate-bounce">🎉</div>
                <h2 class="font-display text-2xl font-bold text-green-700 mb-2">Berhasil!</h2>
                <p class="text-stone-500 text-sm mb-2">Tiket telah berhasil diredeem.</p>
                <p class="text-stone-400 text-xs mb-6">Selamat menikmati filmnya!</p>
                <button onclick="resetPanel()" class="w-full bg-brown-700 hover:bg-brown-800 text-white font-bold py-4 rounded-xl text-base">
                    Redeem Tiket Lain
                </button>
            </div>
        </div>

        <p class="text-center text-brown-400 text-xs mt-4">TIKS Cinema Kiosk · Kontak Admin jika ada masalah</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentFocus = 'phone'; // 'phone' or 'code'
let currentBookingCode = null;

// Numpad focus detection
document.getElementById('inp-phone').addEventListener('focus', () => currentFocus = 'phone');
document.getElementById('inp-code').addEventListener('focus', () => currentFocus = 'code');

function numpadPress(key) {
    const field = document.getElementById(currentFocus === 'phone' ? 'inp-phone' : 'inp-code');
    if (key === '⌫') {
        field.value = field.value.slice(0, -1);
    } else if (key === '*') {
        // Switch focus between fields
        currentFocus = currentFocus === 'phone' ? 'code' : 'phone';
        document.getElementById(currentFocus === 'phone' ? 'inp-phone' : 'inp-code').focus();
        return;
    } else {
        // Kode booking: max 5 digit angka saja
        if (currentFocus === 'code' && field.value.length >= 5) return;
        field.value += key;
    }
}

async function checkTicket() {
    const phone = document.getElementById('inp-phone').value.trim();
    const code  = document.getElementById('inp-code').value.trim();

    if (!phone || !code) {
        showError('Nomor HP dan kode booking wajib diisi.');
        return;
    }

    const btn = document.getElementById('check-btn');
    const spinner = document.getElementById('check-spinner');
    const btnText = document.getElementById('check-btn-text');
    btn.disabled = true;
    spinner.classList.remove('hidden');
    btnText.textContent = 'Mencari...';
    hideError();

    try {
        const res = await fetch('{{ route("redeem.check") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ phone, booking_code: code })
        });
        const data = await res.json();

        if (!data.success && !data.already) {
            showError(data.message || 'Tiket tidak ditemukan.');
        } else {
            const b = data.booking;
            currentBookingCode = b.booking_code;
            document.getElementById('t-film').textContent = b.film;
            document.getElementById('t-cinema').textContent = b.cinema;
            document.getElementById('t-date').textContent = b.date;
            document.getElementById('t-time').textContent = b.time + ' WIB';
            document.getElementById('t-studio').textContent = b.studio;
            document.getElementById('t-seats').textContent = b.seats;
            document.getElementById('t-qty').textContent = b.qty + ' tiket';
            document.getElementById('t-total').textContent = b.total;

            if (data.already || b.is_redeemed) {
                document.getElementById('already-redeemed').classList.remove('hidden');
                document.getElementById('redeem-btn').classList.add('hidden');
                if (data.message) document.getElementById('redeemed-time').textContent = data.message;
            } else {
                document.getElementById('already-redeemed').classList.add('hidden');
                document.getElementById('redeem-btn').classList.remove('hidden');
            }

            document.getElementById('panel-input').classList.add('hidden');
            document.getElementById('panel-ticket').classList.remove('hidden');
        }
    } catch(e) {
        showError('Terjadi kesalahan. Coba lagi.');
    }

    btn.disabled = false;
    spinner.classList.add('hidden');
    btnText.textContent = '🔍 Cari Tiket';
}

function confirmRedeem() {
    Swal.fire({
        title: 'Konfirmasi Redeem',
        text: 'Tiket akan ditandai sebagai sudah digunakan dan tidak dapat diredeem lagi.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#78716c',
        confirmButtonText: '✅ Ya, Redeem!',
        cancelButtonText: 'Batal',
    }).then(async (result) => {
        if (!result.isConfirmed) return;

        const res = await fetch('{{ route("redeem.confirm") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ booking_code: currentBookingCode })
        });
        const data = await res.json();

        if (data.success) {
            document.getElementById('panel-ticket').classList.add('hidden');
            document.getElementById('panel-success').classList.remove('hidden');
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
        }
    });
}

function resetPanel() {
    document.getElementById('inp-phone').value = '';
    document.getElementById('inp-code').value = '';
    document.getElementById('panel-ticket').classList.add('hidden');
    document.getElementById('panel-success').classList.add('hidden');
    document.getElementById('panel-input').classList.remove('hidden');
    currentBookingCode = null;
    hideError();
}

function showError(msg) {
    const el = document.getElementById('error-msg');
    el.textContent = msg;
    el.classList.remove('hidden');
}
function hideError() {
    document.getElementById('error-msg').classList.add('hidden');
}
</script>
@endpush

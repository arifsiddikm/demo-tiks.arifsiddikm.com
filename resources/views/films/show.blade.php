@extends('layouts.app')
@section('title', $film->title . ' - TIKS')
@section('meta_description', Str::limit($film->synopsis, 160))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    {{-- FILM HERO --}}
    <div class="flex flex-col md:flex-row gap-8 mb-10">
        {{-- Poster --}}
        <div class="flex-shrink-0">
            <div class="w-48 md:w-64 mx-auto md:mx-0 rounded-2xl overflow-hidden shadow-xl aspect-[2/3]">
                <img src="{{ $film->poster_url }}" alt="{{ $film->title }}" class="w-full h-full object-cover"
                     onerror="this.onerror=null;this.src='{{ asset('images/default-poster.jpg') }}'">
            </div>
        </div>
        {{-- Info --}}
        <div class="flex-1">
            <div class="flex flex-wrap gap-2 mb-3">
                <span class="bg-brown-100 text-brown-700 text-xs font-bold px-3 py-1 rounded-full">{{ $film->rating }}</span>
                @foreach($film->genres as $genre)
                <span class="bg-stone-100 text-stone-600 text-xs font-semibold px-3 py-1 rounded-full">{{ $genre->name }}</span>
                @endforeach
                <span class="bg-amber-50 text-amber-700 text-xs font-semibold px-3 py-1 rounded-full">{{ $film->film_type ?? '2D' }}</span>
            </div>
            <h1 class="font-display text-3xl md:text-4xl font-bold text-stone-800 mb-2">{{ $film->title }}</h1>
            <div class="flex flex-wrap gap-4 text-sm text-stone-500 mb-4">
                <span class="flex items-center gap-1.5">⏱️ {{ $film->duration }}</span>
                <span class="flex items-center gap-1.5">🎬 {{ $film->language }}</span>
                <span class="flex items-center gap-1.5">📅 {{ $film->release_date ? \Carbon\Carbon::parse($film->release_date)->format('d M Y') : '-' }}</span>
            </div>

            @if($film->director)
            <div class="mb-2 text-sm"><span class="font-semibold text-stone-700">Sutradara:</span> <span class="text-stone-600">{{ $film->director }}</span></div>
            @endif

            @if($film->cast)
            <div class="mb-4 text-sm"><span class="font-semibold text-stone-700">Pemeran:</span>
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach($film->cast_array as $actor)
                    <span class="bg-stone-100 text-stone-600 text-xs px-2.5 py-1 rounded-full">{{ $actor }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($film->trailer_url)
            <a href="{{ $film->trailer_url }}" target="_blank"
               class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-sm">
                ▶️ Tonton Trailer
            </a>
            @endif
        </div>
    </div>

    {{-- TABS --}}
    <div class="border-b border-stone-200 mb-6">
        <div class="flex gap-0">
            <button onclick="switchTab('synopsis')" id="tab-synopsis"
                class="tab-btn px-6 py-3 text-sm font-semibold border-b-2 border-brown-700 text-brown-700 transition-all">
                Sinopsis
            </button>
            <button onclick="switchTab('schedule')" id="tab-schedule"
                class="tab-btn px-6 py-3 text-sm font-semibold border-b-2 border-transparent text-stone-500 hover:text-stone-700 transition-all">
                Jadwal Tayang
            </button>
        </div>
    </div>

    {{-- SYNOPSIS TAB --}}
    <div id="panel-synopsis">
        <div class="max-w-3xl">
            <p class="text-stone-600 leading-relaxed text-base">{{ $film->synopsis }}</p>
        </div>
    </div>

    {{-- SCHEDULE TAB --}}
    <div id="panel-schedule" class="hidden">
        {{-- City + Date Picker --}}
        <div class="bg-white rounded-2xl border border-stone-200 p-5 mb-6">
            <div class="flex flex-col sm:flex-row gap-4">
                {{-- City selector --}}
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-stone-500 mb-2 uppercase tracking-wider">📍 Kota</label>
                    <select id="city-select" class="w-full border border-stone-200 rounded-xl px-4 py-2.5 text-sm text-stone-700 bg-white focus:outline-none focus:ring-2 focus:ring-brown-400 focus:border-transparent transition-all">
                        @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ $city->id === $selectedCity->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Date picker --}}
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-stone-500 mb-2 uppercase tracking-wider">📅 Tanggal</label>
                    <div class="flex gap-2 overflow-x-auto scrollbar-hide pb-1">
                        @foreach($dates as $date)
                        <button onclick="selectDate('{{ $date->format('Y-m-d') }}', this)"
                            class="date-btn flex-shrink-0 flex flex-col items-center px-3 py-2 rounded-xl border-2 transition-all min-w-14 {{ $date->format('Y-m-d') === $selectedDate ? 'border-brown-700 bg-brown-700 text-white' : 'border-stone-200 hover:border-brown-300 text-stone-700' }}">
                            <span class="text-xs font-medium">{{ $date->isoFormat('ddd') }}</span>
                            <span class="text-lg font-bold leading-tight">{{ $date->format('d') }}</span>
                            <span class="text-xs">{{ $date->format('M') }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Schedule Results --}}
        <div id="schedule-results">
            @if($schedules->isEmpty())
            <div class="text-center py-12 text-stone-400">
                <div class="text-5xl mb-3">🎭</div>
                <p class="font-medium">Tidak ada jadwal untuk kota & tanggal ini.</p>
                <p class="text-sm">Coba pilih kota atau tanggal lain.</p>
            </div>
            @else
            @foreach($schedules as $cinemaId => $cinemaSchedules)
                @include('films.partials.schedule-group', ['cinema' => $cinemaSchedules->first()->cinema, 'cinemaSchedules' => $cinemaSchedules])
            @endforeach
            @endif
        </div>
    </div>
</div>

{{-- SEAT SELECTION MODAL --}}
<div id="seat-modal" class="hidden fixed inset-0 z-[200] flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeSeatModal()"></div>
    <div class="relative bg-white w-full sm:max-w-2xl max-h-[92vh] overflow-y-auto rounded-t-3xl sm:rounded-3xl shadow-2xl z-10">
        {{-- Handle --}}
        <div class="sm:hidden flex justify-center pt-3 pb-1">
            <div class="w-10 h-1 bg-stone-200 rounded-full"></div>
        </div>
        <div class="p-5">
            {{-- Schedule Info --}}
            <div id="seat-info" class="bg-brown-50 rounded-2xl p-4 mb-5">
                <div id="seat-info-content" class="text-sm text-stone-600">Loading...</div>
            </div>

            {{-- Seat Legend --}}
            <div class="flex gap-4 mb-5 text-xs font-medium">
                <div class="flex items-center gap-1.5"><div class="w-5 h-5 bg-stone-100 border-2 border-stone-300 rounded-md"></div>Tersedia</div>
                <div class="flex items-center gap-1.5"><div class="w-5 h-5 bg-brown-600 rounded-md"></div>Dipilih</div>
                <div class="flex items-center gap-1.5"><div class="w-5 h-5 bg-stone-300 rounded-md opacity-60"></div>Terpesan</div>
            </div>

            {{-- Screen --}}
            <div class="bg-gradient-to-b from-amber-200 to-amber-50 rounded-xl py-2 text-center text-xs font-semibold text-brown-700 mb-5 tracking-widest">▬▬ LAYAR ▬▬</div>

            {{-- Seats Grid --}}
            <div id="seats-grid" class="mb-5">
                <div class="text-center text-stone-400 py-8">Memuat denah kursi...</div>
            </div>

            {{-- Selection Summary --}}
            <div id="seat-summary" class="bg-brown-50 rounded-2xl p-4 mb-4">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-stone-600">Kursi dipilih: <span id="selected-count" class="font-bold text-brown-700">0</span></span>
                    <span class="font-bold text-stone-800 text-base" id="total-price">Rp 0</span>
                </div>
                <div id="selected-codes" class="text-xs text-stone-500 mt-1"></div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <button onclick="closeSeatModal()" class="flex-1 border-2 border-stone-200 text-stone-600 font-semibold py-3 rounded-xl hover:bg-stone-50 transition-colors text-sm">Batal</button>
                <button onclick="proceedCheckout()" id="checkout-btn" disabled
                    class="flex-2 bg-brown-700 text-white font-bold py-3 px-8 rounded-xl hover:bg-brown-800 transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed flex-1">
                    Lanjut Checkout →
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let selectedScheduleId = null;
let selectedSeats = [];
let schedulePrice = 0;
let currentCityId = {{ $selectedCity->id }};
let currentDate = '{{ $selectedDate }}';
const filmId = {{ $film->id }};

// TABS
function switchTab(tab) {
    ['synopsis', 'schedule'].forEach(t => {
        document.getElementById('panel-' + t).classList.toggle('hidden', t !== tab);
        const btn = document.getElementById('tab-' + t);
        btn.classList.toggle('border-brown-700', t === tab);
        btn.classList.toggle('text-brown-700', t === tab);
        btn.classList.toggle('border-transparent', t !== tab);
        btn.classList.toggle('text-stone-500', t !== tab);
    });
}

// DATE SELECT
function selectDate(date, el) {
    currentDate = date;
    document.querySelectorAll('.date-btn').forEach(b => {
        b.classList.remove('border-brown-700', 'bg-brown-700', 'text-white');
        b.classList.add('border-stone-200', 'text-stone-700');
    });
    el.classList.add('border-brown-700', 'bg-brown-700', 'text-white');
    el.classList.remove('border-stone-200', 'text-stone-700');
    loadSchedules();
}

// CITY SELECT
document.getElementById('city-select').addEventListener('change', function() {
    currentCityId = this.value;
    loadSchedules();
});

function loadSchedules() {
    const container = document.getElementById('schedule-results');
    container.innerHTML = '<div class="text-center py-12"><div class="w-8 h-8 border-4 border-brown-200 border-t-brown-700 rounded-full animate-spin mx-auto"></div></div>';

    fetch(`{{ route('films.schedules') }}?film_id=${filmId}&city_id=${currentCityId}&date=${currentDate}`)
        .then(r => r.json())
        .then(data => { container.innerHTML = data.html; })
        .catch(() => { container.innerHTML = '<p class="text-center text-stone-400 py-8">Gagal memuat jadwal.</p>'; });
}

// OPEN SEAT MODAL
function openSeatModal(scheduleId) {
    @guest
    Swal.fire({
        title: '⚠️ Login Dulu',
        text: 'Kamu harus login untuk membeli tiket.',
        icon: 'info',
        confirmButtonColor: '#7c4b18',
        confirmButtonText: 'Masuk Sekarang',
        showCancelButton: true,
        cancelButtonText: 'Batal',
    }).then(r => {
        if (r.isConfirmed) {
            sessionStorage.setItem('intended_url', window.location.href);
            window.location = '{{ route("login") }}';
        }
    });
    return;
    @endguest

    selectedScheduleId = scheduleId;
    selectedSeats = [];
    document.getElementById('seat-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    loadSeats(scheduleId);
}

function closeSeatModal() {
    document.getElementById('seat-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

function loadSeats(scheduleId) {
    document.getElementById('seats-grid').innerHTML = '<div class="text-center text-stone-400 py-8">Memuat denah kursi...</div>';

    fetch(`/api/seats/${scheduleId}`)
        .then(r => r.json())
        .then(data => {
            schedulePrice = data.schedule.price;

            // Info box
            document.getElementById('seat-info-content').innerHTML = `
                <div class="font-semibold text-stone-800 mb-1">🎬 ${data.schedule.film}</div>
                <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs">
                    <span>📍 ${data.schedule.cinema}</span>
                    <span>📅 ${data.schedule.date}</span>
                    <span>⏰ ${data.schedule.time}</span>
                    <span>🎭 ${data.schedule.studio} · ${data.schedule.film_type}</span>
                    <span class="font-bold text-brown-700">💰 ${data.schedule.price_fmt}/kursi</span>
                </div>`;

            // Build seat grid
            let html = '';
            data.rows.forEach(row => {
                html += `<div class="flex items-center gap-1 mb-1.5">
                    <span class="w-6 text-center text-xs font-bold text-stone-400">${row}</span>
                    <div class="flex gap-1 flex-wrap">`;
                data.cols.forEach(col => {
                    const code = row + col;
                    const booked = data.booked_seats.includes(code);
                    if (booked) {
                        html += `<div class="seat-btn w-8 h-8 bg-stone-200 rounded-md flex items-center justify-center text-xs text-stone-400 opacity-60 cursor-not-allowed" title="${code} - Terpesan">${col}</div>`;
                    } else {
                        html += `<button class="seat-btn w-8 h-8 bg-stone-100 border-2 border-stone-300 rounded-md flex items-center justify-center text-xs font-medium text-stone-600 hover:bg-brown-100 hover:border-brown-500 transition-all" onclick="toggleSeat('${code}', this)" data-code="${code}" title="${code}">${col}</button>`;
                    }
                });
                html += `</div></div>`;
            });
            document.getElementById('seats-grid').innerHTML = html;
            updateSummary();
        });
}

function toggleSeat(code, el) {
    const idx = selectedSeats.indexOf(code);
    if (idx > -1) {
        selectedSeats.splice(idx, 1);
        el.classList.remove('bg-brown-600', 'border-brown-700', 'text-white');
        el.classList.add('bg-stone-100', 'border-stone-300', 'text-stone-600');
    } else {
        if (selectedSeats.length >= 6) {
            Swal.fire({ icon: 'warning', title: 'Batas Maksimal', text: 'Maksimal 6 kursi dalam satu transaksi.', timer: 2000, showConfirmButton: false });
            return;
        }
        selectedSeats.push(code);
        el.classList.add('bg-brown-600', 'border-brown-700', 'text-white');
        el.classList.remove('bg-stone-100', 'border-stone-300', 'text-stone-600');
    }
    updateSummary();
}

function updateSummary() {
    const total = selectedSeats.length * schedulePrice;
    document.getElementById('selected-count').textContent = selectedSeats.length;
    document.getElementById('total-price').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('selected-codes').textContent = selectedSeats.length > 0 ? 'Kursi: ' + selectedSeats.join(', ') : '';
    document.getElementById('checkout-btn').disabled = selectedSeats.length === 0;
}

function proceedCheckout() {
    if (selectedSeats.length === 0) return;

    Swal.fire({
        title: 'Konfirmasi Pembelian',
        html: `<div class="text-left">
            <p class="text-sm text-stone-600 mb-3">Kamu akan memesan:</p>
            <div class="bg-brown-50 rounded-xl p-3 text-sm">
                <div class="font-bold text-brown-800 mb-1">Kursi: ${selectedSeats.join(', ')}</div>
                <div class="text-stone-600">${selectedSeats.length} tiket × Rp ${schedulePrice.toLocaleString('id-ID')}</div>
                <div class="font-bold text-brown-700 mt-2 text-lg">Total: Rp ${(selectedSeats.length * schedulePrice).toLocaleString('id-ID')}</div>
            </div>
        </div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#7c4b18',
        cancelButtonColor: '#78716c',
        confirmButtonText: 'Lanjut Bayar →',
        cancelButtonText: 'Batal',
    }).then(r => {
        if (!r.isConfirmed) return;

        const btn = document.getElementById('checkout-btn');
        btn.disabled = true;
        btn.textContent = 'Memproses...';

        fetch('{{ route("checkout.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ schedule_id: selectedScheduleId, seats: selectedSeats })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.location = data.redirect_to;
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
                btn.disabled = false;
                btn.textContent = 'Lanjut Checkout →';
            }
        })
        .catch(() => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan. Coba lagi.' });
            btn.disabled = false;
            btn.textContent = 'Lanjut Checkout →';
        });
    });
}
</script>
@endpush

@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
    $stats = [
        ['label' => 'Total Pendapatan', 'value' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'), 'icon' => '💰', 'sub' => 'Hari ini: Rp ' . number_format($todayRevenue, 0, ',', '.'), 'color' => 'bg-green-50 border-green-200', 'text' => 'text-green-700'],
        ['label' => 'Total Transaksi', 'value' => number_format($totalBookings), 'icon' => '🎟️', 'sub' => "Lunas: {$paidBookings}", 'color' => 'bg-blue-50 border-blue-200', 'text' => 'text-blue-700'],
        ['label' => 'Pengguna Aktif', 'value' => number_format($totalUsers), 'icon' => '👥', 'sub' => 'Transaksi hari ini: ' . $todayBookings, 'color' => 'bg-purple-50 border-purple-200', 'text' => 'text-purple-700'],
        ['label' => 'Menunggu Bayar', 'value' => number_format($pendingBookings), 'icon' => '⏳', 'sub' => 'Film aktif: ' . $totalFilms, 'color' => 'bg-amber-50 border-amber-200', 'text' => 'text-amber-700'],
    ];
    @endphp

    @foreach($stats as $stat)
    <div class="bg-white rounded-2xl border {{ $stat['color'] }} p-5 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <p class="text-sm font-medium text-stone-500">{{ $stat['label'] }}</p>
            <span class="text-2xl">{{ $stat['icon'] }}</span>
        </div>
        <p class="text-2xl font-bold {{ $stat['text'] }} mb-1">{{ $stat['value'] }}</p>
        <p class="text-xs text-stone-400">{{ $stat['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- Chart + Top Films --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Revenue Chart --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-stone-200 p-6 shadow-sm">
        <h3 class="font-bold text-stone-800 mb-4">📈 Pendapatan 14 Hari Terakhir</h3>
        <canvas id="revenueChart" height="100"></canvas>
    </div>

    {{-- Top Films --}}
    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm">
        <h3 class="font-bold text-stone-800 mb-4">🎬 Film Terpopuler</h3>
        <div class="space-y-3">
            @foreach($topFilms as $i => $film)
            <div class="flex items-center gap-3">
                <span class="w-6 h-6 bg-brown-100 text-brown-700 text-xs font-bold rounded-lg flex items-center justify-center">{{ $i + 1 }}</span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-stone-800 truncate">{{ $film->title }}</p>
                    <p class="text-xs text-stone-400">{{ $film->booking_count ?? 0 }} tiket terjual</p>
                </div>
            </div>
            @endforeach
            @if($topFilms->isEmpty())
            <p class="text-sm text-stone-400 text-center py-4">Belum ada data</p>
            @endif
        </div>
    </div>
</div>

{{-- Recent Orders --}}
<div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-stone-100">
        <h3 class="font-bold text-stone-800">🧾 Transaksi Terbaru</h3>
        <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-brown-700 hover:text-brown-900">Lihat Semua →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-stone-50 text-xs font-semibold text-stone-500 uppercase tracking-wider">
                    <th class="text-left px-6 py-3">Kode Booking</th>
                    <th class="text-left px-6 py-3">Pelanggan</th>
                    <th class="text-left px-6 py-3">Film</th>
                    <th class="text-left px-6 py-3">Total</th>
                    <th class="text-left px-6 py-3">Status</th>
                    <th class="text-left px-6 py-3">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @foreach($recentBookings as $booking)
                <tr class="hover:bg-stone-50 transition-colors">
                    <td class="px-6 py-3 font-mono text-xs font-bold text-brown-700">
                        <a href="{{ route('admin.orders.show', $booking) }}" class="hover:underline">{{ $booking->booking_code }}</a>
                    </td>
                    <td class="px-6 py-3">
                        <p class="font-medium text-stone-800">{{ $booking->user->name }}</p>
                        <p class="text-xs text-stone-400">{{ $booking->user->phone }}</p>
                    </td>
                    <td class="px-6 py-3 text-stone-600 max-w-36 truncate">{{ $booking->schedule->film->title }}</td>
                    <td class="px-6 py-3 font-semibold text-stone-800">{{ $booking->formatted_total }}</td>
                    <td class="px-6 py-3">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full
                            {{ $booking->status === 'paid' ? 'bg-green-100 text-green-700' :
                               ($booking->status === 'waiting_payment' ? 'bg-amber-100 text-amber-700' :
                               ($booking->status === 'pending' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-600')) }}">
                            {{ $booking->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-stone-400 text-xs">{{ $booking->created_at->format('d M H:i') }}</td>
                </tr>
                @endforeach
                @if($recentBookings->isEmpty())
                <tr><td colspan="6" class="px-6 py-10 text-center text-stone-400">Belum ada transaksi</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
const ctx = document.getElementById('revenueChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: {!! json_encode($chartData) !!},
            borderColor: '#7c4b18',
            backgroundColor: 'rgba(124, 75, 24, 0.08)',
            borderWidth: 2.5,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#7c4b18',
            pointRadius: 4,
            pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: {
                    callback: v => 'Rp ' + (v / 1000).toFixed(0) + 'rb',
                    font: { size: 11 }
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 } }
            }
        }
    }
});
</script>
@endpush

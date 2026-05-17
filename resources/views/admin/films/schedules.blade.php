@extends('layouts.admin')
@section('title', 'Jadwal: ' . $film->title)
@section('page-title', 'Jadwal: ' . $film->title)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Add Schedule Form --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-5 sticky top-4">
            <h3 class="font-bold text-stone-800 mb-4">➕ Tambah Jadwal</h3>
            <form action="{{ route('admin.films.schedules.store', $film) }}" method="POST" class="space-y-4">
                @csrf
                @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-sm text-red-700">
                    @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
                </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1.5 uppercase tracking-wider">Bioskop</label>
                    <select name="cinema_id" required class="w-full border-2 border-stone-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-brown-500 bg-white">
                        <option value="">-- Pilih Bioskop --</option>
                        @foreach($cinemas as $cinema)
                        <option value="{{ $cinema->id }}">{{ $cinema->name }} ({{ $cinema->city->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1.5 uppercase tracking-wider">Tanggal Tayang</label>
                    <input type="date" name="show_date" min="{{ today()->format('Y-m-d') }}" required
                        class="w-full border-2 border-stone-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-brown-500 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1.5 uppercase tracking-wider">Jam Tayang</label>
                    <input type="time" name="show_time" required
                        class="w-full border-2 border-stone-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-brown-500 bg-white">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-stone-500 mb-1.5 uppercase tracking-wider">Studio</label>
                        <select name="studio" class="w-full border-2 border-stone-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-brown-500 bg-white">
                            @foreach(['Studio 1','Studio 2','Studio 3','Studio 4','Studio IMAX','Studio 4DX'] as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-stone-500 mb-1.5 uppercase tracking-wider">Tipe</label>
                        <select name="film_type" class="w-full border-2 border-stone-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-brown-500 bg-white">
                            @foreach(['2D','3D','IMAX','4DX'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1.5 uppercase tracking-wider">Harga Tiket (Rp)</label>
                    <input type="number" name="price" min="10000" step="1000" placeholder="50000" required
                        class="w-full border-2 border-stone-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-brown-500 bg-white">
                </div>

                <button type="submit" class="w-full bg-brown-700 hover:bg-brown-800 text-white font-bold py-3 rounded-xl text-sm transition-colors">
                    ✅ Tambah Jadwal
                </button>
            </form>
        </div>
    </div>

    {{-- Schedules List --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-stone-100 flex items-center justify-between">
                <h3 class="font-bold text-stone-800">Daftar Jadwal ({{ $schedules->total() }})</h3>
                <a href="{{ route('admin.films.index') }}" class="text-xs text-brown-700 font-semibold hover:text-brown-900">← Kembali ke Film</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-stone-50 text-xs font-semibold text-stone-500 uppercase tracking-wider">
                            <th class="text-left px-5 py-3">Bioskop</th>
                            <th class="text-left px-5 py-3">Tanggal & Jam</th>
                            <th class="text-left px-5 py-3">Studio</th>
                            <th class="text-left px-5 py-3">Harga</th>
                            <th class="text-left px-5 py-3">Kursi</th>
                            <th class="text-left px-5 py-3">Hapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($schedules as $schedule)
                        <tr class="hover:bg-stone-50 transition-colors">
                            <td class="px-5 py-3">
                                <p class="font-medium text-stone-800 text-xs">{{ $schedule->cinema->name }}</p>
                                <p class="text-xs text-stone-400">{{ $schedule->cinema->city->name }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-semibold text-stone-800">{{ $schedule->show_date->format('d M Y') }}</p>
                                <p class="text-xs text-stone-500">{{ $schedule->formatted_time }} WIB</p>
                            </td>
                            <td class="px-5 py-3 text-xs text-stone-600">
                                {{ $schedule->studio }}<br>
                                <span class="font-semibold text-brown-600">{{ $schedule->film_type }}</span>
                            </td>
                            <td class="px-5 py-3 font-semibold text-stone-800 text-xs">{{ $schedule->formatted_price }}</td>
                            <td class="px-5 py-3">
                                <span class="text-xs {{ $schedule->available_seats > 20 ? 'text-green-600' : ($schedule->available_seats > 0 ? 'text-amber-600' : 'text-red-500') }} font-semibold">
                                    {{ $schedule->available_seats }}/{{ $schedule->total_seats }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <form id="del-sched-{{ $schedule->id }}" action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete('del-sched-{{ $schedule->id }}', 'Hapus jadwal ini?')"
                                        class="text-xs text-red-500 hover:text-red-700 font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @if($schedules->isEmpty())
                        <tr><td colspan="6" class="px-5 py-8 text-center text-stone-400">Belum ada jadwal untuk film ini.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3">{{ $schedules->links() }}</div>
        </div>
    </div>
</div>
@endsection

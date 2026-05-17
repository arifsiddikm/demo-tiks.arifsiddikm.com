@extends('layouts.admin')
@section('title', 'Kelola Film')
@section('page-title', 'Kelola Film')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex gap-3">
        <form action="{{ route('admin.films.index') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul film..."
                class="border border-stone-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-brown-500 focus:ring-2 focus:ring-brown-100 bg-white w-56">
            <select name="status" class="border border-stone-200 rounded-xl px-3 py-2 text-sm bg-white focus:outline-none focus:border-brown-500">
                <option value="">Semua Status</option>
                <option value="now_showing" {{ request('status')=='now_showing'?'selected':'' }}>Sedang Tayang</option>
                <option value="coming_soon" {{ request('status')=='coming_soon'?'selected':'' }}>Segera Tayang</option>
                <option value="ended" {{ request('status')=='ended'?'selected':'' }}>Selesai</option>
            </select>
            <button type="submit" class="bg-stone-700 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-stone-800 transition-colors">Cari</button>
        </form>
    </div>
    <a href="{{ route('admin.films.create') }}" class="bg-brown-700 hover:bg-brown-800 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Film
    </a>
</div>

<div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-stone-50 text-xs font-semibold text-stone-500 uppercase tracking-wider">
                <th class="text-left px-6 py-3">Film</th>
                <th class="text-left px-6 py-3 hidden md:table-cell">Genre</th>
                <th class="text-left px-6 py-3 hidden lg:table-cell">Durasi</th>
                <th class="text-left px-6 py-3">Status</th>
                <th class="text-left px-6 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @foreach($films as $film)
            <tr class="hover:bg-stone-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ $film->poster_url }}" alt="" loading="lazy" class="w-10 h-14 object-cover rounded-lg shadow-sm flex-shrink-0"
                             onerror="this.onerror=null;this.src='{{ asset('images/default-poster.jpg') }}'">
                        <div>
                            <p class="font-semibold text-stone-800 line-clamp-1">{{ $film->title }}</p>
                            <p class="text-xs text-stone-400">{{ $film->rating }} · {{ $film->language }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 hidden md:table-cell">
                    <div class="flex flex-wrap gap-1">
                        @foreach($film->genres->take(2) as $g)
                        <span class="bg-brown-50 text-brown-700 text-xs px-2 py-0.5 rounded-full">{{ $g->name }}</span>
                        @endforeach
                    </div>
                </td>
                <td class="px-6 py-4 hidden lg:table-cell text-stone-500">{{ $film->duration }}</td>
                <td class="px-6 py-4">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full
                        {{ $film->status === 'now_showing' ? 'bg-green-100 text-green-700' : ($film->status === 'coming_soon' ? 'bg-blue-100 text-blue-700' : 'bg-stone-100 text-stone-500') }}">
                        {{ $film->status === 'now_showing' ? 'Sedang Tayang' : ($film->status === 'coming_soon' ? 'Segera' : 'Selesai') }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.films.schedules', $film) }}" title="Jadwal"
                           class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </a>
                        <a href="{{ route('admin.films.edit', $film) }}" title="Edit"
                           class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form id="del-film-{{ $film->id }}" action="{{ route('admin.films.destroy', $film) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDelete('del-film-{{ $film->id }}', 'Film &quot;{{ addslashes($film->title) }}&quot; akan dihapus.')"
                                class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
            @if($films->isEmpty())
            <tr><td colspan="5" class="px-6 py-10 text-center text-stone-400">Belum ada film. <a href="{{ route('admin.films.create') }}" class="text-brown-700 font-semibold">Tambah sekarang</a></td></tr>
            @endif
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $films->links() }}</div>
@endsection

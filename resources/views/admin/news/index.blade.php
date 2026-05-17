@extends('layouts.admin')
@section('title', 'TIKS News')
@section('page-title', 'TIKS News')

@section('content')
<div class="flex items-center justify-between mb-6">
    <form action="{{ route('admin.news.index') }}" method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul berita..."
            class="border border-stone-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-brown-500 bg-white w-60">
        <button type="submit" class="bg-stone-700 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-stone-800">Cari</button>
    </form>
    <a href="{{ route('admin.news.create') }}" class="bg-brown-700 hover:bg-brown-800 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tulis Berita
    </a>
</div>

<div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-stone-50 text-xs font-semibold text-stone-500 uppercase tracking-wider">
                <th class="text-left px-6 py-3">Judul</th>
                <th class="text-left px-6 py-3 hidden md:table-cell">Kategori</th>
                <th class="text-left px-6 py-3 hidden lg:table-cell">Penulis</th>
                <th class="text-left px-6 py-3">Status</th>
                <th class="text-left px-6 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @foreach($news as $article)
            <tr class="hover:bg-stone-50 transition-colors">
                <td class="px-6 py-4">
                    <p class="font-semibold text-stone-800 line-clamp-1">{{ $article->title }}</p>
                    <p class="text-xs text-stone-400 mt-0.5">{{ $article->published_at?->format('d M Y') ?? '-' }}</p>
                </td>
                <td class="px-6 py-4 hidden md:table-cell">
                    <span class="text-xs bg-brown-50 text-brown-700 px-2.5 py-1 rounded-full font-semibold">{{ $article->category }}</span>
                </td>
                <td class="px-6 py-4 hidden lg:table-cell text-stone-500">{{ $article->author->name }}</td>
                <td class="px-6 py-4">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $article->is_published ? 'bg-green-100 text-green-700' : 'bg-stone-100 text-stone-500' }}">
                        {{ $article->is_published ? 'Publish' : 'Draft' }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex gap-1.5">
                        <a href="{{ route('admin.news.edit', $article) }}" class="text-xs font-semibold text-amber-600 bg-amber-50 hover:bg-amber-100 px-2.5 py-1.5 rounded-lg transition-colors">Edit</a>
                        <form id="del-news-{{ $article->id }}" action="{{ route('admin.news.destroy', $article) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDelete('del-news-{{ $article->id }}')"
                                class="text-xs font-semibold text-red-500 bg-red-50 hover:bg-red-100 px-2.5 py-1.5 rounded-lg transition-colors">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
            @if($news->isEmpty())
            <tr><td colspan="5" class="px-6 py-10 text-center text-stone-400">Belum ada berita.</td></tr>
            @endif
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $news->links() }}</div>
@endsection

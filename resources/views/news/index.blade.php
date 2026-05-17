@extends('layouts.app')
@section('title', 'TIKS News - Berita Perfilman')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <div class="mb-8">
        <h1 class="font-display text-3xl font-bold text-stone-800">📰 TIKS News</h1>
        <p class="text-stone-500 text-sm mt-1">Berita terkini seputar film, bioskop, dan industri perfilman Indonesia</p>
    </div>

    {{-- Category Filter --}}
    <div class="flex gap-2 flex-wrap mb-8">
        <a href="{{ route('news.index') }}"
           class="px-4 py-2 rounded-full text-sm font-semibold transition-all {{ !$category ? 'bg-brown-700 text-white' : 'bg-stone-100 text-stone-600 hover:bg-brown-100 hover:text-brown-700' }}">
            Semua
        </a>
        @foreach($categories as $cat)
        <a href="{{ route('news.index', ['category' => $cat]) }}"
           class="px-4 py-2 rounded-full text-sm font-semibold transition-all {{ $category === $cat ? 'bg-brown-700 text-white' : 'bg-stone-100 text-stone-600 hover:bg-brown-100 hover:text-brown-700' }}">
            {{ $cat }}
        </a>
        @endforeach
    </div>

    @if($news->isEmpty())
    <div class="text-center py-20 text-stone-400">
        <div class="text-5xl mb-4">📭</div>
        <p class="font-medium">Belum ada berita.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($news as $article)
        <a href="{{ route('news.show', $article->slug) }}"
           class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5 border border-stone-100 flex flex-col">
            <div class="aspect-[16/9] overflow-hidden bg-stone-100 flex-shrink-0">
                <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" loading="lazy"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                     onerror="this.onerror=null;this.src='{{ asset('images/default-news.jpg') }}'">
            </div>
            <div class="p-5 flex flex-col flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs font-semibold text-brown-600 bg-brown-50 px-2.5 py-1 rounded-full">{{ $article->category }}</span>
                    <span class="text-xs text-stone-400">{{ $article->published_at?->diffForHumans() }}</span>
                </div>
                <h3 class="font-bold text-stone-800 line-clamp-2 group-hover:text-brown-700 transition-colors flex-1">{{ $article->title }}</h3>
                @if($article->excerpt)
                <p class="text-sm text-stone-500 mt-2 line-clamp-2">{{ $article->excerpt }}</p>
                @endif
                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-stone-100">
                    <div class="w-6 h-6 bg-brown-100 rounded-full flex items-center justify-center text-xs font-bold text-brown-700">
                        {{ strtoupper(substr($article->author->name, 0, 1)) }}
                    </div>
                    <span class="text-xs text-stone-400">{{ $article->author->name }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    <div class="mt-8">{{ $news->links() }}</div>
    @endif
</div>
@endsection

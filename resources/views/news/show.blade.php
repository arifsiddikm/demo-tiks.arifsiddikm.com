@extends('layouts.app')
@section('title', $article->title . ' - TIKS News')
@section('meta_description', $article->excerpt ?? Str::limit(strip_tags($article->content), 160))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10">
    <div class="mb-4">
        <a href="{{ route('news.index') }}" class="text-sm text-brown-700 hover:text-brown-900 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            TIKS News
        </a>
    </div>

    <article class="bg-white rounded-3xl border border-stone-200 overflow-hidden shadow-sm">
        @if($article->thumbnail)
        <div class="aspect-[21/9] overflow-hidden bg-stone-100">
            <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
        </div>
        @endif
        <div class="p-6 md:p-10">
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="text-xs font-bold text-brown-600 bg-brown-50 px-3 py-1 rounded-full">{{ $article->category }}</span>
                <span class="text-xs text-stone-400">{{ $article->published_at?->isoFormat('D MMMM Y') }}</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-stone-800 mb-4 leading-tight">{{ $article->title }}</h1>
            @if($article->excerpt)
            <p class="text-stone-500 text-base mb-6 leading-relaxed border-l-4 border-brown-300 pl-4">{{ $article->excerpt }}</p>
            @endif
            <div class="prose prose-stone max-w-none prose-headings:font-bold prose-a:text-brown-700 prose-img:rounded-xl">
                {!! $article->content !!}
            </div>
            <div class="flex items-center gap-3 mt-8 pt-6 border-t border-stone-100">
                <div class="w-10 h-10 bg-brown-100 rounded-xl flex items-center justify-center font-bold text-brown-700">
                    {{ strtoupper(substr($article->author->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-stone-800 text-sm">{{ $article->author->name }}</p>
                    <p class="text-xs text-stone-400">{{ $article->published_at?->isoFormat('D MMMM Y, HH:mm') }} WIB</p>
                </div>
            </div>
        </div>
    </article>

    {{-- Related --}}
    @if($related->isNotEmpty())
    <div class="mt-10">
        <h2 class="font-display text-xl font-bold text-stone-800 mb-4">Berita Terkait</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($related as $rel)
            <a href="{{ route('news.show', $rel->slug) }}" class="group bg-white rounded-2xl border border-stone-100 overflow-hidden hover:shadow-md transition-all">
                <div class="aspect-[16/9] overflow-hidden bg-stone-100">
                    <img src="{{ $rel->thumbnail_url }}" alt="{{ $rel->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="p-4">
                    <span class="text-xs text-brown-600 font-semibold">{{ $rel->category }}</span>
                    <h3 class="font-semibold text-stone-800 text-sm mt-1 line-clamp-2 group-hover:text-brown-700 transition-colors">{{ $rel->title }}</h3>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

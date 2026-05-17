@extends('layouts.app')
@section('title', 'TIKS - Beli Tiket Bioskop Online')
@section('meta_description', 'TIKS - Beli tiket bioskop online mudah dan cepat. Film terbaru, pilih kursi, bayar via Midtrans.')

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden bg-gradient-to-br from-brown-900 via-brown-800 to-stone-900 text-white">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-64 h-64 rounded-full bg-brown-400 blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-96 h-96 rounded-full bg-amber-600 blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-16 md:py-24">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 bg-brown-700/50 rounded-full px-4 py-1.5 text-sm text-brown-200 mb-6 border border-brown-600/50">
                <span class="w-2 h-2 bg-amber-400 rounded-full animate-pulse"></span>
                Film terbaru Maret 2026 sudah tayang!
            </div>
            <h1 class="font-display text-4xl md:text-6xl font-bold leading-tight mb-4">
                Pengalaman Bioskop<br>
                <span class="text-amber-400">di Ujung Jarimu</span>
            </h1>
            <p class="text-brown-200 text-lg md:text-xl mb-8 leading-relaxed">
                Pilih film favoritmu, pilih kursi terbaik, dan bayar langsung. Tiket dikirim ke emailmu dalam hitungan detik.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="#now-showing" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-stone-900 font-bold px-6 py-3 rounded-xl transition-all shadow-lg hover:shadow-amber-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.867v6.266a1 1 0 01-1.447.902L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Lihat Film Sekarang
                </a>
                <a href="{{ route('redeem.index') }}" class="inline-flex items-center gap-2 border-2 border-brown-500 hover:border-brown-300 text-white font-semibold px-6 py-3 rounded-xl transition-all">
                    🎟️ Redeem Tiket
                </a>
            </div>

            {{-- Stats --}}
            <div class="flex flex-wrap gap-6 mt-10 pt-8 border-t border-brown-700/50">
                <div><div class="text-2xl font-bold text-amber-400">{{ $nowShowing->count() }}+</div><div class="text-xs text-brown-300 mt-0.5">Film Tayang</div></div>
                <div class="w-px bg-brown-700"></div>
                <div><div class="text-2xl font-bold text-amber-400">9+</div><div class="text-xs text-brown-300 mt-0.5">Bioskop XXI</div></div>
                <div class="w-px bg-brown-700"></div>
                <div><div class="text-2xl font-bold text-amber-400">6+</div><div class="text-xs text-brown-300 mt-0.5">Kota</div></div>
            </div>
        </div>
    </div>
</section>

{{-- NOW SHOWING --}}
<section id="now-showing" class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-display text-2xl md:text-3xl font-bold text-stone-800">Sedang Tayang</h2>
            <p class="text-stone-500 text-sm mt-1">Pilih film dan beli tiketmu sekarang</p>
        </div>
        {{-- Genre filter --}}
        <div class="hidden sm:flex gap-2">
            <button onclick="filterFilms('all')" class="filter-btn active px-3 py-1.5 text-xs font-semibold rounded-full bg-brown-700 text-white transition-all" data-genre="all">Semua</button>
            <button onclick="filterFilms('Horor')" class="filter-btn px-3 py-1.5 text-xs font-semibold rounded-full bg-stone-100 text-stone-600 hover:bg-brown-100 hover:text-brown-700 transition-all" data-genre="Horor">Horor</button>
            <button onclick="filterFilms('Komedi')" class="filter-btn px-3 py-1.5 text-xs font-semibold rounded-full bg-stone-100 text-stone-600 hover:bg-brown-100 hover:text-brown-700 transition-all" data-genre="Komedi">Komedi</button>
            <button onclick="filterFilms('Drama')" class="filter-btn px-3 py-1.5 text-xs font-semibold rounded-full bg-stone-100 text-stone-600 hover:bg-brown-100 hover:text-brown-700 transition-all" data-genre="Drama">Drama</button>
            <button onclick="filterFilms('Aksi')" class="filter-btn px-3 py-1.5 text-xs font-semibold rounded-full bg-stone-100 text-stone-600 hover:bg-brown-100 hover:text-brown-700 transition-all" data-genre="Aksi">Aksi</button>
        </div>
    </div>

    <div id="film-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
        @foreach($nowShowing as $film)
        <div class="film-card group relative bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 cursor-pointer border border-stone-100 hover:-translate-y-1"
             data-genres="{{ $film->genres->pluck('name')->join(',') }}"
             onclick="window.location='{{ route('films.show', $film->slug) }}'">
            {{-- Poster --}}
            <div class="relative aspect-[2/3] overflow-hidden bg-stone-100">
                <img src="{{ $film->poster_url }}"
                     alt="{{ $film->title }}"
                     loading="lazy"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                     onerror="this.onerror=null;this.src='{{ asset('images/default-poster.jpg') }}'">
                {{-- Overlay --}}
                <div class="film-overlay absolute inset-0 bg-gradient-to-t from-brown-900/90 via-brown-900/20 to-transparent flex flex-col justify-end p-3">
                    <a href="{{ route('films.show', $film->slug) }}"
                       class="w-full bg-amber-500 hover:bg-amber-400 text-stone-900 font-bold text-xs py-2.5 rounded-xl text-center transition-colors flex items-center justify-center gap-1.5">
                        🎟️ Beli Tiket
                    </a>
                </div>
                {{-- Rating badge --}}
                <div class="absolute top-2 left-2">
                    <span class="bg-brown-800/80 text-white text-xs font-bold px-1.5 py-0.5 rounded-md backdrop-blur-sm">{{ $film->rating }}</span>
                </div>
                {{-- Type badges --}}
                <div class="absolute top-2 right-2 flex flex-col gap-1">
                    @foreach($film->genres->take(1) as $genre)
                    <span class="bg-amber-500/90 text-stone-900 text-xs font-semibold px-1.5 py-0.5 rounded-md backdrop-blur-sm">{{ $genre->name }}</span>
                    @endforeach
                </div>
            </div>
            {{-- Info --}}
            <div class="p-3">
                <h3 class="font-semibold text-stone-800 text-sm leading-tight line-clamp-2 group-hover:text-brown-700 transition-colors">{{ $film->title }}</h3>
                <p class="text-xs text-stone-400 mt-1">{{ $film->duration }}</p>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- TIKS NEWS --}}
@if($latestNews->isNotEmpty())
<section class="bg-cream-dark py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-display text-2xl md:text-3xl font-bold text-stone-800">TIKS News</h2>
                <p class="text-stone-500 text-sm mt-1">Berita terkini seputar film dan bioskop</p>
            </div>
            <a href="{{ route('news.index') }}" class="text-brown-700 font-semibold text-sm hover:text-brown-900 flex items-center gap-1">
                Semua Berita <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($latestNews as $article)
            <a href="{{ route('news.show', $article->slug) }}" class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5 border border-stone-100">
                <div class="aspect-[16/9] overflow-hidden bg-stone-100">
                    <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}"
                         loading="lazy"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                         onerror="this.onerror=null;this.src='{{ asset('images/default-news.jpg') }}'">
                </div>
                <div class="p-4">
                    <span class="text-xs font-semibold text-brown-600 bg-brown-50 px-2 py-0.5 rounded-full">{{ $article->category }}</span>
                    <h3 class="font-semibold text-stone-800 mt-2 line-clamp-2 group-hover:text-brown-700 transition-colors">{{ $article->title }}</h3>
                    <p class="text-xs text-stone-400 mt-2">{{ $article->published_at?->diffForHumans() }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- REDEEM CTA --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
    <div class="bg-gradient-to-r from-brown-800 to-brown-900 rounded-3xl p-8 md:p-12 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="relative flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h2 class="font-display text-2xl md:text-3xl font-bold mb-2">🎟️ Sudah Punya Tiket?</h2>
                <p class="text-brown-200 text-sm md:text-base">Redeem tiketmu di mesin lobby bioskop dengan kode booking yang dikirim ke emailmu.</p>
            </div>
            <a href="{{ route('redeem.index') }}" class="flex-shrink-0 bg-amber-500 hover:bg-amber-400 text-stone-900 font-bold px-8 py-4 rounded-xl transition-all text-sm shadow-lg whitespace-nowrap">
                Redeem Sekarang →
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
function filterFilms(genre) {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-brown-700', 'text-white');
        btn.classList.add('bg-stone-100', 'text-stone-600');
    });
    const active = document.querySelector(`[data-genre="${genre}"]`);
    if (active) {
        active.classList.add('active', 'bg-brown-700', 'text-white');
        active.classList.remove('bg-stone-100', 'text-stone-600');
    }

    document.querySelectorAll('#film-grid .film-card').forEach(card => {
        if (genre === 'all' || card.dataset.genres.includes(genre)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
@endpush

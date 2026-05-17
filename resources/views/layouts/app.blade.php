<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TIKS - Tiket Bioskop Online')</title>
    <meta name="description" content="@yield('meta_description', 'TIKS - Beli tiket bioskop online dengan mudah dan cepat. Pilih film, pilih kursi, bayar, selesai!')">
    <meta name="keywords" content="@yield('meta_keywords', 'tiket bioskop, beli tiket film, tiks, bioskop online, tiket xxi')">
    <meta property="og:title" content="@yield('title', 'TIKS - Tiket Bioskop Online')">
    <meta property="og:description" content="@yield('meta_description', 'TIKS - Beli tiket bioskop online.')">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brown: {
                            50: '#fdf8f0', 100: '#faebd7', 200: '#f5d5a8',
                            300: '#edba73', 400: '#e19c45', 500: '#c8832d',
                            600: '#a06420', 700: '#7c4b18', 800: '#5c3510',
                            900: '#3d210a',
                        },
                        cream: { DEFAULT: '#fffbf5', dark: '#f5ede0' }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #fffbf5; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .film-card:hover .film-overlay { opacity: 1; }
        .film-overlay { opacity: 0; transition: opacity 0.3s ease; }
        .seat-btn { transition: all 0.15s ease; }
        .seat-btn:hover:not(:disabled) { transform: scale(1.1); }
    </style>
    @yield('head')
</head>
<body class="font-sans text-stone-800 min-h-screen flex flex-col">

{{-- HEADER --}}
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-stone-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16">

            {{-- LOGO --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 bg-gradient-to-br from-brown-700 to-brown-900 rounded-xl flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow">
                    <span class="text-white font-display font-bold text-base leading-none">T</span>
                </div>
                <span class="font-display font-bold text-2xl text-brown-800 tracking-tight">TIKS</span>
            </a>

            {{-- CITY SELECTOR --}}
            <div class="hidden sm:flex items-center gap-2 bg-cream-dark rounded-xl px-3 py-2 cursor-pointer hover:bg-brown-100 transition-colors group" onclick="document.getElementById('city-modal').classList.remove('hidden')">
                <svg class="w-4 h-4 text-brown-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span id="city-display" class="text-sm font-medium text-brown-700">{{ $selectedCityName ?? 'Pilih Kota' }}</span>
                <svg class="w-3.5 h-3.5 text-brown-500 group-hover:text-brown-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>

            {{-- NAV --}}
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-stone-600 hover:text-brown-700 hover:bg-brown-50 transition-colors {{ request()->routeIs('home') ? 'text-brown-700 bg-brown-50' : '' }}">Film</a>
                <a href="{{ route('news.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-stone-600 hover:text-brown-700 hover:bg-brown-50 transition-colors {{ request()->routeIs('news.*') ? 'text-brown-700 bg-brown-50' : '' }}">TIKS News</a>
                <a href="{{ route('redeem.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-stone-600 hover:text-brown-700 hover:bg-brown-50 transition-colors {{ request()->routeIs('redeem.*') ? 'text-brown-700 bg-brown-50' : '' }}">Redeem Tiket</a>
            </nav>

            {{-- AUTH --}}
            <div class="flex items-center gap-2">
                @auth
                    {{-- TICKETS ICON --}}
                    <a href="{{ route('tickets.index') }}" class="relative p-2 rounded-xl text-stone-500 hover:text-brown-700 hover:bg-brown-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    </a>
                    {{-- USER DROPDOWN --}}
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center gap-2 bg-brown-700 text-white rounded-xl px-3 py-2 text-sm font-medium hover:bg-brown-800 transition-colors">
                            <div class="w-6 h-6 bg-brown-500 rounded-full flex items-center justify-center text-xs font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                            <span class="hidden sm:block max-w-24 truncate">{{ auth()->user()->name }}</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-stone-100 py-2 z-50">
                            <div class="px-4 py-3 border-b border-stone-100">
                                <p class="text-sm font-semibold text-stone-800 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-stone-400">{{ auth()->user()->phone }}</p>
                            </div>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-stone-700 hover:bg-brown-50 hover:text-brown-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0v10"/></svg>
                                Admin Panel
                            </a>
                            @endif
                            <a href="{{ route('tickets.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-stone-700 hover:bg-brown-50 hover:text-brown-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                Tiket Saya
                            </a>
                            <div class="border-t border-stone-100 mt-1 pt-1">
                                <button onclick="confirmLogout()" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors text-left">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Logout
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-brown-700 hover:text-brown-900 transition-colors">Masuk</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-brown-700 text-white text-sm font-semibold rounded-xl hover:bg-brown-800 transition-colors shadow-sm">Daftar</a>
                @endauth

                {{-- Mobile menu --}}
                <button class="md:hidden p-2 rounded-xl text-stone-500 hover:bg-stone-100" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="hidden md:hidden pb-3 border-t border-stone-100 mt-2 pt-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-stone-600 hover:bg-brown-50 hover:text-brown-700">🎬 Film</a>
            <a href="{{ route('news.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-stone-600 hover:bg-brown-50 hover:text-brown-700">📰 TIKS News</a>
            <a href="{{ route('redeem.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-stone-600 hover:bg-brown-50 hover:text-brown-700">🎟️ Redeem Tiket</a>
            <div class="pt-2 border-t border-stone-100">
                <button onclick="document.getElementById('city-modal').classList.remove('hidden')" class="flex items-center gap-2 px-3 py-2 text-sm text-brown-700">
                    📍 <span id="city-display-mobile">{{ $selectedCityName ?? 'Pilih Kota' }}</span>
                </button>
            </div>
        </div>
    </div>
</header>

{{-- CITY MODAL --}}
<div id="city-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('city-modal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm z-10">
        <button onclick="document.getElementById('city-modal').classList.add('hidden')" class="absolute top-4 right-4 text-stone-400 hover:text-stone-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <h3 class="font-display font-bold text-xl text-stone-800 mb-4">📍 Pilih Kota</h3>
        <div class="grid grid-cols-2 gap-2">
            @foreach($cities ?? [] as $city)
            <button onclick="selectCity('{{ $city->slug }}', '{{ $city->name }}')"
                class="city-btn px-4 py-3 rounded-xl border-2 border-stone-200 text-sm font-medium text-stone-700 hover:border-brown-500 hover:bg-brown-50 hover:text-brown-700 transition-all text-left {{ (request()->cookie('selected_city') === $city->slug) ? 'border-brown-500 bg-brown-50 text-brown-700' : '' }}">
                {{ $city->name }}
            </button>
            @endforeach
        </div>
    </div>
</div>

{{-- FLASH MESSAGES --}}
@if(session('success'))
<div id="flash-msg" class="fixed top-20 right-4 z-50 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl shadow-lg text-sm font-medium flex items-center gap-2 max-w-sm">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div id="flash-msg" class="fixed top-20 right-4 z-50 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-lg text-sm font-medium flex items-center gap-2 max-w-sm">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('error') }}
</div>
@endif

{{-- MAIN CONTENT --}}
<main class="flex-1">
    @yield('content')
</main>

{{-- FOOTER --}}
<footer class="bg-brown-900 text-white mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-2">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-9 h-9 bg-brown-500 rounded-xl flex items-center justify-center">
                        <span class="text-white font-display font-bold text-base">T</span>
                    </div>
                    <span class="font-display font-bold text-2xl">TIKS</span>
                </div>
                <p class="text-brown-300 text-sm leading-relaxed mb-4">Platform pembelian tiket bioskop digital. Pilih film favoritmu, pilih kursi, dan nikmati pengalaman bioskop terbaik.</p>
                <div class="flex gap-3">
                    <div class="w-9 h-9 bg-brown-800 rounded-lg flex items-center justify-center hover:bg-brown-700 cursor-pointer transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </div>
                    <div class="w-9 h-9 bg-brown-800 rounded-lg flex items-center justify-center hover:bg-brown-700 cursor-pointer transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-3">Menu</h4>
                <ul class="space-y-2 text-sm text-brown-300">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Film Tayang</a></li>
                    <li><a href="{{ route('news.index') }}" class="hover:text-white transition-colors">TIKS News</a></li>
                    <li><a href="{{ route('redeem.index') }}" class="hover:text-white transition-colors">Redeem Tiket</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-3">Akun</h4>
                <ul class="space-y-2 text-sm text-brown-300">
                    @guest
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Masuk</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Daftar</a></li>
                    @else
                    <li><a href="{{ route('tickets.index') }}" class="hover:text-white transition-colors">Tiket Saya</a></li>
                    @endguest
                </ul>
            </div>
        </div>
        <div class="border-t border-brown-800 mt-8 pt-6 text-center text-brown-400 text-xs">
            <p>© {{ date('Y') }} TIKS Cinema. Semua hak dilindungi.</p>
        </div>
    </div>
</footer>

{{-- Alpine.js --}}
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>

<script>
function confirmLogout() {
    Swal.fire({
        title: 'Yakin mau logout?',
        text: 'Kamu harus login lagi untuk akses akun.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#7c4b18',
        cancelButtonColor: '#78716c',
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Batal',
        borderRadius: '1rem',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
        }
    });
}

function selectCity(slug, name) {
    fetch('{{ route("city.select") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ city_slug: slug })
    }).then(r => r.json()).then(data => {
        if (data.success) {
            document.getElementById('city-display').textContent = name;
            const mob = document.getElementById('city-display-mobile');
            if (mob) mob.textContent = name;
            document.getElementById('city-modal').classList.add('hidden');
            location.reload();
        }
    });
}

// Auto-hide flash message
setTimeout(() => {
    const fm = document.getElementById('flash-msg');
    if (fm) fm.style.display = 'none';
}, 4000);
</script>
@stack('scripts')
</body>
</html>

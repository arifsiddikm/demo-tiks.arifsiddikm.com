<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — TIKS Admin</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brown: { 50:'#fdf8f0',100:'#faebd7',200:'#f5d5a8',300:'#edba73',400:'#e19c45',500:'#c8832d',600:'#a06420',700:'#7c4b18',800:'#5c3510',900:'#3d210a' },
                        cream: { DEFAULT:'#fffbf5', dark:'#f5ede0' }
                    },
                    fontFamily: { sans:['Inter','sans-serif'], display:['Playfair Display','serif'] }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('head')
</head>
<body class="font-sans bg-stone-50 text-stone-800">

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: window.innerWidth >= 1024 }" xmlns:x-data="http://www.w3.org/1999/xhtml">

    {{-- SIDEBAR --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-brown-900 text-white flex flex-col shadow-2xl transition-transform duration-300"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           x-cloak>

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-brown-800">
            <div class="w-9 h-9 bg-amber-500 rounded-xl flex items-center justify-center shadow-md">
                <span class="text-stone-900 font-display font-bold text-base">T</span>
            </div>
            <div>
                <span class="font-display font-bold text-xl text-white">TIKS</span>
                <p class="text-brown-400 text-xs">Admin Panel</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            @php
            $navItems = [
                ['route' => 'admin.dashboard', 'icon' => 'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0v10', 'label' => 'Dashboard'],
                ['route' => 'admin.films.index', 'icon' => 'M15 10l4.553-2.069A1 1 0 0121 8.867v6.266a1 1 0 01-1.447.902L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'label' => 'Kelola Film'],
                ['route' => 'admin.orders.index', 'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', 'label' => 'Transaksi'],
                ['route' => 'admin.users.index', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Pengguna'],
                ['route' => 'admin.news.index', 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6m-6-4h2', 'label' => 'TIKS News'],
            ];
            @endphp

            @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                      {{ request()->routeIs($item['route']) ? 'bg-brown-700 text-white shadow-md' : 'text-brown-300 hover:bg-brown-800 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                </svg>
                {{ $item['label'] }}
            </a>
            @endforeach

            <div class="border-t border-brown-800 my-2"></div>
            <a href="{{ route('home') }}" target="_blank"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-brown-300 hover:bg-brown-800 hover:text-white transition-all">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Lihat Website
            </a>
        </nav>

        {{-- User --}}
        <div class="border-t border-brown-800 p-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-amber-500 rounded-xl flex items-center justify-center font-bold text-stone-900">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-brown-400">Administrator</p>
                </div>
            </div>
            <button onclick="confirmLogout()"
                class="w-full flex items-center justify-center gap-2 text-xs text-red-400 hover:text-red-300 hover:bg-brown-800 py-2 px-3 rounded-xl transition-all font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="flex-1 flex flex-col overflow-hidden" :class="sidebarOpen ? 'lg:ml-64' : ''">

        {{-- Top bar --}}
        <header class="bg-white border-b border-stone-200 px-4 sm:px-6 py-4 flex items-center justify-between flex-shrink-0 shadow-sm">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-xl text-stone-500 hover:bg-stone-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="font-semibold text-stone-800">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="text-xs text-stone-400">{{ now()->isoFormat('dddd, D MMMM Y') }}</div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6">
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 mb-4 text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-4 text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

{{-- Sidebar overlay for mobile --}}
<div x-show="sidebarOpen" @click="sidebarOpen = false"
     class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-cloak x-transition></div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
<script>
function confirmLogout() {
    Swal.fire({ title:'Yakin logout?', icon:'question', showCancelButton:true,
        confirmButtonColor:'#7c4b18', cancelButtonColor:'#78716c',
        confirmButtonText:'Ya, Logout', cancelButtonText:'Batal'
    }).then(r => { if (r.isConfirmed) document.getElementById('logout-form').submit(); });
}
function confirmDelete(formId, msg) {
    Swal.fire({ title:'Hapus Data?', text: msg || 'Data yang dihapus tidak bisa dikembalikan.',
        icon:'warning', showCancelButton:true, confirmButtonColor:'#dc2626',
        cancelButtonColor:'#78716c', confirmButtonText:'Ya, Hapus!', cancelButtonText:'Batal'
    }).then(r => { if (r.isConfirmed) document.getElementById(formId).submit(); });
}
</script>
@stack('scripts')
</body>
</html>

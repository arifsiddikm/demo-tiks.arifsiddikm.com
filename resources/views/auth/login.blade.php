@extends('layouts.app')
@section('title', 'Masuk - TIKS')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                <div class="w-12 h-12 bg-gradient-to-br from-brown-700 to-brown-900 rounded-2xl flex items-center justify-center shadow-lg">
                    <span class="text-white font-display font-bold text-xl">T</span>
                </div>
                <span class="font-display font-bold text-3xl text-brown-800">TIKS</span>
            </a>
            <h1 class="text-xl font-bold text-stone-800 mt-4">Selamat Datang Kembali!</h1>
            <p class="text-stone-500 text-sm mt-1">Masuk untuk beli tiket bioskop favoritmu</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-stone-100 p-8">

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-4 py-3 mb-5 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $errors->first() }}
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="phone" class="block text-sm font-semibold text-stone-700 mb-2">Nomor Telepon</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                            class="w-full pl-11 pr-4 py-3 border-2 border-stone-200 rounded-xl text-stone-800 text-sm placeholder-stone-300 focus:outline-none focus:border-brown-500 focus:ring-2 focus:ring-brown-100 transition-all bg-white"
                            required>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-stone-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input type="password" id="password" name="password" placeholder="••••••••"
                            class="w-full pl-11 pr-12 py-3 border-2 border-stone-200 rounded-xl text-stone-800 text-sm placeholder-stone-300 focus:outline-none focus:border-brown-500 focus:ring-2 focus:ring-brown-100 transition-all bg-white"
                            required>
                        <button type="button" onclick="togglePwd()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-stone-400 hover:text-stone-600">
                            <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-stone-300 text-brown-700 focus:ring-brown-500 cursor-pointer">
                    <label for="remember" class="ml-2 text-sm text-stone-600 cursor-pointer">Ingat saya</label>
                </div>

                <button type="submit"
                    class="w-full bg-brown-700 hover:bg-brown-800 text-white font-bold py-3.5 rounded-xl transition-all shadow-md hover:shadow-brown-700/30 text-sm">
                    Masuk ke TIKS
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-stone-500">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold text-brown-700 hover:text-brown-900 transition-colors">Daftar sekarang</a>
            </div>
        </div>

        {{-- Quick fill for testing --}}
        <div class="mt-4 bg-amber-50 border border-amber-200 rounded-2xl p-4">
            <p class="text-xs font-semibold text-amber-700 mb-2">🧪 Testing Account</p>
            <div class="flex gap-2">
                <button onclick="fillAdmin()" class="flex-1 text-xs bg-brown-700 text-white py-2 px-3 rounded-lg hover:bg-brown-800 transition-colors font-semibold">
                    Admin Login
                </button>
                <button onclick="fillUser()" class="flex-1 text-xs bg-stone-200 text-stone-700 py-2 px-3 rounded-lg hover:bg-stone-300 transition-colors font-semibold">
                    User Login
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePwd() {
    const pwd = document.getElementById('password');
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
}
function fillAdmin() {
    document.getElementById('phone').value = '08000000000';
    document.getElementById('password').value = 'admin123';
}
function fillUser() {
    document.getElementById('phone').value = '08112345678';
    document.getElementById('password').value = 'user123';
}
</script>
@endpush

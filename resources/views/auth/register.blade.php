@extends('layouts.app')
@section('title', 'Daftar - TIKS')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                <div class="w-12 h-12 bg-gradient-to-br from-brown-700 to-brown-900 rounded-2xl flex items-center justify-center shadow-lg">
                    <span class="text-white font-display font-bold text-xl">T</span>
                </div>
                <span class="font-display font-bold text-3xl text-brown-800">TIKS</span>
            </a>
            <h1 class="text-xl font-bold text-stone-800 mt-4">Buat Akun TIKS</h1>
            <p class="text-stone-500 text-sm mt-1">Daftar gratis dan mulai beli tiket sekarang!</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-stone-100 p-8">
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-4 py-3 mb-5 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-stone-700 mb-2">Nama Lengkap</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Nama lengkap kamu"
                            class="w-full pl-11 pr-4 py-3 border-2 border-stone-200 rounded-xl text-stone-800 text-sm placeholder-stone-300 focus:outline-none focus:border-brown-500 focus:ring-2 focus:ring-brown-100 transition-all bg-white"
                            required>
                    </div>
                </div>

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
                    <p class="text-xs text-stone-400 mt-1.5">Nomor ini digunakan untuk login dan redeem tiket</p>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-stone-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input type="password" id="password" name="password" placeholder="Minimal 6 karakter"
                            class="w-full pl-11 pr-4 py-3 border-2 border-stone-200 rounded-xl text-stone-800 text-sm placeholder-stone-300 focus:outline-none focus:border-brown-500 focus:ring-2 focus:ring-brown-100 transition-all bg-white"
                            required>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-stone-700 mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password"
                            class="w-full pl-11 pr-4 py-3 border-2 border-stone-200 rounded-xl text-stone-800 text-sm placeholder-stone-300 focus:outline-none focus:border-brown-500 focus:ring-2 focus:ring-brown-100 transition-all bg-white"
                            required>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-brown-700 hover:bg-brown-800 text-white font-bold py-3.5 rounded-xl transition-all shadow-md hover:shadow-brown-700/30 text-sm">
                    Buat Akun Gratis
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-stone-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-brown-700 hover:text-brown-900 transition-colors">Masuk sekarang</a>
            </div>
        </div>
    </div>
</div>
@endsection

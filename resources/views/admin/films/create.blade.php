@extends('layouts.admin')
@section('title', isset($film) ? 'Edit Film' : 'Tambah Film')
@section('page-title', isset($film) ? 'Edit Film: ' . $film->title : 'Tambah Film Baru')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6">
        <form action="{{ isset($film) ? route('admin.films.update', $film) : route('admin.films.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @if(isset($film)) @method('PUT') @endif

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            {{-- Title --}}
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-2">Judul Film <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $film->title ?? '') }}" required
                    class="w-full border-2 border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brown-500 focus:ring-2 focus:ring-brown-100 bg-white transition-all">
            </div>

            {{-- Synopsis --}}
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-2">Sinopsis <span class="text-red-500">*</span></label>
                <textarea name="synopsis" rows="4" required
                    class="w-full border-2 border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brown-500 focus:ring-2 focus:ring-brown-100 bg-white transition-all resize-none">{{ old('synopsis', $film->synopsis ?? '') }}</textarea>
            </div>

            {{-- Row: Duration, Rating, Language --}}
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Durasi <span class="text-red-500">*</span></label>
                    <input type="text" name="duration" placeholder="120 menit" value="{{ old('duration', $film->duration ?? '') }}" required
                        class="w-full border-2 border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brown-500 bg-white transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Rating <span class="text-red-500">*</span></label>
                    <select name="rating" required class="w-full border-2 border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brown-500 bg-white transition-all">
                        @foreach(['SU','G','PG','13+','17+','21+','R'] as $r)
                        <option value="{{ $r }}" {{ old('rating', $film->rating ?? '') === $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Bahasa</label>
                    <input type="text" name="language" placeholder="Indonesia" value="{{ old('language', $film->language ?? 'Indonesia') }}"
                        class="w-full border-2 border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brown-500 bg-white transition-all">
                </div>
            </div>

            {{-- Director & Cast --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Sutradara</label>
                    <input type="text" name="director" value="{{ old('director', $film->director ?? '') }}"
                        class="w-full border-2 border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brown-500 bg-white transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Pemeran (pisah koma)</label>
                    <input type="text" name="cast" placeholder="Aktor A, Aktris B" value="{{ old('cast', $film->cast ?? '') }}"
                        class="w-full border-2 border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brown-500 bg-white transition-all">
                </div>
            </div>

            {{-- Release date & Status --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Tanggal Rilis <span class="text-red-500">*</span></label>
                    <input type="date" name="release_date" value="{{ old('release_date', isset($film) && $film->release_date ? \Carbon\Carbon::parse($film->release_date)->format('Y-m-d') : '') }}" required
                        class="w-full border-2 border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brown-500 bg-white transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full border-2 border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brown-500 bg-white transition-all">
                        <option value="coming_soon" {{ old('status', $film->status ?? '') === 'coming_soon' ? 'selected' : '' }}>Segera Tayang</option>
                        <option value="now_showing" {{ old('status', $film->status ?? '') === 'now_showing' ? 'selected' : '' }}>Sedang Tayang</option>
                        <option value="ended" {{ old('status', $film->status ?? '') === 'ended' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
            </div>

            {{-- Trailer URL --}}
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-2">URL Trailer (YouTube)</label>
                <input type="url" name="trailer_url" placeholder="https://youtube.com/watch?v=..." value="{{ old('trailer_url', $film->trailer_url ?? '') }}"
                    class="w-full border-2 border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brown-500 bg-white transition-all">
            </div>

            {{-- Genres --}}
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-2">Genre</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($genres as $genre)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="genres[]" value="{{ $genre->id }}"
                            {{ in_array($genre->id, old('genres', $selectedGenres ?? [])) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-stone-300 text-brown-700 focus:ring-brown-500 cursor-pointer">
                        <span class="text-sm text-stone-700">{{ $genre->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Poster --}}
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-2">Poster Film</label>
                @if(isset($film) && $film->poster)
                <div class="mb-3">
                    <img src="{{ $film->poster_url }}" alt="Poster" class="w-24 h-36 object-cover rounded-xl shadow-sm">
                    <p class="text-xs text-stone-400 mt-1">Poster saat ini. Upload baru untuk mengganti.</p>
                </div>
                @endif
                <input type="file" name="poster" accept="image/jpeg,image/png,image/webp"
                    class="w-full border-2 border-dashed border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-600 focus:outline-none focus:border-brown-400 bg-stone-50 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brown-100 file:text-brown-700 hover:file:bg-brown-200 cursor-pointer transition-all">
                <p class="text-xs text-stone-400 mt-1.5">Format: JPG, PNG, WebP. Maks 3MB. Rasio ideal 2:3.</p>
            </div>

            {{-- Is Active --}}
            <div class="flex items-center gap-3">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                    {{ old('is_active', isset($film) ? $film->is_active : true) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-stone-300 text-brown-700 focus:ring-brown-500 cursor-pointer">
                <label for="is_active" class="text-sm font-semibold text-stone-700 cursor-pointer">Film Aktif (tampil di website)</label>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3 pt-2 border-t border-stone-100">
                <button type="submit" class="bg-brown-700 hover:bg-brown-800 text-white font-bold px-8 py-3 rounded-xl transition-colors text-sm shadow-sm">
                    {{ isset($film) ? '💾 Simpan Perubahan' : '✅ Tambah Film' }}
                </button>
                <a href="{{ route('admin.films.index') }}" class="border-2 border-stone-200 text-stone-600 font-semibold px-6 py-3 rounded-xl hover:bg-stone-50 transition-colors text-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

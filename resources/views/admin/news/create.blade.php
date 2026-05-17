@extends('layouts.admin')
@section('title', isset($news) ? 'Edit Berita' : 'Tulis Berita')
@section('page-title', isset($news) ? 'Edit Berita' : 'Tulis Berita Baru')

@section('head')
<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6">
        <form action="{{ isset($news) ? route('admin.news.update', $news) : route('admin.news.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @if(isset($news)) @method('PUT') @endif

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-2">Judul Berita <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $news->title ?? '') }}" required
                    class="w-full border-2 border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brown-500 focus:ring-2 focus:ring-brown-100 bg-white transition-all">
            </div>

            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-2">Excerpt (Ringkasan)</label>
                <textarea name="excerpt" rows="2"
                    class="w-full border-2 border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brown-500 focus:ring-2 focus:ring-brown-100 bg-white transition-all resize-none">{{ old('excerpt', $news->excerpt ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-2">Konten Berita <span class="text-red-500">*</span></label>
                <textarea name="content" id="content" rows="12">{{ old('content', $news->content ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                    <select name="category" required class="w-full border-2 border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brown-500 bg-white transition-all">
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category', $news->category ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Thumbnail</label>
                    @if(isset($news) && $news->thumbnail)
                    <div class="mb-2">
                        <img src="{{ $news->thumbnail_url }}" class="w-20 h-14 object-cover rounded-xl">
                    </div>
                    @endif
                    <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"
                        class="w-full border-2 border-dashed border-stone-200 rounded-xl px-3 py-2 text-sm text-stone-600 bg-stone-50 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brown-100 file:text-brown-700 hover:file:bg-brown-200 cursor-pointer">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" id="is_published" name="is_published" value="1"
                    {{ old('is_published', isset($news) ? $news->is_published : true) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-stone-300 text-brown-700 focus:ring-brown-500 cursor-pointer">
                <label for="is_published" class="text-sm font-semibold text-stone-700 cursor-pointer">Publish sekarang</label>
            </div>

            <div class="flex gap-3 pt-2 border-t border-stone-100">
                <button type="submit" class="bg-brown-700 hover:bg-brown-800 text-white font-bold px-8 py-3 rounded-xl transition-colors text-sm shadow-sm">
                    {{ isset($news) ? '💾 Simpan Perubahan' : '✅ Publish Berita' }}
                </button>
                <a href="{{ route('admin.news.index') }}" class="border-2 border-stone-200 text-stone-600 font-semibold px-6 py-3 rounded-xl hover:bg-stone-50 transition-colors text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
ClassicEditor.create(document.querySelector('#content'), {
    toolbar: ['heading','|','bold','italic','underline','|','bulletedList','numberedList','blockQuote','|','link','|','undo','redo'],
}).catch(e => console.error(e));
</script>
@endpush

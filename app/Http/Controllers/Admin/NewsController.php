<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $news = News::with('author')
            ->when($request->search, fn($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->latest()
            ->paginate(15);
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        $categories = ['Box Office', 'Cinema Update', 'Film Review', 'Tips & Trik', 'Industri Film', 'Selebrita', 'Umum'];
        return view('admin.news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'excerpt'    => 'nullable|string|max:500',
            'content'    => 'required|string',
            'category'   => 'required|string',
            'thumbnail'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $data = $request->only(['title','excerpt','content','category']);
        $data['slug']         = Str::slug($request->title) . '-' . time();
        $data['author_id']    = Auth::id();
        $data['is_published'] = $request->boolean('is_published', true);
        $data['published_at'] = $data['is_published'] ? now() : null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('news', 'public');
        }

        News::create($data);
        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil ditambahkan!');
    }

    public function edit(News $news)
    {
        $categories = ['Box Office', 'Cinema Update', 'Film Review', 'Tips & Trik', 'Industri Film', 'Selebrita', 'Umum'];
        return view('admin.news.edit', compact('news', 'categories'));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'content'   => 'required|string',
            'category'  => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $data = $request->only(['title','excerpt','content','category']);
        $data['is_published'] = $request->boolean('is_published');
        if ($data['is_published'] && !$news->published_at) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('thumbnail')) {
            if ($news->thumbnail) Storage::disk('public')->delete($news->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('news', 'public');
        }

        $news->update($data);
        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil diperbarui!');
    }

    public function destroy(News $news)
    {
        if ($news->thumbnail) Storage::disk('public')->delete($news->thumbnail);
        $news->delete();
        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil dihapus.');
    }
}

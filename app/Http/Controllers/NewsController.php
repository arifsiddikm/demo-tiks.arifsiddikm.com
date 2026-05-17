<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $category   = $request->query('category');
        $news       = News::where('is_published', true)
            ->when($category, fn($q) => $q->where('category', $category))
            ->latest('published_at')
            ->paginate(9);
        $categories = News::where('is_published', true)->distinct()->pluck('category');

        return view('news.index', compact('news', 'categories', 'category'));
    }

    public function show(string $slug)
    {
        $article = News::where('slug', $slug)->where('is_published', true)->firstOrFail();
        $related  = News::where('is_published', true)
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('news.show', compact('article', 'related'));
    }
}

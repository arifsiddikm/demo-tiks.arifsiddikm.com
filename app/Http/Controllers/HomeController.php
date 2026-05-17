<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Film;
use App\Models\FilmSchedule;
use App\Models\Genre;
use App\Models\News;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $selectedCitySlug = $request->cookie('selected_city', 'cilegon');
        $selectedCity     = City::where('slug', $selectedCitySlug)->where('is_active', true)->first()
                         ?? City::where('is_active', true)->first();

        $nowShowing = Film::with('genres')
            ->where('status', 'now_showing')
            ->where('is_active', true)
            ->latest()
            ->get();

        $comingSoon = Film::where('status', 'coming_soon')->where('is_active', true)->latest()->take(4)->get();
        $cities     = City::where('is_active', true)->get();
        $latestNews = News::where('is_published', true)->latest('published_at')->take(3)->get();

        return view('home.index', compact('nowShowing', 'comingSoon', 'selectedCity', 'cities', 'latestNews'));
    }

    public function selectCity(Request $request)
    {
        $slug = $request->input('city_slug');
        $city = City::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return response()->json(['success' => true, 'city' => $city->name])
            ->cookie('selected_city', $city->slug, 60 * 24 * 30); // 30 days
    }
}

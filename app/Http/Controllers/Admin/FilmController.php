<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use App\Models\City;
use App\Models\Film;
use App\Models\FilmSchedule;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FilmController extends Controller
{
    public function index(Request $request)
    {
        $films = Film::with('genres')
            ->when($request->search, fn($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);
        return view('admin.films.index', compact('films'));
    }

    public function create()
    {
        $genres = Genre::orderBy('name')->get();
        return view('admin.films.create', compact('genres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'synopsis'     => 'required|string',
            'duration'     => 'required|string',
            'rating'       => 'required|string',
            'director'     => 'nullable|string',
            'cast'         => 'nullable|string',
            'release_date' => 'required|date',
            'status'       => 'required|in:coming_soon,now_showing,ended',
            'poster'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'trailer_url'  => 'nullable|url',
            'genres'       => 'nullable|array',
        ]);

        $data          = $request->only(['title','synopsis','duration','rating','language','director','cast','release_date','status','trailer_url']);
        $data['slug']  = Str::slug($request->title) . '-' . time();
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('poster')) {
            $data['poster'] = $request->file('poster')->store('posters', 'public');
        }

        $film = Film::create($data);
        if ($request->genres) {
            $film->genres()->sync($request->genres);
        }

        return redirect()->route('admin.films.index')->with('success', 'Film berhasil ditambahkan!');
    }

    public function edit(Film $film)
    {
        $genres = Genre::orderBy('name')->get();
        $selectedGenres = $film->genres->pluck('id')->toArray();
        return view('admin.films.edit', compact('film', 'genres', 'selectedGenres'));
    }

    public function update(Request $request, Film $film)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'synopsis'     => 'required|string',
            'duration'     => 'required|string',
            'rating'       => 'required|string',
            'release_date' => 'required|date',
            'status'       => 'required|in:coming_soon,now_showing,ended',
            'poster'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $data = $request->only(['title','synopsis','duration','rating','language','director','cast','release_date','status','trailer_url']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('poster')) {
            if ($film->poster) Storage::disk('public')->delete($film->poster);
            $data['poster'] = $request->file('poster')->store('posters', 'public');
        }

        $film->update($data);
        if ($request->has('genres')) {
            $film->genres()->sync($request->genres ?? []);
        }

        return redirect()->route('admin.films.index')->with('success', 'Film berhasil diperbarui!');
    }

    public function destroy(Film $film)
    {
        if ($film->poster) Storage::disk('public')->delete($film->poster);
        $film->delete();
        return redirect()->route('admin.films.index')->with('success', 'Film berhasil dihapus.');
    }

    // --- SCHEDULES ---
    public function schedules(Film $film)
    {
        $schedules = FilmSchedule::with('cinema.city')
            ->where('film_id', $film->id)
            ->orderBy('show_date')
            ->orderBy('show_time')
            ->paginate(20);
        $cinemas = Cinema::with('city')->where('is_active', true)->get();
        return view('admin.films.schedules', compact('film', 'schedules', 'cinemas'));
    }

    public function storeSchedule(Request $request, Film $film)
    {
        $request->validate([
            'cinema_id' => 'required|exists:cinemas,id',
            'show_date' => 'required|date|after_or_equal:today',
            'show_time' => 'required',
            'studio'    => 'required|string',
            'film_type' => 'required|in:2D,3D,4DX,IMAX',
            'price'     => 'required|numeric|min:10000',
        ]);

        FilmSchedule::create([
            'film_id'         => $film->id,
            'cinema_id'       => $request->cinema_id,
            'show_date'       => $request->show_date,
            'show_time'       => $request->show_time,
            'studio'          => $request->studio,
            'film_type'       => $request->film_type,
            'total_seats'     => 96,
            'available_seats' => 96,
            'price'           => $request->price,
            'is_active'       => true,
        ]);

        return back()->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function destroySchedule(FilmSchedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Models\City;
use App\Models\Film;
use App\Models\FilmSchedule;
use App\Models\Seat;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FilmController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $film = Film::with(['genres'])->where('slug', $slug)->where('is_active', true)->firstOrFail();

        $selectedCitySlug = $request->cookie('selected_city', 'cilegon');
        $selectedCity     = City::where('slug', $selectedCitySlug)->where('is_active', true)->first()
                         ?? City::where('is_active', true)->first();

        // Generate 14-day date range starting today
        $dates = collect(range(0, 13))->map(fn($d) => Carbon::today()->addDays($d));

        // Schedules grouped by date then cinema for selected city
        $selectedDate = $request->query('date', Carbon::today()->format('Y-m-d'));

        $schedules = FilmSchedule::with('cinema.city')
            ->where('film_id', $film->id)
            ->where('show_date', $selectedDate)
            ->whereHas('cinema', fn($q) => $q->where('city_id', $selectedCity->id)->where('is_active', true))
            ->where('is_active', true)
            ->orderBy('show_time')
            ->get()
            ->groupBy('cinema_id');

        $cities = City::where('is_active', true)->get();

        return view('films.show', compact('film', 'dates', 'selectedDate', 'selectedCity', 'schedules', 'cities'));
    }

    public function schedulesByCityDate(Request $request)
    {
        $request->validate([
            'film_id' => 'required|exists:films,id',
            'city_id' => 'required|exists:cities,id',
            'date'    => 'required|date',
        ]);

        $schedules = FilmSchedule::with('cinema')
            ->where('film_id', $request->film_id)
            ->where('show_date', $request->date)
            ->whereHas('cinema', fn($q) => $q->where('city_id', $request->city_id)->where('is_active', true))
            ->where('is_active', true)
            ->orderBy('show_time')
            ->get()
            ->groupBy('cinema_id');

        $html = '';
        if ($schedules->isEmpty()) {
            $html = '<div class="col-span-full text-center py-10 text-gray-400"><p class="text-lg">Tidak ada jadwal untuk kota & tanggal ini.</p></div>';
        } else {
            foreach ($schedules as $cinemaId => $cinemaSchedules) {
                $cinema = $cinemaSchedules->first()->cinema;
                $html  .= view('films.partials.schedule-group', compact('cinema', 'cinemaSchedules'))->render();
            }
        }

        return response()->json(['html' => $html]);
    }

    public function seats(int $scheduleId)
    {
        $schedule = FilmSchedule::with(['film', 'cinema', 'seats'])->findOrFail($scheduleId);

        // Auto-generate seats if none exist
        if ($schedule->seats->isEmpty()) {
            $this->generateSeats($schedule);
            $schedule->load('seats');
        }

        $bookedCodes = $schedule->seats->where('status', 'booked')->pluck('code')->toArray();

        return response()->json([
            'schedule'    => [
                'id'        => $schedule->id,
                'film'      => $schedule->film->title,
                'cinema'    => $schedule->cinema->name,
                'date'      => $schedule->show_date->format('d M Y'),
                'time'      => $schedule->formatted_time,
                'studio'    => $schedule->studio,
                'film_type' => $schedule->film_type,
                'price'     => $schedule->price,
                'price_fmt' => $schedule->formatted_price,
            ],
            'booked_seats' => $bookedCodes,
            'rows'         => ['A','B','C','D','E','F','G','H'],
            'cols'         => range(1, 12),
        ]);
    }

    private function generateSeats(FilmSchedule $schedule): void
    {
        $rows = ['A','B','C','D','E','F','G','H'];
        $cols = range(1, 12);
        $batch = [];
        foreach ($rows as $row) {
            foreach ($cols as $col) {
                $batch[] = [
                    'schedule_id' => $schedule->id,
                    'row'         => $row,
                    'number'      => $col,
                    'code'        => $row . $col,
                    'status'      => 'available',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
        }
        \Illuminate\Support\Facades\DB::table('seats')->insert($batch);
    }
}

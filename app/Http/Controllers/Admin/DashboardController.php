<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Film;
use App\Models\User;
use App\Models\FilmSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue    = Booking::where('status', 'paid')->sum('total_price');
        $totalBookings   = Booking::count();
        $paidBookings    = Booking::where('status', 'paid')->count();
        $totalUsers      = User::where('role', 'user')->count();
        $totalFilms      = Film::where('is_active', true)->count();
        $todayRevenue    = Booking::where('status', 'paid')->whereDate('paid_at', today())->sum('total_price');
        $todayBookings   = Booking::whereDate('created_at', today())->count();
        $pendingBookings = Booking::where('status', 'waiting_payment')->count();

        // Chart: daily revenue last 14 days
        $revenueChart = Booking::where('status', 'paid')
            ->where('paid_at', '>=', Carbon::now()->subDays(13))
            ->select(DB::raw('DATE(paid_at) as date'), DB::raw('SUM(total_price) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $chartData   = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = Carbon::now()->subDays($i)->format('d M');
            $chartData[]   = $revenueChart[$d]->total ?? 0;
        }

        // Top films by bookings
        $topFilms = Film::withCount(['schedules as booking_count' => fn($q) => $q->whereHas('bookings', fn($b) => $b->where('status', 'paid'))])
            ->orderByDesc('booking_count')
            ->take(5)
            ->get();

        // Recent bookings
        $recentBookings = Booking::with(['user', 'schedule.film'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue', 'totalBookings', 'paidBookings', 'totalUsers',
            'totalFilms', 'todayRevenue', 'todayBookings', 'pendingBookings',
            'chartLabels', 'chartData', 'topFilms', 'recentBookings'
        ));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Booking::with(['user', 'schedule.film', 'schedule.cinema', 'bookingSeats'])
            ->when($request->search, function($q) use ($request) {
                $q->where('booking_code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $request->search . '%')
                                                    ->orWhere('phone', 'like', '%' . $request->search . '%'));
            })
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'schedule.film', 'schedule.cinema', 'bookingSeats']);
        return view('admin.orders.show', compact('booking'));
    }

    public function confirm(Booking $booking)
    {
        if (!in_array($booking->status, ['waiting_payment', 'pending'])) {
            return response()->json(['success' => false, 'message' => 'Status tidak valid untuk dikonfirmasi.'], 422);
        }

        $booking->update(['status' => 'paid', 'paid_at' => now()]);

        try {
            app(\App\Services\EmailService::class)->sendTicketConfirmation($booking);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Email konfirmasi manual gagal: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Pembayaran berhasil dikonfirmasi dan tiket dikirim ke pengguna.']);
    }

    public function cancel(Booking $booking)
    {
        if ($booking->status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Tiket sudah lunas, tidak bisa dibatalkan.'], 422);
        }
        $booking->update(['status' => 'cancelled']);
        return response()->json(['success' => true, 'message' => 'Booking berhasil dibatalkan.']);
    }
}


class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::when($request->search, function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            })
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->withCount('bookings')
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'phone'    => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:user,admin',
        ]);

        User::create([
            'name'      => $request->name,
            'phone'     => $request->phone,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dibuat!');
    }

    public function toggleActive(User $user)
    {
        if ($user->isAdmin() && $user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Tidak bisa menonaktifkan akun sendiri.']);
        }
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json(['success' => true, 'message' => "Akun berhasil {$status}.", 'is_active' => $user->is_active]);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dihapus.');
    }
}

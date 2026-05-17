<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('home');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string',
        ], [
            'phone.required'    => 'Nomor telepon wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $phone = $request->phone;
        if (strpos($phone, '62') === 0) {
            $phone = '0' . substr($phone, 2);
        }

        $credentials = ['phone' => $phone, 'password' => $request->password];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['phone' => 'Akun kamu dinonaktifkan. Hubungi admin.']);
            }

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            // Redirect back to intended URL (e.g. film detail page)
            $intended = session()->pull('url.intended', route('home'));
            return redirect($intended)->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        return back()->withErrors(['phone' => 'Nomor telepon atau password salah.'])->withInput($request->only('phone'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Berhasil logout.');
    }
}

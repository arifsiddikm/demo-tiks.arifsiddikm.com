<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegister()
    {
        if (Auth::check()) return redirect()->route('home');
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'phone'    => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'phone.required'     => 'Nomor telepon wajib diisi.',
            'phone.unique'       => 'Nomor telepon sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $phone = $request->phone;
        if (strpos($phone, '62') === 0) {
            $phone = '0' . substr($phone, 2);
        }

        $user = User::create([
            'name'     => $request->name,
            'phone'    => $phone,
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $intended = session()->pull('url.intended', route('home'));
        return redirect($intended)->with('success', 'Akun berhasil dibuat! Selamat datang, ' . $user->name . '!');
    }
}

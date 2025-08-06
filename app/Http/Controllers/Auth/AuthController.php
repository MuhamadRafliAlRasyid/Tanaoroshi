<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Bagian;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        $bagians = Bagian::all(); // Pastikan penulisan model benar (Bagian, bukan bagian)
        return view('auth.register', compact('bagians'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:user,spv,admin', // Sesuaikan dengan role yang ada
            'bagian_id' => 'nullable|exists:bagian,id',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $userData = $request->only(['name', 'email', 'role', 'bagian_id']);
        $userData['password'] = Hash::make($request->password);

        if ($request->hasFile('profile_photo')) {
            $fileName = Str::random(10) . '.' . $request->file('profile_photo')->getClientOriginalExtension();
            $request->file('profile_photo')->move(public_path('images/profile'), $fileName);
            $userData['profile_photo_path'] = $fileName;
        }

        User::create($userData);

        // Redirect berdasarkan role
        return match ($userData['role']) {
            'super' => redirect('/super/dashboard')->with('success', 'Registrasi berhasil!'),
            'admin' => redirect('/admin/dashboard')->with('success', 'Registrasi berhasil!'),
            'karyawan' => redirect('/karyawan/dashboard')->with('success', 'Registrasi berhasil!'),
            default => redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.'),
        };
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Anda telah logout.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $role = Auth::user()->role;
            return match ($role) {
                'super' => redirect('/super/kepala')->with('success', 'Login berhasil!'),
                'admin' => redirect('/admin/dashboard')->with('success', 'Login berhasil!'),
                'karyawan' => redirect('/karyawan/gudang')->with('success', 'Login berhasil!'),
                default => redirect('/')->with('success', 'Login berhasil!'),
            };
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }
}

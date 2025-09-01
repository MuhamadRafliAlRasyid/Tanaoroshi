<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Bagian;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Menampilkan formulir registrasi.
     *
     * @return \Illuminate\View\View
     */
    public function showRegister()
    {
        $bagians = Bagian::all();
        return view('auth.register', compact('bagians'));
    }

    /**
     * Menangani proses registrasi pengguna baru.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:user,spv,admin,super,karyawan', // Ditambahkan 'super' dan 'karyawan'
            'bagian_id' => 'nullable|exists:bagian,id',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $userData = $request->only(['name', 'email', 'role', 'bagian_id']);
        $userData['password'] = Hash::make($request->password);

        if ($request->hasFile('profile_photo')) {
            $directory = public_path('images/profile');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $fileName = Str::random(10) . '.' . $request->file('profile_photo')->getClientOriginalExtension();
            $request->file('profile_photo')->move($directory, $fileName);
            $userData['profile_photo_path'] = $fileName;
        }

        User::create($userData);

        return match ($userData['role']) {
            'super' => redirect('/super/dashboard')->with('success', 'Registrasi berhasil!'),
            'admin' => redirect('/admin/dashboard')->with('success', 'Registrasi berhasil!'),
            'karyawan' => redirect('/karyawan/dashboard')->with('success', 'Registrasi berhasil!'),
            default => redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.'),
        };
    }

    /**
     * Menampilkan formulir login.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Menangani proses logout pengguna.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Anda telah logout.');
    }

    /**
     * Menangani proses login pengguna.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $role = Auth::user()->role;
            $sparepartsId = $request->input('spareparts_id'); // Ambil spareparts_id dari query string jika ada

            Log::info('User logged in: ' . Auth::user()->email . ', Role: ' . ($role ?? 'No role') . ', Spareparts ID: ' . ($sparepartsId ?? 'None'));

            if ($role === 'karyawan') {
                // Jika ada spareparts_id (dari scan QR), arahkan ke halaman create pengambilan
                if ($sparepartsId) {
                    return redirect()->route('pengambilan.create', ['spareparts_id' => $sparepartsId])->with('success', 'Login berhasil!');
                }
                // Jika login biasa, arahkan ke dashboard karyawan
                return redirect('/karyawan/dashboard')->with('success', 'Login berhasil!');
            }

            return match ($role) {
                'super' => redirect('/super/dashboard')->with('success', 'Login berhasil!'),
                'admin' => redirect('/admin/dashboard')->with('success', 'Login berhasil!'),
                default => redirect('/')->with('success', 'Login berhasil!'),
            };
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }
}

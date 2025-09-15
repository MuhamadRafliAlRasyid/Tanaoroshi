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
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,karyawan,super',
            'bagian_id' => 'nullable|exists:bagians,id',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'role', 'bagian_id']);
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('profile_photo')) {
            $directory = public_path('images/profile');
            if (!file_exists($directory)) mkdir($directory, 0755, true);

            $fileName = Str::random(10) . '.' . $request->file('profile_photo')->getClientOriginalExtension();
            $request->file('profile_photo')->move($directory, $fileName);
            $data['profile_photo_path'] = $fileName;
        }

        User::create($data);

        return match ($data['role']) {
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
        $sparepartsId = request()->query('spareparts_id'); // Ambil dari query string (dari scan QR)
        // Simpan spareparts_id ke sesi jika ada
        if ($sparepartsId) {
            session(['spareparts_id' => $sparepartsId]);
        }
        return view('auth.login', compact('sparepartsId'));
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

        // Ambil spareparts_id dari sesi (karena query string tidak otomatis tersedia di POST)
        $sparepartsId = session('spareparts_id') ?? $request->input('spareparts_id') ?? $request->query('spareparts_id');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $role = Auth::user()->role;

            Log::info('User logged in: ' . Auth::user()->email . ', Role: ' . ($role ?? 'No role') . ', Spareparts ID: ' . ($sparepartsId ?? 'None'));

            // Prioritaskan pengalihan ke pengambilan.create jika sparepartsId ada
            if ($sparepartsId) {
                // Hapus sesi setelah digunakan untuk mencegah penggunaan berulang
                $request->session()->forget('spareparts_id');
                return redirect()->route('pengambilan.create', ['spareparts_id' => $sparepartsId])->with('success', 'Login berhasil!');
            }

            // Pengalihan default berdasarkan role
            return match ($role) {
                'super' => redirect('/super/dashboard')->with('success', 'Login berhasil!'),
                'admin' => redirect('/admin/dashboard')->with('success', 'Login berhasil!'),
                'karyawan' => redirect('/karyawan/dashboard')->with('success', 'Login berhasil!'),
                default => redirect('/')->with('success', 'Login berhasil!'),
            };
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }
}

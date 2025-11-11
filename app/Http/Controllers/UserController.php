<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bagian;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Tampilkan daftar pengguna (selain admin) beserta relasi bagian.
     */
    public function index(Request $request)
    {
        $query = User::with('bagian')->where('role', '!=', 'admin');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhereHas('bagian', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.index', compact('users'));
    }

    /**
     * Tampilkan form untuk membuat user baru.
     */
    public function create()
    {
        $bagians = Bagian::all();
        return view('admin.create', compact('bagians'));
    }

    /**
     * Simpan user baru ke database.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
                'bagian_id' => 'nullable|exists:bagian,id',
                'role' => 'required|in:super,admin,karyawan',
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);
            $this->handleFileUpload($request, $user);

            $redirectRoute = optional(Auth::user())->role === 'admin'
                ? 'admin.index'
                : 'permintaan.index';

            Log::info('User created successfully', ['user_id' => $user->id, 'email' => $user->email]);

            return redirect()->route($redirectRoute)->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in store method: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Gagal membuat user. Silakan coba lagi.');
        }
    }

    /**
     * Tampilkan form edit data user.
     */
    public function edit(User $user) // ← Laravel otomatis decode hashid → $user
{
    $bagians = Bagian::all();
    return view('admin.edit', compact('user', 'bagians'));
}

public function update(Request $request, User $user)
{
    // $user sudah dari hashid
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:6',
        'bagian_id' => 'nullable|exists:bagian,id',
        'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'role' => 'required|in:super,admin,karyawan',
    ]);

    if ($request->filled('password')) {
        $validated['password'] = Hash::make($request->password);
    } else {
        unset($validated['password']);
    }

    $validated = $this->handleFileUpload($request, $user, $validated);
    $user->update($validated);

    return redirect()->route('admin.index')->with('success', 'User updated.');
}

public function destroy(User $user)
{
    // Hapus foto
    if ($user->profile_photo_path) {
        $path = public_path('images/profile/' . $user->profile_photo_path);
        if (File::exists($path)) File::delete($path);
    }

    $user->delete();
    return redirect()->route('admin.index')->with('success', 'User deleted.');
}
    /**
     * Handle upload foto profil user.
     */
    private function handleFileUpload(Request $request, User $user, array $validated = [])
    {
        if ($request->hasFile('profile_photo')) {
            $directory = public_path('images/profile');
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $file = $request->file('profile_photo');
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move($directory, $filename);

            if ($user->profile_photo_path && File::exists($directory . '/' . $user->profile_photo_path)) {
                File::delete($directory . '/' . $user->profile_photo_path);
            }

            $validated['profile_photo_path'] = $filename;
        } else {
            $validated['profile_photo_path'] = $user->profile_photo_path;
        }

        return $validated;
    }
}

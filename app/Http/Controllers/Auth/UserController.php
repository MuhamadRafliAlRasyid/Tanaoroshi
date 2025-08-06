<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bagian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Tampilkan daftar pengguna (selain admin) beserta relasi bagian.
     */
    public function index()
    {
        $users = User::with('bagian')
            ->where('role', '!=', 'admin')
            ->get();

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'bagian_id' => 'nullable|exists:bagian,id',
            'role' => 'required|in:admin,user,spv',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Enkripsi password
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        // Upload foto profil jika ada
        $filename = $this->handleFileUpload($request);
        if ($filename) {
            $user->profile_photo_path = $filename;
            $user->save();
        }

        // Redirect berdasarkan role user yang login
        $redirectRoute = optional(Auth::user())->role === 'admin'
            ? 'admin.index'
            : 'permintaan.index';


        return redirect()->route($redirectRoute)->with('success', 'User created successfully.');
    }

    /**
     * Tampilkan form edit data user.
     */
    public function edit(User $user)
    {
        $bagians = Bagian::all();
        return view('admin.edit', compact('user', 'bagians'));
    }

    /**
     * Update data user ke database.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'bagian_id' => 'nullable|exists:bagian,id',
            'role' => 'required|in:admin,user,spv',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Update password jika diisi
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        // Upload foto baru jika ada
        $filename = $this->handleFileUpload($request, $user);
        if ($filename) {
            $validated['profile_photo_path'] = $filename;
        }

        $user->update($validated);

        // Redirect berdasarkan role user yang login
        $redirectRoute = optional(Auth::user())->role === 'admin'
            ? 'admin.index'
            : 'permintaan.index';

        return redirect()->route($redirectRoute)->with('success', 'User updated successfully.');
    }

    /**
     * Hapus user dan foto profil terkait.
     */
    public function destroy(User $user)
    {
        // Hapus file foto jika ada
        if ($user->profile_photo_path && file_exists(public_path('img/profile_photo/' . $user->profile_photo_path))) {
            unlink(public_path('img/profile_photo/' . $user->profile_photo_path));
        }

        $user->delete();
        return redirect()->route('admin.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Menghandle upload foto profil user.
     *
     * @param Request $request
     * @param User|null $user
     * @return string|null
     */
    private function handleFileUpload(Request $request, User $user = null)
    {
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/profile_photo'), $filename);

            // Hapus foto lama jika ada
            if ($user && $user->profile_photo_path) {
                $oldPath = public_path('img/profile_photo/' . $user->profile_photo_path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            return $filename;
        }

        // Jika tidak ada upload baru, tetap gunakan foto lama (jika ada)
        return $user ? $user->profile_photo_path : null;
    }
}

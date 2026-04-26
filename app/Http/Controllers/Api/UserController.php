<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Bagian;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Index - Daftar User (selain super)
     */
    public function index(Request $request)
    {
        try {
            $query = User::with('bagian')->where('role', '!=', 'admin');

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereHas('bagian', fn($q) => $q->where('nama_bagian', 'like', "%{$search}%"));
                });
            }

            $users = $query->latest()->paginate(15);

            return response()->json([
                'status' => true,
                'data'   => $users->items(),
                'meta'   => [
                    'current_page' => $users->currentPage(),
                    'last_page'    => $users->lastPage(),
                    'total'        => $users->total(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('User Index Error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Gagal mengambil data pengguna'
            ], 500);
        }
    }

    /**
     * Store - Buat User Baru (dengan foto)
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'          => 'required|string|max:255',
                'email'         => 'required|email|unique:users,email',
                'password'      => 'required|string|min:6',
                'role'          => 'required|in:admin,karyawan',
                'bagian_id'     => 'nullable|exists:bagian,id',
                'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated);

            // Handle upload foto
            $this->handleFileUpload($request, $user);

            Log::info('User created successfully', [
                'user_id' => $user->id,
                'email'   => $user->email
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'User berhasil ditambahkan',
                'data'    => $user->load('bagian')
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('User Store Error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Gagal membuat user'
            ], 500);
        }
    }

    /**
     * Show - Detail User
     */
    public function show(User $user)
    {
        return response()->json([
            'status' => true,
            'data'   => $user->load('bagian')
        ]);
    }

    /**
     * Update - Edit User (dengan foto)
     */
    /**
 * Update - Edit User (dengan foto)
 */
/**
 * Update - Edit User (dengan foto)
 */
public function update(Request $request, User $user)
{
    try {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'sometimes|nullable|email|unique:users,email,' . $user->id,
            'password'      => 'nullable|string|min:6',
            'role'          => 'sometimes|in:admin,karyawan,super',
            'bagian_id'     => 'nullable|exists:bagian,id',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        // Handle foto
        $validated = $this->handleFileUpload($request, $user, $validated);

        // FORCE update name (kadang model tidak mendeteksi perubahan)
        $user->name = $validated['name'];

        // Update semua field
        $user->update($validated);

        Log::info('User Updated Successfully', [
            'user_id' => $user->id,
            'name_before' => $user->getOriginal('name'),
            'name_after'  => $user->name,
            'has_new_photo' => $request->hasFile('profile_photo'),
            'fields_received' => $request->all()
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'User berhasil diperbarui',
            'data'    => $user->fresh()->load('bagian')
        ]);

    } catch (ValidationException $e) {
        Log::warning('User Update Validation Failed', [
            'user_id' => $user->id,
            'errors'  => $e->errors(),
            'received_fields' => $request->all()
        ]);
        return response()->json([
            'status' => false,
            'message' => 'Validasi gagal',
            'errors'  => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('User Update Error: ' . $e->getMessage(), ['user_id' => $user->id]);
        return response()->json([
            'status'  => false,
            'message' => 'Gagal memperbarui user'
        ], 500);
    }
}
    /**
     * Destroy - Hapus User + Foto
     */
    public function destroy(User $user)
    {
        try {
            // Hapus foto jika ada
            if ($user->profile_photo_path) {
                $path = public_path('images/profile/' . $user->profile_photo_path);
                if (File::exists($path)) {
                    File::delete($path);
                }
            }

            $user->delete();

            return response()->json([
                'status'  => true,
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('User Destroy Error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghapus user'
            ], 500);
        }
    }

    /**
     * Handle Upload Foto Profil
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

            // Hapus foto lama jika ada
            if ($user->profile_photo_path && File::exists($directory . '/' . $user->profile_photo_path)) {
                File::delete($directory . '/' . $user->profile_photo_path);
            }

            $validated['profile_photo_path'] = $filename;
        } else {
            // Pertahankan foto lama
            $validated['profile_photo_path'] = $user->profile_photo_path;
        }

        return $validated;
    }
}

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
                'bagian_id' => 'nullable|exists:bagian,id', // Opsional
                'role' => 'required|in:super,admin,karyawan', // Diperlukan untuk create
                'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            // Enkripsi password
            $validated['password'] = Hash::make($validated['password']);

            // Simpan user terlebih dahulu
            $user = User::create($validated);

            // Upload foto profil jika ada
            $this->handleFileUpload($request, $user);

            // Redirect berdasarkan role user yang login
            $redirectRoute = optional(Auth::user())->role === 'admin'
                ? 'admin.index'
                : 'permintaan.index';

            Log::info('User created successfully', ['user_id' => $user->id, 'email' => $user->email]);

            return redirect()->route($redirectRoute)->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in store method: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'request' => $request->all()]);
            return redirect()->back()->with('error', 'Gagal membuat user. Silakan coba lagi.');
        }
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
        try {
            // Log data request untuk debugging
            Log::info('Update request received', ['request' => $request->all(), 'user_id' => $user->id]);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:6',
                'bagian_id' => 'nullable|exists:bagian,id', // Opsional
                'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                // 'role' dihilangkan dari required karena tidak selalu diupdate di form
            ]);

            // Update password jika diisi
            if ($request->filled('password')) {
                $validated['password'] = Hash::make($request->password);
            } else {
                unset($validated['password']);
            }

            // Upload foto baru jika ada
            $this->handleFileUpload($request, $user, $validated);

            $user->update($validated);

            // Redirect berdasarkan role user yang login
            $redirectRoute = optional(Auth::user())->role === 'admin'
                ? 'admin.index'
                : 'permintaan.index';

            Log::info('User updated successfully', ['user_id' => $user->id, 'email' => $user->email]);

            return redirect()->route($redirectRoute)->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error in update method: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'request' => $request->all()]);
            return redirect()->back()->with('error', 'Gagal memperbarui user. Silakan coba lagi.');
        }
    }

    /**
     * Hapus user dan foto profil terkait.
     */
    public function destroy(User $user)
    {
        try {
            // Hapus file foto jika ada
            if ($user->profile_photo_path && file_exists(public_path('img/profile_photo/' . $user->profile_photo_path))) {
                unlink(public_path('img/profile_photo/' . $user->profile_photo_path));
            }

            $user->delete();
            Log::info('User deleted successfully', ['user_id' => $user->id]);

            return redirect()->route('admin.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error in destroy method: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal menghapus user. Silakan coba lagi.');
        }
    }

    /**
     * Menghandle upload foto profil user.
     *
     * @param Request $request
     * @param User $user
     * @param array $validated (opsional untuk update)
     * @return void
     */
    private function handleFileUpload(Request $request, User $user, array $validated = [])
    {
        try {
            if ($request->hasFile('profile_photo')) {
                Log::info('Photo upload initiated', ['user_id' => $user->id]);

                // Pastikan direktori ada
                $directory = public_path('img/profile_photo');
                if (!File::isDirectory($directory)) {
                    File::makeDirectory($directory, 0755, true);
                    Log::info('Directory created', ['path' => $directory]);
                }

                $file = $request->file('profile_photo');
                $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
                $filePath = $directory . '/' . $filename;

                Log::info('Attempting to move file', ['filename' => $filename, 'path' => $filePath]);

                // Pindahkan file
                $file->move($directory, $filename);

                // Verifikasi file tersimpan
                if (File::exists($filePath)) {
                    Log::info('File moved successfully', ['path' => $filePath]);

                    // Hapus foto lama jika ada
                    if ($user->profile_photo_path && File::exists($directory . '/' . $user->profile_photo_path)) {
                        File::delete($directory . '/' . $user->profile_photo_path);
                        Log::info('Old photo deleted', ['old_path' => $directory . '/' . $user->profile_photo_path]);
                    }

                    // Perbarui path foto
                    $user->profile_photo_path = $filename;
                    $user->save();
                    Log::info('Photo path updated', ['new_path' => $filename]);
                } else {
                    Log::error('File move failed', ['path' => $filePath]);
                    throw new \Exception('Gagal menyimpan file.');
                }
            } elseif (!isset($validated['profile_photo_path']) && $user->profile_photo_path) {
                // Pertahankan foto lama jika tidak ada upload baru
                $validated['profile_photo_path'] = $user->profile_photo_path;
                Log::info('Retaining old photo', ['path' => $user->profile_photo_path]);
            }
        } catch (\Exception $e) {
            Log::error('Error in handleFileUpload: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e; // Melempar error ke metode pemanggil untuk penanganan lebih lanjut
        }
    }
}

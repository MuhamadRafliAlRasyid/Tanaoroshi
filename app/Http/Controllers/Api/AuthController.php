<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Spareparts;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required|string',
            ]);

            // Ambil spareparts_id dari berbagai kemungkinan (QR Code Flow)
            $sparepartsHashid = $request->input('spareparts_id')
                             ?? $request->query('spareparts_id')
                             ?? $request->route('spareparts_hashid')   // dari route /login/{spareparts_hashid}
                             ?? $request->header('X-Spareparts-Id');

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Email atau password salah'
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('mobile_app')->plainTextToken;

            Log::info('Mobile Login Success: ' . $user->email .
                      ' | Role: ' . ($user->role ?? 'No role') .
                      ' | Spareparts HashID: ' . ($sparepartsHashid ?? 'None'));

            $response = [
                'status'  => true,
                'message' => 'Login berhasil',
                'token'   => $token,
                'user'    => $user->only(['id', 'hashid', 'name', 'email', 'role', 'bagian_id']),
            ];

            // ==================== QR CODE FLOW ====================
            if ($sparepartsHashid) {
                // Optional: validasi apakah sparepart benar ada
                $sparepartExists = Spareparts::where('hashid', $sparepartsHashid)->exists();

                $response['spareparts_id']     = $sparepartsHashid;
                $response['next_screen']       = 'pengambilan.create';
                $response['should_open_form']  = true;
                $response['sparepart_exists']  = $sparepartExists;
                $response['message']           = $sparepartExists
                    ? 'Login berhasil. Silakan lanjutkan pengambilan sparepart.'
                    : 'Login berhasil. Sparepart tidak ditemukan, tapi bisa dilanjutkan.';
            }
            // ==================== NORMAL LOGIN ====================
            else {
                $response['next_screen'] = match ($user->role) {
                    'super'    => 'super.dashboard',
                    'admin'    => 'admin.dashboard',
                    'karyawan' => 'karyawan.dashboard',
                    default    => 'home',
                };
                $response['should_open_form'] = false;
            }

            return response()->json($response);

        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Mobile Login Error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat login'
            ], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name'      => 'required|string|max:255',
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required|string|min:6',
                'role'      => 'required|in:admin,karyawan',
                'bagian_id' => 'nullable|exists:bagian,id',
            ]);

            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => $request->role,
                'bagian_id' => $request->bagian_id,
            ]);

            $token = $user->createToken('mobile_app')->plainTextToken;

            return response()->json([
                'status'  => true,
                'message' => 'Registrasi berhasil',
                'token'   => $token,
                'user'    => $user->only(['id', 'hashid', 'name', 'email', 'role', 'bagian_id'])
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Register Error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat registrasi'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Logout berhasil'
            ]);
        } catch (\Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Gagal logout'
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'user'   => $request->user()->only(['id', 'hashid', 'name', 'email', 'role', 'bagian_id', 'profile_photo_path', 'profile_photo_url'])
        ]);
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            $request->validate([
                'name'     => 'sometimes|string|max:255',
                'password' => 'sometimes|nullable|string|min:6',
            ]);

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->name = $request->name ?? $user->name;
            $user->save();

            return response()->json([
                'status'  => true,
                'message' => 'Profil berhasil diperbarui',
                'user'    => $user->only(['id', 'hashid', 'name', 'email', 'role', 'bagian_id'])
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Update Profile Error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Gagal memperbarui profil'
            ], 500);
        }
    }
}

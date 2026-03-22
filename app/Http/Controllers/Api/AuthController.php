<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
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

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Email atau password salah'
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('mobile_app')->plainTextToken;

            return response()->json([
                'status'  => true,
                'message' => 'Login berhasil',
                'token'   => $token,
                'user'    => $user->only(['id', 'name', 'email', 'role', 'bagian_id', 'hashid'])
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
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
                'user'    => $user->only(['id', 'name', 'email', 'role', 'bagian_id', 'hashid'])
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
                'message' => 'Gagal melakukan logout'
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'user'   => $request->user()->only(['id', 'name', 'email', 'role', 'bagian_id', 'hashid'])
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
                'user'    => $user->only(['id', 'name', 'email', 'role', 'bagian_id', 'hashid'])
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
                'status' => false,
                'message' => 'Gagal memperbarui profil'
            ], 500);
        }
    }
}

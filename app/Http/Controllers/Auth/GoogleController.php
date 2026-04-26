<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            Log::info('GOOGLE USER:', (array) $googleUser);

            if (!$googleUser->getEmail()) {
                return redirect('/login')->with('error', 'Email Google tidak tersedia');
            }

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName() ?? 'User Google',
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(16)),
                    'role' => 'karyawan',
                    'bagian_id' => null,
                    'profile_photo_path' => $googleUser->getAvatar(),
                ]);

                Log::info('USER CREATED:', $user->toArray());
            }

            Auth::login($user);

            Log::info('LOGIN SUCCESS USER ID: ' . $user->id);

            return match ($user->role) {
                'super' => redirect('/super/dashboard'),
                'admin' => redirect('/admin/dashboard'),
                'karyawan' => redirect('/karyawan/dashboard'),
                default => redirect('/'),
            };

        } catch (\Exception $e) {
            Log::error('Google Login Error:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return redirect('/login')->with('error', 'Login Google gagal');
        }
    }
}

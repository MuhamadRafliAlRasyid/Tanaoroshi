@extends('layouts.guest')

@section('title', 'Login - Tanaoroshi')

@section('content')
    <div
        class="min-h-screen bg-gradient-to-br from-white via-amber-50/30 to-amber-100/40 flex items-center justify-center p-6">
        <div
            class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden border border-amber-200/60">

            <!-- KIRI: PENJELASAN APLIKASI - Gradasi Putih & Warna Logo -->
            <div
                class="relative bg-gradient-to-br from-white to-amber-50 p-12 lg:p-16 text-gray-800 flex flex-col justify-center">
                <!-- Lingkaran dekoratif warna logo -->
                <div class="absolute -top-20 -left-20 w-64 h-64 bg-amber-200/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-16 -right-16 w-48 h-48 bg-blue-200/20 rounded-full blur-3xl"></div>

                <div class="relative max-w-md">
                    <!-- Logo Baru -->
                    <div class="flex items-center gap-5 mb-10">
                        <div
                            class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800/90 backdrop-blur-sm p-3 rounded-2xl shadow-md border border-amber-200/50">
                            <img src="https://perindag.slemankab.go.id/wp-content/uploads/2025/09/Logo-Metrologi-Diedit.png"
                                alt="Logo Tanaoroshi" class="w-20 h-20 object-contain">
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold tracking-tight text-gray-800">Tanaoroshi</h1>
                            <p class="text-amber-700 text-lg font-medium">Sistem Inventaris</p>
                        </div>
                    </div>

                    <h2 class="text-3xl font-semibold leading-tight mb-6 text-gray-800">
                        Kelola Inventaris Sparepart & Alat<br>
                        <span class="text-amber-600">Disperindag Kabupaten Karawang</span>
                    </h2>

                    <p class="text-gray-600 text-lg leading-relaxed mb-10">
                        Aplikasi modern untuk mencatat pengambilan, pengembalian, purchase request,
                        serta memantau stok secara real-time dengan dukungan scan QR Code.
                    </p>

                    <!-- Fitur dengan ikon -->
                    <div class="grid grid-cols-2 gap-5 text-sm">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-6 h-6 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 text-amber-600">
                                ✅</div>
                            <div>
                                <p class="font-semibold text-gray-700">Stok Real‑time</p>
                                <p class="text-gray-500 dark:text-gray-400 text-xs">Update otomatis</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div
                                class="w-6 h-6 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 text-amber-600">
                                📱</div>
                            <div>
                                <p class="font-semibold text-gray-700">QR Code Scan</p>
                                <p class="text-gray-500 dark:text-gray-400 text-xs">Pengambilan cepat</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div
                                class="w-6 h-6 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 text-amber-600">
                                📊</div>
                            <div>
                                <p class="font-semibold text-gray-700">Laporan Lengkap</p>
                                <p class="text-gray-500 dark:text-gray-400 text-xs">PDF & Excel</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div
                                class="w-6 h-6 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 text-amber-600">
                                🔒</div>
                            <div>
                                <p class="font-semibold text-gray-700">Aman & Terintegrasi</p>
                                <p class="text-gray-500 dark:text-gray-400 text-xs">Role-based access</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KANAN: FORM LOGIN - Latar putih -->
            <div
                class="p-12 lg:p-16 flex flex-col justify-center bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800">
                <div class="mb-10 text-center">
                    <h2 class="text-3xl font-bold text-gray-800">Selamat Datang Kembali</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Masuk untuk melanjutkan ke sistem</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl text-sm shadow-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="loginForm" action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                autofocus
                                class="w-full pl-11 pr-5 py-4 border border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 rounded-2xl focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition text-base placeholder-gray-400 bg-gray-50 dark:bg-gray-900 dark:bg-gray-900/50"
                                placeholder="nama@email.com">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input id="password" name="password" type="password" required
                                class="w-full pl-11 pr-12 py-4 border border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 rounded-2xl focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition text-base placeholder-gray-400 bg-gray-50 dark:bg-gray-900 dark:bg-gray-900/50"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePassword()"
                                class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-amber-600 transition">
                                <i class="fas fa-eye-slash text-xl" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tombol Login - Warna Emas dari Logo -->
                    <button type="submit"
                        class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-4 rounded-2xl transition flex items-center justify-center gap-2 text-base shadow-md shadow-amber-200 mt-4">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Masuk ke Sistem</span>
                    </button>
                </form>

                <!-- Google Login -->
                <div class="mt-8">
                    <a href="{{ route('google.redirect') }}"
                        class="w-full flex items-center justify-center gap-3 border border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 hover:border-amber-300 bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 hover:bg-amber-50/30 py-4 rounded-2xl text-sm font-medium text-gray-700 transition shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 48 48">
                            <path fill="#EA4335"
                                d="M24 9.5c3.5 0 6.7 1.2 9.2 3.6l6.9-6.9C35.7 2.1 30.2 0 24 0 14.6 0 6.4 5.5 2.5 13.5l8 6.2C12.3 13.1 17.7 9.5 24 9.5z" />
                            <path fill="#4285F4"
                                d="M46.1 24.5c0-1.6-.1-2.7-.4-3.9H24v7.4h12.7c-.3 1.8-1.8 4.6-5.1 6.5l7.8 6c4.6-4.3 7.3-10.5 7.3-16z" />
                            <path fill="#FBBC05"
                                d="M10.5 28.3c-.6-1.8-.9-3.6-.9-5.3s.3-3.5.9-5.3l-8-6.2C.9 14.6 0 18.2 0 22s.9 7.4 2.5 10.5l8-6.2z" />
                            <path fill="#34A853"
                                d="M24 48c6.2 0 11.4-2 15.2-5.5l-7.8-6c-2.1 1.5-4.8 2.5-7.4 2.5-6.3 0-11.7-3.6-13.5-8.9l-8 6.2C6.4 42.5 14.6 48 24 48z" />
                        </svg>
                        <span>Masuk dengan Google</span>
                    </a>
                </div>

                <!-- Footer -->
                <div class="text-center mt-8 text-gray-400 text-xs">
                    © 2026 Dinas Industri dan Perdagangan Kabupaten Karawang
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePassword() {
            const pass = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pass.type === "password") {
                pass.type = "text";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                pass.type = "password";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>
@endpush

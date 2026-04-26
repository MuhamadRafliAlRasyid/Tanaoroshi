@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="container">
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <div class="flex items-center border border-gray-200 rounded-lg px-4 py-2">
                <img alt="Logo" class="w-33 h-18" src="{{ asset('images/logo.jpg') }}" />
            </div>
        </div>

        @if ($errors->any())
            <div
                class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded-lg shadow-md text-center animate-fade-in">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="loginForm" action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div class="text-center">
                <h2 class="text-xl font-bold text-indigo-800 mb-1">Selamat Datang Kembali</h2>
                <p class="text-gray-600 text-xs">Silakan masuk untuk melanjutkan</p>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email" class="block text-xs font-medium text-gray-700 mb-1">Alamat Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                    class="w-full border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-400 transition duration-200">
                <span class="error text-red-500 text-xs mt-1 hidden" id="emailError">Email tidak valid.</span>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="block text-xs font-medium text-gray-700 mb-1">Kata Sandi</label>
                <div class="relative">
                    <input id="password" name="password" type="password" required
                        class="w-full border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-400 pr-9 transition duration-200">
                    <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 cursor-pointer"
                        onclick="togglePasswordVisibility()">
                        <i class="fas fa-eye-slash text-sm" id="eyeIcon"></i>
                    </span>
                    <span class="error text-red-500 text-xs mt-1 hidden" id="passwordError">Kata sandi minimal 6
                        karakter.</span>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full bg-indigo-600 text-white font-semibold py-1.5 rounded-md shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition flex items-center justify-center space-x-2 text-sm">
                <i class="fas fa-sign-in-alt"></i>
                <span>Masuk</span>
            </button>
            <div class="flex items-center my-4">
                <div class="flex-grow border-t border-gray-300"></div>
                <span class="mx-2 text-gray-400 text-xs">ATAU</span>
                <div class="flex-grow border-t border-gray-300"></div>
            </div>

            <a href="{{ route('google.redirect') }}"
                class="w-full flex items-center justify-center gap-2 border border-gray-300 py-2 rounded-md text-sm font-medium hover:bg-gray-100 transition">

                <!-- ICON GOOGLE -->
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
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            eyeIcon.classList.toggle('fa-eye-slash');
            eyeIcon.classList.toggle('fa-eye');
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let isValid = true;

            // Reset errors
            document.querySelectorAll('.error').forEach(error => error.classList.add('hidden'));
            const errorContainer = document.querySelector('.bg-red-100');
            if (errorContainer) errorContainer.classList.remove('animate-fade-in');

            // Validasi Email
            const email = document.getElementById('email').value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                document.getElementById('emailError').classList.remove('hidden');
                isValid = false;
            }

            // Validasi Password
            const password = document.getElementById('password').value;
            if (password.length < 6) {
                document.getElementById('passwordError').classList.remove('hidden');
                isValid = false;
            }

            if (isValid) {
                this.submit();
            } else if (errorContainer) {
                errorContainer.classList.add('animate-fade-in');
            }
        });
    </script>
@endpush

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.5s ease-in;
    }
</style>

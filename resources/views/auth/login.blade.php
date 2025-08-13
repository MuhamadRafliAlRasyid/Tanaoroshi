@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="container">
        @if ($errors->any())
            <div
                class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg transition duration-300 ease-in-out">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="loginForm" action="{{ route('login') }}" method="POST" class="space-y-6">
            @csrf
            <h2 class="text-center text-2xl font-semibold text-gray-800 mb-6">Selamat Datang Kembali</h2>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                    class="focus:placeholder-transparent">
                <span class="error" id="emailError">Email tidak valid.</span>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <div class="relative">
                    <input id="password" name="password" type="password" required
                        class="focus:placeholder-transparent pr-10">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm cursor-pointer"
                        onclick="togglePasswordVisibility()">
                        <i class="fas fa-eye-slash" id="eyeIcon"></i>
                    </span>
                </div>
                <span class="error" id="passwordError">Kata sandi minimal 6 karakter.</span>
            </div>

            <!-- Remember & Forgot -->
            {{-- <div class="flex items-center justify-between text-sm text-gray-700">
                <label class="flex items-center space-x-2">
                    <input name="remember" type="checkbox" class="w-4 h-4 border-gray-400 rounded">
                    <span>Ingat Saya</span>
                </label>
                <a href="/forgot-password" class="text-indigo-600 hover:underline">Lupa Kata Sandi?</a>
            </div> --}}

            <!-- Submit -->
            <button type="submit">Masuk</button>

            <!-- Register Link -->
            <div class="links">
                <a href="{{ route('register') }}">Belum punya akun? Daftar</a>
            </div>
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
            document.querySelectorAll('.error').forEach(error => error.style.display = 'none');
            const errorContainer = document.querySelector('.bg-red-100');
            if (errorContainer) errorContainer.classList.add('hidden');

            // Validasi Email
            const email = document.getElementById('email').value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                document.getElementById('emailError').style.display = 'block';
                isValid = false;
            }

            // Validasi Password
            const password = document.getElementById('password').value;
            if (password.length < 6) {
                document.getElementById('passwordError').style.display = 'block';
                isValid = false;
            }

            if (isValid) {
                this.submit();
            } else if (errorContainer) {
                errorContainer.classList.remove('hidden');
            }
        });
    </script>
@endpush

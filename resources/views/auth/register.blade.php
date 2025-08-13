@extends('layouts.guest')

@section('title', 'Register')

@section('content')
    <div class="container">
        <div>
            <h2 class="text-center text-2xl font-semibold text-gray-800 mb-4">Daftar Akun Baru</h2>
            <p class="mt-2 text-sm text-gray-600 text-center">Bergabunglah untuk mengelola sparepart dengan mudah!</p>
        </div>

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

        <form id="registerForm" action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="space-y-6">
                <!-- Nama Lengkap -->
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="focus:placeholder-transparent">
                    <span id="nameError" class="error">Nama wajib diisi.</span>
                </div>

                <!-- Alamat Email -->
                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="focus:placeholder-transparent">
                    <span id="emailError" class="error">Email tidak valid.</span>
                </div>

                <!-- Kata Sandi -->
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="focus:placeholder-transparent pr-10 w-full">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm cursor-pointer"
                            onclick="togglePasswordVisibility('password', 'eyeIconPassword')">
                            <i class="fas fa-eye-slash" id="eyeIconPassword"></i>
                        </span>
                    </div>
                    <span id="passwordError" class="error">Kata sandi minimal 6 karakter.</span>
                </div>

                <!-- Konfirmasi Kata Sandi -->
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="focus:placeholder-transparent pr-10 w-full">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm cursor-pointer"
                            onclick="togglePasswordVisibility('password_confirmation', 'eyeIconConfirm')">
                            <i class="fas fa-eye-slash" id="eyeIconConfirm"></i>
                        </span>
                    </div>
                    <span id="confirmPasswordError" class="error">Konfirmasi kata sandi tidak cocok.</span>
                </div>

                <!-- Peran -->
                <div class="form-group">
                    <label for="role">Peran</label>
                    <select id="role" name="role" required>
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Pengguna</option>
                        <option value="spv" {{ old('role') == 'spv' ? 'selected' : '' }}>Supervisor</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <!-- Bagian -->
                <div class="form-group">
                    <label for="bagian_id">Bagian</label>
                    <select id="bagian_id" name="bagian_id">
                        <option value="">Pilih Bagian</option>
                        @forelse ($bagians as $bagian)
                            <option value="{{ $bagian->id }}" {{ old('bagian_id') == $bagian->id ? 'selected' : '' }}>
                                {{ $bagian->nama }}</option>
                        @empty
                            <option disabled>Tidak ada bagian tersedia</option>
                        @endforelse
                    </select>
                </div>

                <!-- Foto Profil -->
                <div class="form-group">
                    <label for="profile_photo">Foto Profil</label>
                    <input type="file" id="profile_photo" name="profile_photo" class="file-input">
                    <span id="photoError" class="error">Format file harus JPG, JPEG, atau PNG (maks. 2MB). File
                        opsional.</span>
                </div>
            </div>

            <button type="submit">Daftar Sekarang</button>
            <div class="links">
                <a href="{{ route('login') }}">Sudah punya akun? Login</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            eyeIcon.classList.toggle('fa-eye-slash');
            eyeIcon.classList.toggle('fa-eye');
        }

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let isValid = true;

            // Validasi Nama
            const name = document.getElementById('name').value.trim();
            if (!name) {
                document.getElementById('nameError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('nameError').style.display = 'none';
            }

            // Validasi Email
            const email = document.getElementById('email').value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                document.getElementById('emailError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('emailError').style.display = 'none';
            }

            // Validasi Password
            const password = document.getElementById('password').value;
            if (password.length < 6) {
                document.getElementById('passwordError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('passwordError').style.display = 'none';
            }

            // Validasi Konfirmasi Password
            const confirmPassword = document.getElementById('password_confirmation').value;
            if (password !== confirmPassword) {
                document.getElementById('confirmPasswordError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('confirmPasswordError').style.display = 'none';
            }

            // Validasi Foto
            const photo = document.getElementById('profile_photo').files[0];
            if (photo && (photo.size > 2 * 1024 * 1024 || !['image/jpeg', 'image/png', 'image/jpg'].includes(photo
                    .type))) {
                document.getElementById('photoError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('photoError').style.display = 'none';
            }

            if (isValid) {
                this.submit();
            }
        });
    </script>
@endpush

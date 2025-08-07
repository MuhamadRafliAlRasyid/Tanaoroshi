@extends('layouts.guest')

@section('title', 'Register')

@section('content')
    <h2>Daftar Akun Baru</h2>
    <p>Bergabunglah untuk mengelola sparepart dengan mudah!</p>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="registerForm" action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            <span class="error" id="nameError">Nama wajib diisi.</span>
        </div>

        <div class="form-group">
            <label for="email">Alamat Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            <span class="error" id="emailError">Email tidak valid.</span>
        </div>

        <div class="form-group">
            <label for="password">Kata Sandi</label>
            <input type="password" id="password" name="password" required>
            <span class="error" id="passwordError">Kata sandi minimal 6 karakter.</span>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Kata Sandi</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
            <span class="error" id="confirmPasswordError">Konfirmasi kata sandi tidak cocok.</span>
        </div>

        <div class="form-group">
            <label for="role">Peran</label>
            <select id="role" name="role" required>
                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Pengguna</option>
                <option value="spv" {{ old('role') == 'spv' ? 'selected' : '' }}>Supervisor</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <div class="form-group">
            <label for="bagian_id">Bagian</label>
            <select id="bagian_id" name="bagian_id">
                <option value="">Pilih Bagian</option>
                @foreach ($bagians as $bagian)
                    <option value="{{ $bagian->id }}" {{ old('bagian_id') == $bagian->id ? 'selected' : '' }}>
                        {{ $bagian->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="profile_photo">Foto Profil</label>
            <input type="file" id="profile_photo" name="profile_photo" class="file-input">
            <span class="error" id="photoError">Format file harus JPG, JPEG, atau PNG (maks. 2MB).</span>
        </div>

        <button type="submit">Daftar Sekarang</button>
        <div class="links">
            <a href="{{ route('login') }}">Sudah punya akun? Login</a>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let isValid = true;

            // Validasi Nama
            const name = document.getElementById('name').value;
            if (!name) {
                document.getElementById('nameError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('nameError').style.display = 'none';
            }

            // Validasi Email
            const email = document.getElementById('email').value;
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register - BUHINCORE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.jpg') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background: url('{{ asset('images/bg.jpg') }}') no-repeat center center fixed;
            background-size: cover;
        }

        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        input,
        select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ecf0f1;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #3498db;
        }

        .file-input {
            padding: 0.5rem 0;
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        button:hover {
            background: #2980b9;
            transform: scale(1.05);
        }

        .links {
            text-align: center;
            margin-top: 1rem;
        }

        .links a {
            color: #3498db;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .links a:hover {
            color: #2980b9;
        }

        .error {
            color: #e74c3c;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="w-full max-w-md">
            <div class="flex justify-center mb-10">
                <div class="flex items-center border border-gray-200 rounded-md px-3 py-2">
                    <img alt="Logo" class="w-6 h-6" src="{{ asset('images/logo.jpg') }}" />
                    <span class="ml-2 text-xs font-semibold text-black">BUHINCORE</span>
                </div>
            </div>

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
                            <option value="{{ $bagian->id }}"
                                {{ old('bagian_id') == $bagian->id ? 'selected' : '' }}>
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
        </div>

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
</body>

</html>

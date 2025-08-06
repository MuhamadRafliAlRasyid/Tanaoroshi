<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - BUHINCORE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="images/logo daihatsu.jpg">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background: url('images/bg.jpg');
            background-size: cover;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="flex justify-center mb-10">
            <div class="flex items-center border border-gray-200 rounded-md px-3 py-2">
                <img alt="Logo" class="w-25 h-25" src="images/logo.jpg" />
            </div>
        </div>


        <!-- Error Validation -->
        <div id="errorContainer" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hidden">
            <ul id="errorList" class="list-disc list-inside text-sm"></ul>
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Login Form -->
        <form id="loginForm" action="{{ route('login') }}" method="POST"
            class="space-y-5 bg-white p-6 rounded-lg shadow-lg">
            @csrf
            <h2 class="text-center text-xl font-semibold text-gray-800 mb-4">Selamat Datang Kembali</h2>

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-normal text-gray-700 mb-1">Alamat Email</label>
                <div class="relative">
                    <input id="email" name="email" type="email" value="" required autofocus
                        class="w-full border border-gray-300 rounded-md px-3 py-2 pr-10 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" />
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-indigo-500 text-sm">
                        <i class="fas fa-check-circle"></i>
                    </span>
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-xs font-normal text-gray-700 mb-1">Kata Sandi</label>
                <div class="relative">
                    <input id="password" name="password" type="password" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 pr-10 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" />
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm cursor-pointer"
                        onclick="togglePasswordVisibility()">
                        <i class="fas fa-eye-slash" id="eyeIcon"></i>
                    </span>
                </div>
            </div>

            <!-- Remember & Forgot -->
            <div class="flex items-center justify-between text-xs text-gray-700">
                <label class="flex items-center space-x-2">
                    <input name="remember" type="checkbox" class="w-3.5 h-3.5 border border-gray-400 rounded-sm" />
                    <span>Ingat Saya</span>
                </label>
                <a class="text-indigo-600 hover:underline" href="/forgot-password">Lupa Kata Sandi?</a>
            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md py-2 transition duration-200">
                Masuk
            </button>

            <!-- Register Link -->
            <a href={{ route('register') }} class="block text-center mt-4 text-indigo-600 text-sm hover:underline">Belum
                punya
                akun? Daftar</a>
        </form>
    </div>

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
            const errorContainer = document.getElementById('errorContainer');
            const errorList = document.getElementById('errorList');

            // Reset errors
            errorContainer.classList.add('hidden');
            errorList.innerHTML = '';

            // Validasi Email
            const email = document.getElementById('email').value;
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                errorList.innerHTML += '<li>Email tidak valid.</li>';
                isValid = false;
            }

            // Validasi Password
            const password = document.getElementById('password').value;
            if (password.length < 6) {
                errorList.innerHTML += '<li>Kata sandi minimal 6 karakter.</li>';
                isValid = false;
            }

            if (!isValid) {
                errorContainer.classList.remove('hidden');
            } else {
                this.submit();
            }
        });
    </script>
</body>

</html>

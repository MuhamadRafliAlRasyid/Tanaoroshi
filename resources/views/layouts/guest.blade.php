<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title') - Tanaoroshi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.jpg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    @livewireStyles

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        :root {
            --bg: #f8fafc;
            --card: rgba(255, 255, 255, 0.98);
            --text: #2c3e50;
            --label: #374151;
            --input-bg: #f8fafc;
            --input-border: #e2e8f0;
            --btn-from: #4f46e5;
            --btn-to: #3b82f6;
            --error: #ef4444;
            --shadow: rgba(0, 0, 0, 0.15);
        }

        [data-theme="dark"] {
            --bg: #0f172a;
            --card: rgba(15, 23, 42, 0.95);
            --text: #e2e8f0;
            --label: #cbd5e1;
            --input-bg: #1e293b;
            --input-border: #334155;
            --btn-from: #6366f1;
            --btn-to: #8b5cf6;
            --error: #f87171;
            --shadow: rgba(0, 0, 0, 0.3);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: url('{{ asset('images/bg.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            margin: 0;
            transition: all 0.3s ease;
        }

        /* Card */
        .container {
            background: var(--card);
            backdrop-filter: blur(12px);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px var(--shadow);
            width: 100%;
            max-width: 420px;
            transition: all 0.3s ease;
        }

        .container:hover {
            transform: translateY(-6px);
            box-shadow: 0 25px 50px var(--shadow);
        }

        /* Logo */
        .logo-wrapper {
            display: flex;
            justify-content: center;
            margin-top: -4.5rem;
            margin-bottom: 1.5rem;
        }

        .logo-container {
            background: var(--card);
            padding: 0.75rem;
            border-radius: 20px;
            box-shadow: 0 12px 25px var(--shadow);
            width: 90px;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid var(--input-bg);
        }

        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 16px;
        }

        /* Label */
        label {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--label);
        }

        label i {
            margin-right: 0.5rem;
            color: #6366f1;
        }

        /* Input */
        input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--input-border);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: var(--input-bg);
            color: var(--text);
        }

        input:focus {
            outline: none;
            border-color: #6366f1;
            background: var(--card);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
        }

        input[type="password"] {
            padding-right: 3.5rem !important;
        }

        /* Toggle */
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            font-size: 1.25rem;
            transition: color 0.2s;
            z-index: 10;
        }

        .password-toggle:hover {
            color: #6366f1;
        }

        /* Button */
        button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--btn-from), var(--btn-to));
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
            white-space: nowrap;
        }

        button:hover {
            background: linear-gradient(135deg, #4338ca, #2563eb);
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(59, 130, 246, 0.3);
        }

        button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Error */
        .error {
            color: var(--error);
            font-size: 0.8rem;
            margin-top: 0.4rem;
            display: none;
        }

        /* Text */
        .text-center h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .text-center p {
            font-size: 0.875rem;
            color: #94a3b8;
        }

        /* Links */
        .links {
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
            margin-top: 1rem;
        }

        .links a {
            color: #6366f1;
            font-weight: 500;
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }

        /* Dark Mode Toggle */
        .theme-toggle {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--card);
            border: 2px solid var(--input-border);
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 10px var(--shadow);
            transition: all 0.3s ease;
            z-index: 50;
        }

        .theme-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 15px var(--shadow);
        }

        /* Mobile */
        @media (max-width: 480px) {
            .container {
                padding: 1.5rem;
                margin: 0.5rem;
                border-radius: 16px;
            }

            .logo-wrapper {
                margin-top: -3.5rem;
            }

            .logo-container {
                width: 75px;
                height: 75px;
                padding: 0.5rem;
            }

            input,
            button {
                font-size: 1rem !important;
                padding: 0.8rem 0.9rem;
            }

            .text-center h1 {
                font-size: 1.35rem;
            }

            .links {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }

            input[type="password"] {
                padding-right: 3.25rem !important;
            }

            .password-toggle {
                right: 0.75rem !important;
                font-size: 1.15rem !important;
            }

            .theme-toggle {
                top: 0.75rem;
                right: 0.75rem;
                width: 42px;
                height: 42px;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
</head>

<body class="h-full" data-theme="light">
    <!-- Dark Mode Toggle -->
    <button class="theme-toggle" onclick="toggleTheme()">
        <i class="fas fa-moon" id="themeIcon"></i>
    </button>

    <div class="w-full max-w-md mx-auto">
        @yield('content')
    </div>

    @stack('scripts')
    @livewireScripts

    <script>
        function toggleTheme() {
            const body = document.body;
            const icon = document.getElementById('themeIcon');
            const current = body.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';

            body.setAttribute('data-theme', next);
            icon.classList.toggle('fa-moon', next === 'light');
            icon.classList.toggle('fa-sun', next === 'dark');

            localStorage.setItem('theme', next);
        }

        // Load saved theme
        document.addEventListener('DOMContentLoaded', () => {
            const saved = localStorage.getItem('theme') || 'light';
            document.body.setAttribute('data-theme', saved);
            document.getElementById('themeIcon').classList.toggle('fa-sun', saved === 'dark');
            document.getElementById('themeIcon').classList.toggle('fa-moon', saved === 'light');
        });
    </script>
</body>

</html>

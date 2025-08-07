<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title') - Tanaoroshi</title>
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

<body class="bg-white min-h-screen flex flex-col justify-center items-center">

    <div class="w-full max-w-md">
        {{-- <div class="flex justify-center mb-10">
                <div class="flex items-center border border-gray-200 rounded-md px-3 py-2">
                    <img alt="Logo" class="w-6 h-6" src="{{ asset('images/logo.jpg') }}" />
                    <span class="ml-2 text-xs font-semibold text-black">BUHINCORE</span>
                </div>
            </div> --}}

        @yield('content')
    </div>

    @stack('scripts')
</body>

</html>

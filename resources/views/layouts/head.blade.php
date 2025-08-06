<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- SEO -->
    <meta name="description" content="@yield('description', 'Sistem Informasi Sparepart')">
    <meta name="keywords" content="@yield('keywords', 'sparepart, dashboard, inventory')">
    <meta name="author" content="@yield('author', 'BuhinCore')">

    <!-- Title -->
    <title>@yield('title', 'BuhinCore App')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts (optional) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />

    <!-- Custom CSS (if needed) -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Extra Head Content -->
    @stack('head')
</head>

<body class="bg-gray-100 text-gray-900 font-sans leading-normal tracking-normal">

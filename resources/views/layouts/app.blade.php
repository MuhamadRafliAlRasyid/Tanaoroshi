<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title') - Tanaoroshi</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.jpg') }}?v={{ time() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    @livewireStyles
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-700 h-screen overflow-hidden">
    @auth
        <!-- Header Navbar -->
        <header class="bg-white shadow-sm px-6 py-3 flex items-center justify-between border-b fixed w-full z-10 h-16">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="w-10 h-10">
                <h1 class="text-lg font-bold text-gray-800">Tanaoroshi</h1>
            </div>
            <div class="w-full max-w-md mx-8">
                <form action="#" method="GET" class="relative">
                    <input type="text" name="search" placeholder="Search item"
                        class="w-full border border-gray-300 rounded-full px-4 py-2 pl-10 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </form>
            </div>
            <div class="flex items-center space-x-4">
                <i class="fas fa-bell text-gray-500 text-lg hover:text-blue-500 cursor-pointer"></i>
                <div class="flex items-center gap-2">
                    <img src="{{ asset('img/profile_photo/' . Auth::user()->profile_photo_path) }}"
                        class="w-8 h-8 rounded-full border" alt="User    Avatar">
                    <div class="text-sm">
                        <p class="font-medium text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                    </div>
                </div>
            </div>
        </header>
    @endauth

    <div class="flex pt-16 h-full">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r flex flex-col justify-between px-6 py-6 fixed h-full">
            <div>
                @auth
                    <div class="mb-10">
                        <h1 class="text-xl font-bold text-gray-700 mb-4">Dashboard</h1>
                        <ul class="space-y-4 text-gray-700">
                            @if (Auth::user()->role === 'user')
                                <li><a href="{{ route('spareparts.index') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600"><i
                                            class="fas fa-box"></i> Sparepart</a></li>
                                <li><a href="#" class="text-base flex items-center gap-2 hover:text-blue-600"><i
                                            class="fas fa-hand-holding"></i> Permintaan</a></li>
                                <li><a href="#" class="text-base flex items-center gap-2 hover:text-blue-600"><i
                                            class="fas fa-print"></i> Laporan</a></li>
                            @elseif (Auth::user()->role === 'spv')
                                <li><a href="#" class="text-base flex items-center gap-2 hover:text-blue-600"><i
                                            class="fas fa-file-alt"></i> Permintaan</a></li>
                            @elseif (Auth::user()->role === 'admin')
                                <li><a href="{{ route('spareparts.index') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600"><i
                                            class="fas fa-box"></i> Sparepart</a></li>
                                <li><a href="#" class="text-base flex items-center gap-2 hover:text-blue-600"><i
                                            class="fas fa-tasks"></i> Daftar Pengambilan Sparepart </a></li>
                                <li><a href="{{ route('admin.index') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600"><i
                                            class="fas fa-users"></i> Daftar User</a></li>
                            @endif
                        </ul>
                    </div><br><br><br><br><br><br><br><br><br><br><br><br><br>
                    <div class="text-gray-700 pt-8 border-t mt-5">
                        <h2 class="text-xl font-bold text-gray-700 mb-3">Tools</h2>
                        <ul class="space-y-4">
                            <li>
                                <a href="{{ route('admin.edit', Auth::user()->id) }}"
                                    class="text-base flex items-center gap-2 hover:text-blue-600">
                                    <i class="fas fa-user-cog"></i> Account & Security
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="text-base flex items-center gap-2 text-red-600 hover:text-red-800">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>

        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 overflow-y-auto p-8">
            @php
                $route = request()->route()->getName();
                $breadcrumbs = [
                    'barangs.index' => 'Stok Barang',
                    'permintaan.index' => 'Permintaan',
                    'admin.index' => 'Daftar Anggota',
                    'admin.edit' => 'Edit User',
                    'dashboard' => 'Dashboard',
                ];
                $breadcrumb = $breadcrumbs[$route] ?? 'Halaman';
            @endphp

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Masukan Item</h2>
                <nav class="text-sm text-gray-500">
                    Dashboard <span class="mx-1">&rsaquo;</span>
                    <span class="text-blue-600">{{ $breadcrumb }}</span>
                </nav>
            </div>

            <!-- Page Content -->
            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>

</html>

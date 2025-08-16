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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    @livewireStyles
    <style>
        body {
            font-family: 'Inter', sans-serif;
            overflow-y: auto;
        }

        .sidebar-scroll {
            height: calc(100vh - 64px);
            overflow-y: auto;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-700">
    @auth
        <!-- Header Navbar -->
        <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between border-b fixed w-full z-20 h-16">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="w-28 h-12 object-contain">
                <h1 class="text-xl font-semibold text-gray-900">Tanaoroshi</h1>
            </div>
            <div class="flex items-center space-x-4">
                <button class="text-gray-500 text-lg hover:text-blue-600 transition">
                    <i class="fas fa-bell"></i>
                </button>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="flex items-center gap-2 hover:bg-gray-100 p-2 rounded-full transition">
                        <img src="{{ asset('img/profile_photo/' . Auth::user()->profile_photo_path) }}"
                            class="w-8 h-8 rounded-full border" alt="User Avatar">
                        <div class="text-sm text-gray-700">
                            <p class="font-medium">{{ Auth::user()->name }}</p>
                            <p class="text-xs capitalize">{{ Auth::user()->role }}</p>
                        </div>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg py-2 z-10">
                        <a href="{{ route('admin.edit', Auth::user()->id) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <i class="fas fa-user-cog"></i> Account & Security
                        </a>
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-2">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex pt-16 h-screen">
            <!-- Sidebar -->
            <aside class="w-64 bg-white border-r shadow-sm fixed h-full z-10" x-data="{ open: true }">
                <div class="sidebar-scroll p-6">
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-6">Dashboard</h2>
                        <ul class="space-y-3 text-gray-700">
                            @if (Auth::user()->role === 'user')
                                <li><a href="{{ route('spareparts.index') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600 transition"><i
                                            class="fas fa-box"></i> Sparepart</a></li>
                                <li><a href="#"
                                        class="text-base flex items-center gap-2 hover:text-blue-600 transition"><i
                                            class="fas fa-hand-holding"></i> Permintaan</a></li>
                                <li><a href="#"
                                        class="text-base flex items-center gap-2 hover:text-blue-600 transition"><i
                                            class="fas fa-print"></i> Laporan</a></li>
                            @elseif (Auth::user()->role === 'admin')
                                <li><a href="{{ route('spareparts.index') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600 transition"><i
                                            class="fas fa-box"></i> Sparepart</a></li>
                                <li><a href="{{ route('pengambilan.index') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600 transition"><i
                                            class="fas fa-tasks"></i> Daftar Pengambilan Sparepart</a></li>
                                <li><a href="{{ route('admin.index') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600 transition"><i
                                            class="fas fa-users"></i> Daftar User</a></li>
                                <li><a href="{{ route('bagian.index') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600 transition"><i
                                            class="fas fa-users"></i> Daftar Nama Dept</a></li>
                                <li><a href="{{ route('purchase_requests.index') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600 transition"><i
                                            class="fas fa-shopping-cart"></i> Pengajuan Sparepart</a></li>
                            @elseif (Auth::user()->role === 'super')
                                <li><a href="{{ route('purchase_requests.index') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600 transition"><i
                                            class="fas fa-check-circle"></i> Approve Pengajuan Sparepart</a></li>
                                <!-- Tambahkan link lain untuk role super jika diperlukan -->
                            @endif
                        </ul>
                    </div>
                    <div class="border-t pt-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Tools</h2>
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('admin.edit', Auth::user()->id) }}"
                                    class="text-base flex items-center gap-2 hover:text-blue-600 transition">
                                    <i class="fas fa-user-cog"></i> Account & Security
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="text-base flex items-center gap-2 text-red-600 hover:text-red-800 transition">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 ml-64 p-8 overflow-y-auto">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">@yield('title')</h2>
                </div>

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    @else
        @yield('content')
    @endauth

    @livewireScripts
</body>

</html>

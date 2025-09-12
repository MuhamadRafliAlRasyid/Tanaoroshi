<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Tanaoroshi - Sistem Manajemen Sparepart" />
    <meta name="author" content="Your Company" />
    <meta name="robots" content="index, follow" />
    <title>@yield('title') - Tanaoroshi</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.jpg') }}?v={{ time() }}"
        onerror="this.src='{{ asset('images/logo.jpg') }}'">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    @livewireStyles
    <style>
        body {
            font-family: 'Inter', sans-serif;
            overflow-y: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .sidebar-scroll {
            height: calc(100vh - 64px);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #9CA3AF #F3F4F6;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: #9CA3AF;
            border-radius: 3px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: #F3F4F6;
        }

        @media (max-width: 768px) {
            .sidebar-scroll {
                height: auto;
                max-height: calc(100vh - 64px);
            }

            aside {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }

            aside.open {
                transform: translateX(0);
                position: fixed;
                top: 64px;
                bottom: 0;
                z-index: 30;
                width: 250px;
            }

            .ml-64 {
                margin-left: 0 !important;
            }

            .hamburger {
                display: block;
            }
        }

        .hamburger {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-700" x-data="{ sidebarOpen: false, notifOpen: false }">
    @auth
        <!-- Header Navbar -->
        <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between border-b fixed w-full z-20 h-16">
            <div class="flex items-center space-x-4">
                <button @click="sidebarOpen = !sidebarOpen" class="hamburger lg:hidden">
                    <i class="fas fa-bars"></i>
                </button>
                <img src="{{ asset('images/logo.jpg') }}?v={{ time() }}" alt="Logo"
                    class="w-28 h-12 object-contain" onerror="this.src='{{ asset('images/logo.jpg') }}'">
                <h1 class="text-xl font-semibold text-gray-900">Tanaoroshi</h1>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Di dalam header, ganti bagian notifikasi -->
                <div class="relative">
                    <button @click="notifOpen = !notifOpen" id="notifBell" class="relative focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if (auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                            <span
                                class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-1">{{ auth()->user()->unreadNotifications->count() }}</span>
                        @endif
                    </button>
                    <div x-show="notifOpen" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95" @click.away="notifOpen = false" id="notifDropdown"
                        class="hidden absolute right-0 mt-2 w-72 bg-white border rounded-lg shadow-lg z-50 max-h-80 overflow-y-auto">
                        <div class="p-2 font-bold border-b">Notifikasi</div>
                        <div class="max-h-64 overflow-y-auto">
                            @if (auth()->check())
                                @forelse(auth()->user()->unreadNotifications as $notif)
                                    <a href="{{ $notif->data['action_url'] }}"
                                        class="p-2 hover:bg-gray-100 border-b text-red-600">
                                        ⚠️ Peringatan: Stok **{{ $notif->data['nama_part'] }}** kritis!<br>
                                        Sisa stok: {{ $notif->data['jumlah_baru'] }} (Titik Pesanan:
                                        {{ $notif->data['titik_pesanan'] }})<br>
                                        <span class="text-blue-600">Klik untuk ajukan pembelian</span>
                                    </a>
                                @empty
                                    <div class="p-2 text-gray-500">Tidak ada notifikasi</div>
                                @endforelse
                            @endif
                        </div>
                    </div>
                </div>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="flex items-center gap-2 hover:bg-gray-100 p-2 rounded-full transition" aria-label="User Menu"
                        aria-haspopup="true" aria-expanded="open">
                        <img src="{{ asset('img/profile_photo/' . Auth::user()->profile_photo_path) }}"
                            class="w-8 h-8 rounded-full border" alt="User Avatar"
                            onerror="this.src='{{ asset('images/avatar.png') }}'">
                        <div class="text-sm text-gray-700">
                            <p class="font-medium">{{ Auth::user()->name }}</p>
                            <p class="text-xs capitalize">{{ Auth::user()->role }}</p>
                        </div>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95" @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg py-2 z-10"
                        role="menu">
                        <a href="{{ route('admin.edit', Auth::user()->id) }}"
                            class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2"
                            role="menuitem">
                            <i class="fas fa-user-cog"></i> Account & Security
                        </a>
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-2"
                            role="menuitem">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex pt-16 h-screen" :class="{ 'ml-64': !sidebarOpen }">
            <!-- Sidebar -->
            <aside class="w-64 bg-white border-r shadow-sm fixed h-full z-10 transition-transform duration-300"
                x-bind:class="{ 'transform -translate-x-full': !sidebarOpen }" x-data="{ open: true }">
                <div class="sidebar-scroll p-6">
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-6">Dashboard</h2>
                        <ul class="space-y-3 text-gray-700">
                            @if (Auth::user()->role === 'user')
                                <li><a href="{{ route('spareparts.index') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600 transition"><i
                                            class="fas fa-box"></i> Sparepart</a></li>
                                <li><a href="{{ route('pengambilan.create') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600 transition"><i
                                            class="fas fa-hand-holding"></i> Permintaan</a></li>
                                <li><a href="{{ route('pengambilan.exportpdf') }}"
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
                            @elseif (Auth::user()->role === 'karyawan')
                                <li><a href="{{ route('pengambilan.index') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600 transition"><i
                                            class="fas fa-tasks"></i> Daftar Pengambilan Sparepart</a></li>
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
            <main class="flex-1 p-8 overflow-y-auto" :class="{ 'ml-0': !sidebarOpen, 'ml-64': sidebarOpen }">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">@yield('title')</h2>
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    @else
        @yield('content')
    @endauth

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const notifBell = document.getElementById('notifBell');
            const notifDropdown = document.getElementById('notifDropdown');

            notifBell.addEventListener('click', (e) => {
                e.stopPropagation();
                notifDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
                if (!notifBell.contains(e.target) && !notifDropdown.contains(e.target)) {
                    notifDropdown.classList.add('hidden');
                }
            });
        });
    </script>
</body>

</html>

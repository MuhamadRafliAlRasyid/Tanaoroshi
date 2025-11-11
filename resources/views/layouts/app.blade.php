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

        /* Mobile Sidebar */
        @media (max-width: 1023px) {
            .sidebar {
                position: fixed;
                top: 64px;
                left: 0;
                width: 256px;
                height: calc(100vh - 64px);
                z-index: 40;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
            }

            .hamburger {
                display: block;
            }

            .overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 30;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s;
            }

            .overlay.show {
                opacity: 1;
                visibility: visible;
            }

            /* Sembunyikan teks di mobile */
            .hide-on-mobile {
                display: none;
            }

            .logo-mobile {
                width: 32px;
                height: 32px;
            }
        }

        /* Desktop */
        @media (min-width: 1024px) {
            .sidebar {
                position: fixed;
                top: 64px;
                left: 0;
                width: 256px;
                height: calc(100vh - 64px);
                transform: translateX(0) !important;
            }

            .main-content {
                margin-left: 256px;
            }

            .hamburger,
            .overlay {
                display: none !important;
            }

            .hide-on-mobile {
                display: block;
            }

            .logo-mobile {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-700" x-data="{ sidebarOpen: false, notifOpen: false, userOpen: false }">
    @auth
        <!-- Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="overlay" :class="{ 'show': sidebarOpen }"></div>

        <!-- Header Navbar -->
        <header class="bg-white shadow-sm border-b fixed w-full z-50 h-16 flex items-center px-4 lg:px-6">
            <div class="flex items-center flex-1">
                <!-- Hamburger + Logo -->
                <button @click="sidebarOpen = !sidebarOpen" class="hamburger text-gray-700 p-2 lg:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center space-x-3 ml-2 lg:ml-0">
                    <img src="{{ asset('images/logo.jpg') }}?v={{ time() }}" alt="Logo"
                        class="logo-mobile object-contain" onerror="this.src='{{ asset('images/logo.jpg') }}'">
                    <h1 class="text-lg lg:text-xl font-semibold text-gray-900 hide-on-mobile">Tanaoroshi</h1>
                </div>
            </div>

            <!-- Right Side: Notif + User -->
            <div class="flex items-center space-x-3">
                <!-- Notifikasi -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 rounded-full hover:bg-gray-100 relative">
                        <i class="fas fa-bell text-lg text-gray-700"></i>
                        @if (auth()->user()->unreadNotifications->count() > 0)
                            <span
                                class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>
                    <div x-show="open" x-transition @click.away="open = false"
                        class="absolute right-0 mt-2 w-72 bg-white border rounded-lg shadow-lg z-50 max-h-80 overflow-y-auto">
                        <div class="p-3 font-bold border-b text-sm">Notifikasi</div>
                        <div class="max-h-64 overflow-y-auto">
                            @forelse(auth()->user()->unreadNotifications as $notif)
                                <a href="{{ $notif->data['action_url'] }}"
                                    class="block p-3 hover:bg-gray-50 border-b text-sm">
                                    <span class="text-red-600 font-medium">Peringatan:</span>
                                    Stok <strong>{{ $notif->data['nama_part'] }}</strong> kritis!<br>
                                    Sisa: {{ $notif->data['jumlah_baru'] }} | Titik Pesanan:
                                    {{ $notif->data['titik_pesanan'] }}
                                    <span class="block text-blue-600 text-xs mt-1">Klik untuk ajukan</span>
                                </a>
                            @empty
                                <div class="p-3 text-gray-500 text-center text-sm">Tidak ada notifikasi</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="flex items-center gap-2 p-2 rounded-full hover:bg-gray-100 transition">
                        <img src="{{ asset('images/profile/' . Auth::user()->profile_photo_path) }}"
                            class="w-8 h-8 rounded-full border" alt="User"
                            onerror="this.src='{{ asset('images/avatar.png') }}'">
                        <span class="hide-on-mobile text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                    </button>
                    <div x-show="open" x-transition @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-white border rounded-md shadow-lg py-2 z-50">
                        <a href="{{ route('admin.edit', Auth::user()->hashid) }}"
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

        <div class="flex pt-16 min-h-screen">
            <!-- Sidebar -->
            <aside class="sidebar bg-white border-r" :class="{ 'open': sidebarOpen }">
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
                                            class="fas fa-tasks"></i> Daftar Pengambilan</a></li>
                                <li><a href="{{ route('admin.index') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600 transition"><i
                                            class="fas fa-users"></i> Daftar User</a></li>
                                <li><a href="{{ route('bagian.index') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600 transition"><i
                                            class="fas fa-building"></i> Daftar Dept</a></li>
                                <li><a href="{{ route('purchase_requests.index') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600 transition"><i
                                            class="fas fa-shopping-cart"></i> Pengajuan Sparepart</a></li>
                            @elseif (Auth::user()->role === 'super')
                                <li><a href="{{ route('purchase_requests.index') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600 transition"><i
                                            class="fas fa-check-circle"></i> Approve Pengajuan</a></li>
                            @elseif (Auth::user()->role === 'karyawan')
                                <li><a href="{{ route('pengambilan.index') }}"
                                        class="text-base flex items-center gap-2 hover:text-blue-600 transition"><i
                                            class="fas fa-tasks"></i> Daftar Pengambilan</a></li>
                            @endif
                        </ul>
                    </div>
                    <div class="border-t pt-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Tools</h2>
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('admin.edit', Auth::user()->hashid) }}"
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
            <main class="main-content flex-1 p-6 lg:p-8 overflow-y-auto bg-gray-50">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">@yield('title')</h2>
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>

                @yield('content')
            </main>
        </div>
    @else
        @yield('content')
    @endauth

    @livewireScripts
</body>

</html>

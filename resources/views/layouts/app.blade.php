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
                    <button @click="open = !open" class="p-2 rounded-full hover:bg-gray-100 relative transition-all">
                        <i class="fas fa-bell text-xl text-gray-700"></i>
                        @if (auth()->user()->unreadNotifications->count() > 0)
                            <span
                                class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-md animate-pulse"
                                style="min-width: 20px; height: 20px; padding: 0 5px;">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown Notifikasi -->
                    <div x-show="open" x-transition @click.away="open = false"
                        class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-50 to-pink-50 p-3 border-b">
                            <h3 class="font-bold text-sm text-red-800">Notifikasi Sistem</h3>
                            @if (auth()->user()->unreadNotifications->count() > 0)
                                <p class="text-xs text-red-600 mt-1">{{ auth()->user()->unreadNotifications->count() }}
                                    notifikasi belum dibaca</p>
                            @endif
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            @php
                                // Pisahkan notifikasi berdasarkan type
                                $criticalNotifications = auth()
                                    ->user()
                                    ->unreadNotifications->filter(function ($notif) {
                                        return !isset($notif->data['type']) ||
                                            $notif->data['type'] !== 'pending_purchase_request';
                                    })
                                    ->sortBy(function ($notif) {
                                        return $notif->data['sparepart_id'] ?? 0;
                                    });

                                $prNotifications = auth()
                                    ->user()
                                    ->unreadNotifications->filter(function ($notif) {
                                        return isset($notif->data['type']) &&
                                            $notif->data['type'] === 'pending_purchase_request';
                                    })
                                    ->sortByDesc('created_at');
                            @endphp

                            <!-- Notifikasi Stok Kritis -->
                            @if ($criticalNotifications->count() > 0)
                                <div class="bg-red-50 p-2 border-b">
                                    <p class="text-xs font-semibold text-red-700">Stok Kritis</p>
                                </div>
                                @foreach ($criticalNotifications as $notif)
                                    <a href="{{ $notif->data['action_url'] ?? '#' }}"
                                        class="block p-4 hover:bg-gray-50 transition-all border-b group">
                                        <div class="flex items-start gap-3">
                                            <div class="w-2 h-2 bg-red-600 rounded-full mt-1.5 animate-pulse"></div>
                                            <div class="flex-1">
                                                <div class="flex justify-between items-start">
                                                    <p class="font-semibold text-sm text-gray-900">
                                                        {{ $notif->data['nama_part'] }}</p>
                                                    <span
                                                        class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">#{{ $notif->data['sparepart_id'] ?? 'N/A' }}</span>
                                                </div>
                                                <p class="text-xs text-gray-600 mt-1">
                                                    Stok: <span
                                                        class="font-bold text-red-600">{{ $notif->data['jumlah_baru'] }}</span>
                                                    ≤ Titik: <span
                                                        class="font-bold">{{ $notif->data['titik_pesanan'] }}</span>
                                                </p>
                                                <p
                                                    class="text-xs text-blue-600 mt-2 group-hover:underline flex items-center">
                                                    <i class="fas fa-shopping-cart mr-1"></i> Ajukan Pembelian
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ $notif->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @endif

                            <!-- Notifikasi Purchase Request -->
                            @if ($prNotifications->count() > 0)
                                <div class="bg-blue-50 p-2 border-b">
                                    <p class="text-xs font-semibold text-blue-700">Purchase Request</p>
                                </div>
                                @foreach ($prNotifications as $notif)
                                    <a href="{{ $notif->data['action_url'] ?? '#' }}"
                                        class="block p-4 hover:bg-gray-50 transition-all border-b group">
                                        <div class="flex items-start gap-3">
                                            <div class="w-2 h-2 bg-blue-600 rounded-full mt-1.5"></div>
                                            <div class="flex-1">
                                                <div class="flex justify-between items-start">
                                                    <p class="font-semibold text-sm text-gray-900">
                                                        {{ $notif->data['nama_part'] }}</p>
                                                    <span
                                                        class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">PR</span>
                                                </div>
                                                <p class="text-xs text-gray-600 mt-1">
                                                    Qty: <span class="font-bold">{{ $notif->data['quantity'] }}</span>
                                                    {{ $notif->data['satuan'] }}
                                                    • Oleh: <span
                                                        class="font-medium">{{ $notif->data['created_by'] ?? 'Unknown' }}</span>
                                                </p>
                                                <p
                                                    class="text-xs text-blue-600 mt-2 group-hover:underline flex items-center">
                                                    <i class="fas fa-eye mr-1"></i> Review Purchase Request
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ \Carbon\Carbon::parse($notif->data['created_at'] ?? $notif->created_at)->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @endif

                            @if (auth()->user()->unreadNotifications->count() === 0)
                                <div class="p-8 text-center text-gray-400">
                                    <i class="fas fa-check-circle text-3xl mb-2 text-green-500"></i>
                                    <p class="text-sm font-medium">Tidak ada notifikasi</p>
                                    <p class="text-xs mt-1">Semua sudah ditangani</p>
                                </div>
                            @endif
                        </div>

                        @if (auth()->user()->unreadNotifications->count() > 0)
                            <div class="bg-gray-50 p-3 border-t">
                                <button onclick="markAllAsRead()"
                                    class="w-full text-center text-xs text-blue-600 hover:text-blue-800 font-medium">
                                    <i class="fas fa-check-double mr-1"></i> Tandai semua sudah dibaca
                                </button>
                            </div>
                        @endif
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
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf
                        </form>
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

    <script>
        function markAllAsRead() {
            fetch('{{ route('notifications.markAllRead') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload page untuk update notifikasi
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Echo !== 'undefined') {
                Echo.channel('notifications')
                    .listen('SparepartCriticalBroadcasted', (e) => {
                        const notifBell = document.querySelector('.fa-bell');
                        const notifCount = document.querySelector('.absolute.bg-red-600');
                        let count = parseInt(notifCount?.textContent || 0);
                        notifCount ? notifCount.textContent = count + 1 : null;

                        // Optional: tampilkan toast sederhana
                        const toast = document.createElement('div');
                        toast.textContent = e.message;
                        toast.className =
                            "fixed top-4 right-4 bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-2 rounded shadow";
                        document.body.appendChild(toast);
                        setTimeout(() => toast.remove(), 4000);
                    });
            }
        });
    </script>
</body>

</html>

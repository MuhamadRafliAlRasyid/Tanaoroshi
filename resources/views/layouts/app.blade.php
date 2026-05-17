<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: $persist(false).as('darkMode') }" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Tanaoroshi - Sistem Manajemen Alat" />
    <meta name="author" content="Your Company" />
    <meta name="robots" content="index, follow" />
    <meta name="view-transition" content="same-origin" />
    <title>@yield('title') Sismalat</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logos.jpg') }}?v={{ time() }}" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        gold: '#e6a817',
                        'gold-light': '#f5c842',
                    }
                }
            }
        }
    </script>
    {{-- Alpine inti tidak perlu dimuat, Livewire sudah bawa --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    @livewireStyles
    @stack('styles')

    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed: 80px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            -webkit-font-smoothing: antialiased;
        }

        .dark body {
            background: #0f172a;
        }

        @view-transition {
            navigation: auto;
        }

        .sidebar {
            width: var(--sidebar-width);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: calc(100vh - 64px);
            position: fixed;
            top: 64px;
            left: 0;
            z-index: 40;
            background: white;
            border-right: 1px solid rgba(245, 200, 66, 0.15);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.03);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #e5e7eb #fff;
            transform: translateX(0);
        }

        .dark .sidebar {
            background: #1e293b;
            border-color: rgba(245, 200, 66, 0.1);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.2);
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        .sidebar-text,
        .chevron {
            opacity: 1;
            transition: opacity 0.2s ease;
        }

        .sidebar.collapsed .sidebar-text,
        .sidebar.collapsed .chevron {
            opacity: 0;
            width: 0;
            overflow: hidden;
            white-space: nowrap;
            pointer-events: none;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed~.main-content {
            margin-left: var(--sidebar-collapsed);
        }

        @media (max-width: 1023px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px !important;
                box-shadow: 8px 0 30px rgba(0, 0, 0, 0.1);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar.collapsed {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0 !important;
            }

            .overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.3);
                backdrop-filter: blur(4px);
                z-index: 30;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s;
            }

            .overlay.show {
                opacity: 1;
                visibility: visible;
            }
        }

        .menu-item {
            transition: all 0.2s ease;
            border-radius: 12px;
        }

        .menu-item.active {
            background: linear-gradient(135deg, rgba(245, 200, 66, 0.15), rgba(230, 168, 23, 0.08));
            border-right: 3px solid #e6a817;
            font-weight: 600;
            color: #b45309;
        }

        .dark .menu-item.active {
            background: linear-gradient(135deg, rgba(245, 200, 66, 0.2), rgba(230, 168, 23, 0.1));
            color: #fbbf24;
        }

        .menu-item:hover:not(.active) {
            background: rgba(245, 200, 66, 0.08);
        }

        .animate-fade-in {
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .bell-shake {
            animation: ring 0.5s ease-in-out;
        }

        @keyframes ring {
            0% {
                transform: rotate(0);
            }

            25% {
                transform: rotate(8deg);
            }

            50% {
                transform: rotate(-8deg);
            }

            75% {
                transform: rotate(4deg);
            }

            100% {
                transform: rotate(0);
            }
        }

        .preloader {
            position: fixed;
            inset: 0;
            background: white;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.4s, visibility 0.4s;
        }

        .dark .preloader {
            background: #0f172a;
        }

        .preloader.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .spinner {
            width: 48px;
            height: 48px;
            border: 5px solid #fde68a;
            border-top: 5px solid #e6a817;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        .collapsed-tooltip {
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.2s ease;
            position: absolute;
            left: 70px;
            background: #1e293b;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            white-space: nowrap;
            pointer-events: none;
            z-index: 100;
        }

        .collapsed-tooltip::after {
            content: '';
            position: absolute;
            top: 50%;
            left: -6px;
            transform: translateY(-50%);
            border-top: 6px solid transparent;
            border-bottom: 6px solid transparent;
            border-right: 6px solid #1e293b;
        }

        .sidebar.collapsed .menu-item:hover .collapsed-tooltip {
            visibility: visible;
            opacity: 1;
        }

        .ripple {
            position: relative;
            overflow: hidden;
        }

        .ripple::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle, currentColor 10%, transparent 10%) no-repeat center;
            transform: scale(10, 10);
            opacity: 0;
            transition: transform 0.5s, opacity 0.3s;
        }

        .ripple:active::after {
            transform: scale(0, 0);
            opacity: 0.2;
            transition: 0s;
        }

        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: #e6a817;
            z-index: 9999;
            transition: width 0.2s;
            box-shadow: 0 0 8px rgba(230, 168, 23, 0.6);
        }

        button,
        .menu-item,
        .ripple {
            transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.2s ease;
        }

        button:hover,
        .menu-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .dark button:hover,
        .dark .menu-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        button:active,
        .menu-item:active {
            transform: translateY(0);
        }

        .sidebar.collapsed .menu-item:hover {
            transform: scale(1.05);
        }

        .animate-shrink {
            animation: shrink 5s linear forwards;
        }

        @keyframes shrink {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        /* ========== DARK MODE TEXT ========== */
        .dark .animate-fade-in h1,
        .dark .animate-fade-in h2,
        .dark .animate-fade-in h3,
        .dark .animate-fade-in h4,
        .dark .animate-fade-in h5,
        .dark .animate-fade-in h6,
        .dark .animate-fade-in p,
        .dark .animate-fade-in span:not([class*="text-"]),
        .dark .animate-fade-in label:not([class*="text-"]),
        .dark .animate-fade-in td,
        .dark .animate-fade-in th,
        .dark .animate-fade-in li,
        .dark .animate-fade-in blockquote {
            color: #e2e8f0;
        }

        .dark .animate-fade-in [class*="bg-gradient"],
        .dark .animate-fade-in [class*="text-transparent"] {
            color: inherit !important;
        }

        .dark main.animate-fade-in h1,
        .dark main.animate-fade-in h2,
        .dark main.animate-fade-in h3,
        .dark main.animate-fade-in p,
        .dark main.animate-fade-in span:not([class*="text-"]),
        .dark main.animate-fade-in label:not([class*="text-"]),
        .dark main.animate-fade-in td,
        .dark main.animate-fade-in th {
            color: #e2e8f0;
        }

        /* ========== DARK MODE BACKGROUND GRADIENT ========== */
        .dark [class*="via-white"][class*="bg-gradient"] {
            --tw-gradient-via: #1e293b !important;
        }

        .dark [class*="from-amber-50"][class*="bg-gradient"] {
            --tw-gradient-from: #0f172a !important;
        }

        .dark [class*="to-orange-50"][class*="bg-gradient"] {
            --tw-gradient-to: #1e293b !important;
        }

        .dark [class*="from-amber-100"][class*="bg-gradient"] {
            --tw-gradient-from: #334155 !important;
        }

        .dark [class*="to-orange-100"][class*="bg-gradient"] {
            --tw-gradient-to: #334155 !important;
        }

        .dark [class*="bg-gradient-to-br"]:not([class*="dark:from-"]):not([class*="dark:via-"]):not([class*="dark:to-"]) {
            background-image: linear-gradient(to bottom right, #1e293b, #0f172a, #1e293b) !important;
        }

        /* ========== DARK MODE FORM INPUTS ========== */
        .dark input[type="text"],
        .dark input[type="number"],
        .dark input[type="email"],
        .dark input[type="password"],
        .dark input[type="date"],
        .dark input[type="file"],
        .dark input[type="search"],
        .dark textarea,
        .dark select {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            border-color: #4b5563 !important;
        }

        .dark input::placeholder,
        .dark textarea::placeholder {
            color: #9ca3af !important;
        }

        .dark input[type="file"]::-webkit-file-upload-button {
            background-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        /* ========== SIDEBAR DARK MODE ========== */
        .dark .sidebar a,
        .dark .sidebar button,
        .dark .sidebar span:not([class*="text-"]),
        .dark .sidebar div:not([class*="text-"]),
        .dark .sidebar .menu-item {
            color: #cbd5e1;
        }
    </style>
</head>

<body x-data="appLayout()" x-init="init()" @keydown.window="handleKeydown($event)"
    class="transition-colors duration-300 dark:bg-gray-900">

    {{-- Scroll Progress --}}
    <div class="scroll-progress" :style="'width: ' + scrollProgress + '%'"></div>

    {{-- Preloader --}}
    <div x-show="loading" x-transition.opacity.duration.400ms class="preloader">
        <div class="flex flex-col items-center">
            <img src="{{ asset('images/logos.jpg') }}" class="w-16 h-16 animate-bounce" alt="Loading">
            <div class="spinner mt-4"></div>
            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm">Memuat...</p>
        </div>
    </div>

    {{-- Toast Container --}}
    <div class="fixed top-4 right-4 z-50 space-y-3 w-80 pointer-events-none">
        <template x-for="(toast, index) in toasts" :key="index">
            <div x-show="true" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full"
                :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-red-500'"
                class="text-white px-5 py-4 rounded-xl shadow-lg flex items-start gap-3 pointer-events-auto"
                x-init="setTimeout(() => toasts.splice(index, 1), 5000)">
                <i class="fas text-xl" :class="toast.type === 'success' ? 'fa-check-circle' : 'fa-times-circle'"></i>
                <div class="flex-1">
                    <p x-text="toast.message" class="text-sm font-medium"></p>
                    <div class="w-full h-1 mt-2 bg-white/30 rounded-full overflow-hidden">
                        <div class="h-full bg-white/80 rounded-full animate-shrink"></div>
                    </div>
                </div>
                <button @click="toasts.splice(index, 1)" class="text-white/80 hover:text-white ml-2">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </template>
    </div>

    @auth
        @php $user = Auth::user(); @endphp

        {{-- Overlay mobile --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="overlay" :class="{ 'show': sidebarOpen }"></div>

        {{-- Header --}}
        <header
            class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-b border-amber-100 dark:border-gray-700 fixed w-full z-50 h-16 flex items-center px-4 lg:px-6 shadow-sm transition-colors">
            <div class="flex items-center flex-1 gap-3">
                {{-- Tombol desktop: collapse sidebar --}}
                <button @click="collapsed = !collapsed"
                    class="text-gray-500 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-700 p-2 rounded-lg transition hidden lg:block">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                {{-- Tombol mobile: toggle sidebar --}}
                <button @click="toggleSidebar()"
                    class="text-gray-500 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-700 p-2 rounded-lg transition lg:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center gap-3 ml-2">
                    <img src="{{ asset('images/logos.jpg') }}" class="w-9 h-9 lg:w-10 lg:h-10 object-contain rounded-lg"
                        alt="Logo" />
                    <h1 class="text-lg lg:text-xl font-semibold text-gray-800 dark:text-white hidden sm:block">Sistem
                        Manajemen Alat</h1>
                </div>
            </div>

            <div class="flex items-center gap-2 lg:gap-3">
                {{-- Pencarian Global --}}
                <button @click="openSearch = true"
                    class="p-2 rounded-full hover:bg-amber-50 dark:hover:bg-gray-700 transition text-gray-600 dark:text-gray-300">
                    <i class="fas fa-search"></i>
                    <span class="text-xs ml-1 hidden md:inline opacity-50">Ctrl+K</span>
                </button>
                <div x-show="openSearch" @click.away="openSearch = false" x-transition
                    class="fixed inset-0 z-50 flex items-start justify-center pt-20 bg-black/40 backdrop-blur-sm">
                    <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-2xl shadow-2xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <i class="fas fa-search text-gray-500"></i>
                            <input type="text" placeholder="Cari halaman atau data..."
                                class="w-full bg-transparent border-0 text-lg focus:outline-none dark:text-white"
                                x-ref="searchInput" x-init="$nextTick(() => $refs.searchInput.focus())" @keydown.escape="openSearch = false">
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <p class="font-medium mb-2">Menu cepat:</p>
                            <a href="{{ route('alat.index') }}" @click="openSearch = false"
                                class="block px-3 py-2 rounded-lg hover:bg-amber-50 dark:hover:bg-gray-700">Data Alat</a>
                            <a href="{{ route('pengambilan_alat.index') }}" @click="openSearch = false"
                                class="block px-3 py-2 rounded-lg hover:bg-amber-50 dark:hover:bg-gray-700">Pengambilan</a>
                            <a href="{{ route('pengembalian_alat.index') }}" @click="openSearch = false"
                                class="block px-3 py-2 rounded-lg hover:bg-amber-50 dark:hover:bg-gray-700">Pengembalian</a>
                            @if ($user->isAdminOrSuper())
                                <a href="{{ route('admin.index') }}" @click="openSearch = false"
                                    class="block px-3 py-2 rounded-lg hover:bg-amber-50 dark:hover:bg-gray-700">User</a>
                            @endif
                            <a href="{{ route('admin.edit', auth()->user()->hashid ?? '') }}" @click="openSearch = false"
                                class="block px-3 py-2 rounded-lg hover:bg-amber-50 dark:hover:bg-gray-700">Akun Saya</a>
                        </div>
                    </div>
                </div>

                {{-- Dark Mode Toggle --}}
                <button @click="darkMode = !darkMode"
                    class="p-2 rounded-full hover:bg-amber-50 dark:hover:bg-gray-700 transition text-gray-600 dark:text-gray-300">
                    <i class="fas" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
                </button>

                {{-- Notifikasi --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="p-2 rounded-full hover:bg-amber-50 dark:hover:bg-gray-700 relative transition"
                        :class="{ 'bell-shake': unreadCount > 0 && !open }">
                        <i class="fas fa-bell text-xl text-gray-600 dark:text-gray-300"></i>
                        @php $unread = auth()->user()->unreadNotifications->count(); @endphp
                        <span x-show="unreadCount > 0" :class="unreadCount > 0 && 'animate-pulse'"
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center shadow"
                            x-text="unreadCount"></span>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-amber-100 dark:border-gray-700 z-50 max-h-96 overflow-y-auto">
                        <div
                            class="p-4 border-b bg-gradient-to-r from-amber-50 to-yellow-50 dark:from-gray-700 dark:to-gray-700">
                            <h3 class="font-semibold text-base text-amber-800 dark:text-amber-200">Notifikasi</h3>
                            <p x-show="unreadCount > 0" class="text-xs text-amber-600 dark:text-amber-400 mt-1"
                                x-text="unreadCount + ' belum dibaca'"></p>
                        </div>
                        @forelse(auth()->user()->unreadNotifications->take(5) as $n)
                            <a href="{{ $n->data['action_url'] ?? '#' }}"
                                class="flex items-start gap-3 p-4 hover:bg-gray-50 dark:hover:bg-gray-700 border-b dark:border-gray-700 transition">
                                <div class="w-3 h-3 bg-red-400 rounded-full mt-1.5"></div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium dark:text-white">
                                        {{ Str::limit($n->data['nama_alat'] ?? 'Notifikasi', 35) }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="p-6 text-center text-gray-400 text-sm">Tidak ada notifikasi baru</div>
                        @endforelse
                        @if ($unread > 0)
                            <div class="p-3 bg-gray-50 dark:bg-gray-700">
                                <button @click="markAllAsRead()"
                                    class="w-full text-center text-sm text-amber-600 dark:text-amber-400 hover:text-amber-800 font-medium">
                                    Tandai semua sudah dibaca
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- User Menu --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="flex items-center gap-2 p-2 rounded-full hover:bg-amber-50 dark:hover:bg-gray-700 transition">
                        <img src="{{ asset('images/profile/' . Auth::user()->profile_photo_path) }}"
                            class="w-8 h-8 lg:w-9 lg:h-9 rounded-full border"
                            onerror="this.src='{{ asset('images/avatar.png') }}'">
                        <span
                            class="hidden sm:block text-sm font-medium text-gray-700 dark:text-gray-200">{{ Auth::user()->name }}</span>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-xl shadow-lg py-2 z-50">
                        <a href="{{ route('admin.edit', Auth::user()->hashid) }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 dark:text-white">
                            <i class="fas fa-user-cog w-5"></i> Pengaturan Akun
                        </a>
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                            <i class="fas fa-sign-out-alt w-5"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Sidebar --}}
        <aside class="sidebar" :class="{ 'open': sidebarOpen, 'collapsed': collapsed && window.innerWidth >= 1024 }">
            <nav class="p-4 space-y-1">
                <a href="{{ $user->isAdminOrSuper() ? route('admin.dashboard') : route('karyawan.dashboard') }}"
                    class="menu-item flex items-center gap-4 px-4 py-3 text-base {{ request()->routeIs('admin.dashboard', 'karyawan.dashboard') ? 'active' : 'text-gray-600 dark:text-gray-300' }} ripple">
                    <i class="fas fa-tachometer-alt w-6 h-6 text-center text-lg"></i>
                    <span class="sidebar-text">Dashboard</span>
                    <span class="collapsed-tooltip">Dashboard</span>
                </a>
                <a href="{{ route('alats.index') }}"
                    class="menu-item flex items-center gap-4 px-4 py-3 text-base {{ request()->routeIs('alat.index') ? 'active' : 'text-gray-600 dark:text-gray-300' }} ripple">
                    <i class="fas fa-tools w-6 h-6 text-center text-lg"></i>
                    <span class="sidebar-text">Data Alat</span>
                    <span class="collapsed-tooltip">Data Alat</span>
                </a>
                @if ($user->isAdminOrSuper())
                    <a href="{{ route('kategoris.index') }}"
                        class="menu-item flex items-center gap-4 px-4 py-3 text-base {{ request()->routeIs('kategori.*') ? 'active' : 'text-gray-600 dark:text-gray-300' }} ripple">
                        <i class="fas fa-tags w-6 h-6 text-center text-lg"></i>
                        <span class="sidebar-text">Kategori Alat</span>
                        <span class="collapsed-tooltip">Kategori</span>
                    </a>
                    <a href="{{ route('kalibrasis.index') }}"
                        class="menu-item flex items-center gap-4 px-4 py-3 text-base {{ request()->routeIs('kalibrasi.*') ? 'active' : 'text-gray-600 dark:text-gray-300' }} ripple">
                        <i class="fas fa-wrench w-6 h-6 text-center text-lg"></i>
                        <span class="sidebar-text">Kalibrasi</span>
                        <span class="collapsed-tooltip">Kalibrasi</span>
                    </a>
                    <a href="{{ route('alats.daftarRiwayat') }}"
                        class="menu-item flex items-center gap-4 px-4 py-3 text-base text-gray-600 dark:text-gray-300 ripple">
                        <i class="fas fa-history w-6 h-6 text-center text-lg"></i>
                        <span class="sidebar-text">Riwayat Alat</span>
                        <span class="collapsed-tooltip">Riwayat Alat</span>
                    </a>
                @endif
                <a href="{{ route('pengambilan_alat.index') }}"
                    class="menu-item flex items-center gap-4 px-4 py-3 text-base {{ request()->routeIs('pengambilan_alat.*') ? 'active' : 'text-gray-600 dark:text-gray-300' }} ripple">
                    <i class="fas fa-hand-holding w-6 h-6 text-center text-lg"></i>
                    <span class="sidebar-text">Pengambilan Alat</span>
                    <span class="collapsed-tooltip">Pengambilan</span>
                </a>
                <a href="{{ route('pengembalian_alat.index') }}"
                    class="menu-item flex items-center gap-4 px-4 py-3 text-base {{ request()->routeIs('pengembalian_alat.*') ? 'active' : 'text-gray-600 dark:text-gray-300' }} ripple">
                    <i class="fas fa-undo-alt w-6 h-6 text-center text-lg"></i>
                    <span class="sidebar-text">Pengembalian Alat</span>
                    <span class="collapsed-tooltip">Pengembalian</span>
                </a>
                @if ($user->isAdminOrSuper())
                    <div x-data="{ open: {{ request()->routeIs('bagian.*', 'admin.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between px-4 py-3 text-base text-gray-600 dark:text-gray-300 ripple">
                            <div class="flex items-center gap-4">
                                <i class="fas fa-users w-6 h-6 text-center text-lg"></i>
                                <span class="sidebar-text">Manajemen</span>
                            </div>
                            <i class="fas fa-chevron-down chevron text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-transition class="pl-12 mt-1 space-y-1 sidebar-text">
                            <a href="{{ route('bagian.index') }}"
                                class="flex items-center gap-4 px-4 py-2.5 rounded-lg text-base hover:bg-amber-50 dark:hover:bg-gray-700 {{ request()->routeIs('bagian.*') ? 'text-amber-700 dark:text-amber-300 font-medium bg-amber-50 dark:bg-gray-700' : '' }}">Bagian</a>
                            <a href="{{ route('admin.index') }}"
                                class="flex items-center gap-4 px-4 py-2.5 rounded-lg text-base hover:bg-amber-50 dark:hover:bg-gray-700 {{ request()->routeIs('admin.index') ? 'text-amber-700 dark:text-amber-300 font-medium bg-amber-50 dark:bg-gray-700' : '' }}">User</a>
                        </div>
                        <span class="collapsed-tooltip">Manajemen</span>
                    </div>
                @endif
                <div class="border-t my-4 dark:border-gray-700"></div>
                <a href="{{ route('admin.edit', Auth::user()->hashid ?? '') }}"
                    class="menu-item flex items-center gap-4 px-4 py-3 text-base text-gray-600 dark:text-gray-300 ripple">
                    <i class="fas fa-user-cog w-6 h-6 text-center text-lg"></i>
                    <span class="sidebar-text">Akun Saya</span>
                    <span class="collapsed-tooltip">Akun Saya</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="menu-item w-full flex items-center gap-4 px-4 py-3 text-base text-red-600 ripple">
                        <i class="fas fa-sign-out-alt w-6 h-6 text-center text-lg"></i>
                        <span class="sidebar-text">Logout</span>
                        <span class="collapsed-tooltip">Logout</span>
                    </button>
                </form>
            </nav>
        </aside>

        {{-- Main Content --}}
        <main
            class="main-content relative z-0 pt-26 lg:pt-28 p-6 lg:p-8 min-h-[calc(100vh-64px)] dark:bg-gray-900 transition-colors">
            <div class="animate-fade-in dark:text-gray-100">
                @if (isset($breadcrumbs))
                    <nav class="flex mb-5" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm">
                            @foreach ($breadcrumbs as $breadcrumb)
                                <li class="inline-flex items-center animate-fade-in"
                                    style="animation-delay: {{ $loop->index * 0.1 }}s">
                                    @if (!$loop->last)
                                        <a href="{{ $breadcrumb['url'] }}"
                                            class="text-gray-500 dark:text-gray-400 hover:text-gold transition">
                                            {{ $breadcrumb['name'] }}
                                        </a>
                                        <i class="fas fa-chevron-right mx-2 text-xs text-gray-400 dark:text-gray-500"></i>
                                    @else
                                        <span class="text-gold font-medium">{{ $breadcrumb['name'] }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </nav>
                @endif
                @yield('content')
            </div>
        </main>
    @else
        <main class="animate-fade-in dark:text-gray-100">
            @yield('content')
        </main>
    @endauth

    {{-- Back to Top --}}
    <button x-show="scrolled" @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-6 right-6 z-50 p-3 bg-gold hover:bg-gold-light text-white rounded-full shadow-lg transition transform hover:scale-110"
        x-transition>
        <i class="fas fa-arrow-up"></i>
    </button>

    @livewireScripts

    {{-- Plugin Alpine (otomatis mendaftarkan diri) --}}
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')

    <script>
        document.addEventListener('alpine:init', () => {
            // Plugin sudah otomatis terdaftar, tidak perlu Alpine.plugin(...)

            Alpine.data('appLayout', () => ({
                sidebarOpen: false,
                collapsed: false,
                unreadCount: {{ auth()->check() ? auth()->user()->unreadNotifications->count() : 0 }},
                loading: true,
                scrolled: false,
                scrollProgress: 0,
                openSearch: false,
                toasts: [
                    @if (session('success'))
                        {
                            type: 'success',
                            message: @json(session('success'))
                        },
                    @endif
                    @if (session('error'))
                        {
                            type: 'error',
                            message: @json(session('error'))
                        },
                    @endif
                ],

                init() {
                    window.addEventListener('load', () => setTimeout(() => this.loading = false, 200));

                    this.$watch('sidebarOpen', val => {
                        if (val && window.innerWidth < 1024) {
                            document.querySelectorAll('.sidebar a').forEach(a => {
                                a.addEventListener('click', () => this.sidebarOpen =
                                    false);
                            });
                        }
                    });

                    window.addEventListener('scroll', () => {
                        const winScroll = document.documentElement.scrollTop;
                        const height = document.documentElement.scrollHeight - document
                            .documentElement.clientHeight;
                        this.scrollProgress = (winScroll / height) * 100;
                        this.scrolled = winScroll > 200;
                    });
                },

                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                },

                handleKeydown(event) {
                    if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
                        event.preventDefault();
                        this.openSearch = true;
                    }
                    if (event.key === 'Escape' && this.openSearch) {
                        this.openSearch = false;
                    }
                },

                async markAllAsRead() {
                    try {
                        const res = await fetch('{{ route('notifications.markAllRead') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.unreadCount = 0;
                            location.reload();
                        }
                    } catch (e) {
                        console.error(e);
                    }
                }
            }));
        });
    </script>
</body>

</html>

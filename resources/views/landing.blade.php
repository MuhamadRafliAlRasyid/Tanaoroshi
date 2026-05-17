<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistem Manajemen Alat Metrologi – Tanaoroshi</title>
    <meta name="description"
        content="Sistem informasi manajemen alat ukur dan kalibrasi milik Dinas Perindustrian dan Perdagangan Kabupaten Karawang. Kelola inventaris, pengambilan, pengembalian, dan kalibrasi alat dengan QR Code." />
    <link rel="icon" href="{{ asset('images/logos.jpg') }}" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fefce8',
                            100: '#fef9c3',
                            200: '#fef08a',
                            300: '#fde047',
                            400: '#facc15',
                            500: '#e6a817',
                            600: '#c88a00',
                            700: '#a16207',
                            800: '#854d0e',
                            900: '#713f12'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .step-card.active {
            border-color: #e6a817;
            box-shadow: 0 0 0 3px rgba(230, 168, 23, 0.3);
        }
    </style>
</head>

<body class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 text-gray-800 font-sans">

    <!-- Navbar - lebih modern dengan transisi scroll -->
    <header x-data="{ open: false, scrolled: false }" x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 30)" class="fixed w-full z-50 transition-all duration-300"
        :class="scrolled ?
            'bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800/90 backdrop-blur-md shadow-sm border-b border-amber-100' :
            'bg-transparent'">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logos.jpg') }}" alt="Tanaoroshi" class="h-10 w-auto" />
                    <span class="text-xl font-bold text-brand-700">Tanaoroshi</span>
                </div>
                <div class="hidden md:flex items-center gap-8 text-sm font-medium">
                    <a href="#hero" class="text-gray-700 hover:text-brand-600 transition">Beranda</a>
                    <a href="#fitur" class="text-gray-700 hover:text-brand-600 transition">Fitur</a>
                    <a href="#alur" class="text-gray-700 hover:text-brand-600 transition">Alur</a>
                    <a href="#alat" class="text-gray-700 hover:text-brand-600 transition">Alat</a>
                    <a href="#tentang" class="text-gray-700 hover:text-brand-600 transition">Tentang</a>
                    <a href="#statistik" class="text-gray-700 hover:text-brand-600 transition">Statistik</a>
                    <a href="#kontak" class="text-gray-700 hover:text-brand-600 transition">Kontak</a>
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="bg-brand-500 hover:bg-brand-600 text-white px-5 py-2 rounded-full font-semibold transition shadow-md shadow-amber-200">
                            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                class="text-gray-700 hover:text-red-600 font-medium text-sm flex items-center gap-1">
                                <i class="fas fa-sign-out-alt"></i> Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                            class="bg-brand-500 hover:bg-brand-600 text-white px-5 py-2 rounded-full font-semibold transition shadow-md shadow-amber-200">
                            <i class="fas fa-sign-in-alt mr-1"></i> Masuk
                        </a>
                    @endauth
                </div>
                <button @click="open = !open" class="md:hidden p-2 text-gray-700">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
            <div x-show="open" x-transition
                class="md:hidden py-4 space-y-3 bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 rounded-b-2xl shadow-lg px-4">
                <a href="#hero" class="block text-gray-700">Beranda</a>
                <a href="#fitur" class="block text-gray-700">Fitur</a>
                <a href="#alur" class="block text-gray-700">Alur</a>
                <a href="#alat" class="block text-gray-700">Alat</a>
                <a href="#tentang" class="block text-gray-700">Tentang</a>
                <a href="#statistik" class="block text-gray-700">Statistik</a>
                <a href="#kontak" class="block text-gray-700">Kontak</a>
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="inline-block bg-brand-500 text-white px-5 py-2 rounded-full font-semibold">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline-block">
                        @csrf
                        <button type="submit"
                            class="text-red-600 hover:text-red-800 font-medium text-sm flex items-center gap-1">
                            <i class="fas fa-sign-out-alt"></i> Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-block bg-brand-500 text-white px-5 py-2 rounded-full font-semibold">
                        <i class="fas fa-sign-in-alt mr-1"></i> Masuk
                    </a>
                @endauth
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section id="hero"
        class="relative pt-28 pb-16 overflow-hidden bg-gradient-to-br from-amber-50 via-white to-blue-50">
        <div
            class="absolute inset-0 opacity-20 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNlMmU4ZjAiIGZpbGwtb3BhY2l0eT0iMC40Ij48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIzMCIvPjwvZz48L2c+PC9zdmc+')] bg-repeat">
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row items-center gap-12">
            <div class="lg:w-1/2 space-y-6" data-aos="fade-right">
                <span class="inline-block bg-amber-100 text-amber-800 text-sm font-semibold px-4 py-1 rounded-full">Unit
                    Metrologi Disperindag Karawang</span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight">
                    Kelola Alat Ukur <br />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600">Lebih
                        Cerdas & Akurat</span>
                </h1>
                <p class="text-lg text-gray-600 max-w-xl">
                    Tanaoroshi adalah sistem manajemen alat metrologi modern. Catat, pantau, dan kembalikan alat ukur
                    dengan QR Code dalam satu platform terintegrasi.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-bold py-3 px-8 rounded-full text-lg transition shadow-xl shadow-amber-200">
                            <i class="fas fa-tachometer-alt"></i> Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-bold py-3 px-8 rounded-full text-lg transition shadow-xl shadow-amber-200">
                            <i class="fas fa-arrow-right"></i> Masuk ke Aplikasi
                        </a>
                    @endauth
                    <a href="#fitur"
                        class="inline-flex items-center justify-center gap-2 border border-amber-300 hover:bg-amber-50 text-gray-800 font-semibold py-3 px-8 rounded-full text-lg transition">
                        <i class="fas fa-star"></i> Pelajari Fitur
                    </a>
                </div>
            </div>
            <div class="lg:w-1/2 flex justify-center" data-aos="fade-left">
                <div class="relative w-full max-w-md">
                    <div
                        class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 rounded-3xl shadow-2xl p-6 border border-amber-100">
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600 text-2xl">
                                <i class="fas fa-tools"></i>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total alat terdaftar</div>
                                <div class="text-2xl font-bold text-gray-800" x-data="{ count: 0, target: 102 }"
                                    x-init="let s = setInterval(() => { count < target ? count += Math.ceil(target / 30) : (count = target, clearInterval(s)) }, 40)" x-text="count">0</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600 text-2xl">
                                <i class="fas fa-hand-holding"></i>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Pengambilan bulan ini</div>
                                <div class="text-2xl font-bold text-gray-800" x-data="{ count: 0, target: 28 }"
                                    x-init="let s = setInterval(() => { count < target ? count += Math.ceil(target / 30) : (count = target, clearInterval(s)) }, 40)" x-text="count">0</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 text-2xl">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">QR Code Tergenerate</div>
                                <div class="text-2xl font-bold text-gray-800" x-data="{ count: 0, target: 102 }"
                                    x-init="let s = setInterval(() => { count < target ? count += Math.ceil(target / 30) : (count = target, clearInterval(s)) }, 40)" x-text="count">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur Unggulan -->
    <section id="fitur" class="py-20 bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4" data-aos="fade-up">Fitur <span
                    class="text-brand-600">Unggulan</span></h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto mb-12" data-aos="fade-up">Semua yang Anda
                butuhkan untuk
                mengelola alat ukur dan kalibrasi dalam satu sistem.</p>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- fitur cards tetap sama seperti sebelumnya -->
                <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 border border-amber-100 rounded-2xl p-8 shadow-md hover:shadow-xl transition transform hover:-translate-y-1"
                    data-aos="fade-up" data-aos-delay="100">
                    <div
                        class="w-16 h-16 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-6">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Inventaris Alat</h3>
                    <p class="text-gray-600">Data lengkap alat ukur, kategori, sertifikat, dan masa berlaku. Dilengkapi
                        foto & QR Code untuk identifikasi cepat.</p>
                </div>
                <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 border border-amber-100 rounded-2xl p-8 shadow-md hover:shadow-xl transition transform hover:-translate-y-1"
                    data-aos="fade-up" data-aos-delay="200">
                    <div
                        class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-6">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Kalibrasi Terjadwal</h3>
                    <p class="text-gray-600">Pantau jadwal kalibrasi, dapatkan notifikasi otomatis saat masa berlaku
                        hampir habis atau kedaluwarsa.</p>
                </div>
                <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 border border-amber-100 rounded-2xl p-8 shadow-md hover:shadow-xl transition transform hover:-translate-y-1"
                    data-aos="fade-up" data-aos-delay="300">
                    <div
                        class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-6">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">QR Code & Mobile</h3>
                    <p class="text-gray-600">Scan QR Code untuk langsung mencatat pengambilan atau pengembalian alat
                        dari smartphone.</p>
                </div>
                <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 border border-amber-100 rounded-2xl p-8 shadow-md hover:shadow-xl transition transform hover:-translate-y-1"
                    data-aos="fade-up" data-aos-delay="100">
                    <div
                        class="w-16 h-16 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-6">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Laporan & Cetak Bukti</h3>
                    <p class="text-gray-600">Unduh laporan pengambilan/pengembalian dalam format PDF dan cetak bukti
                        resmi bertanda tangan.</p>
                </div>
                <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 border border-amber-100 rounded-2xl p-8 shadow-md hover:shadow-xl transition transform hover:-translate-y-1"
                    data-aos="fade-up" data-aos-delay="200">
                    <div
                        class="w-16 h-16 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-6">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Multi‑Pengguna</h3>
                    <p class="text-gray-600">Akses berbasis peran (admin, karyawan, super) dengan manajemen user dan
                        bagian yang fleksibel.</p>
                </div>
                <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 border border-amber-100 rounded-2xl p-8 shadow-md hover:shadow-xl transition transform hover:-translate-y-1"
                    data-aos="fade-up" data-aos-delay="300">
                    <div
                        class="w-16 h-16 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-6">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Notifikasi Cerdas</h3>
                    <p class="text-gray-600">Peringatan stok kritis, masa kalibrasi habis, dan purchase request
                        langsung ke admin.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== BAGIAN UTAMA : ALUR PENGGUNAAN INTERAKTIF ========== -->
    <section id="alur" class="py-20 bg-gradient-to-br from-amber-50 to-yellow-50" x-data="{ activeStep: 1 }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4" data-aos="fade-up">
                Bagaimana <span class="text-brand-600">Cara Kerjanya?</span>
            </h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto mb-16" data-aos="fade-up">
                Tiga langkah mudah menggunakan Tanaoroshi dalam operasional sehari‑hari unit metrologi. Klik setiap
                langkah untuk melihat panduan detail.
            </p>

            <!-- Tiga kartu langkah yang bisa diklik -->
            <div class="grid md:grid-cols-3 gap-8 relative mb-16">
                <!-- Garis penghubung horizontal (desktop) -->
                <div class="hidden md:block absolute top-12 left-[15%] right-[15%] h-0.5 bg-amber-200 z-0"></div>

                <!-- Langkah 1: Mencari Alat -->
                <div class="relative z-10 cursor-pointer" @click="activeStep = 1" data-aos="fade-up"
                    data-aos-delay="100">
                    <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 rounded-2xl shadow-md border-2 transition-all duration-300 p-6 flex flex-col items-center"
                        :class="activeStep === 1 ? 'border-brand-500 shadow-xl shadow-amber-100 step-card active' :
                            'border-amber-100 hover:border-amber-300'">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center text-3xl mb-4"
                            :class="activeStep === 1 ? 'bg-brand-100 text-brand-600' : 'bg-amber-100 text-amber-600'">
                            <i class="fas fa-search"></i>
                        </div>
                        <span class="text-xs font-bold px-3 py-1 rounded-full mb-2"
                            :class="activeStep === 1 ? 'bg-brand-500 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600'">Langkah
                            1</span>
                        <h3 class="text-xl font-bold text-gray-800">Cari & Lihat Alat</h3>
                    </div>
                </div>

                <!-- Langkah 2: Mengambil Alat -->
                <div class="relative z-10 cursor-pointer" @click="activeStep = 2" data-aos="fade-up"
                    data-aos-delay="300">
                    <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 rounded-2xl shadow-md border-2 transition-all duration-300 p-6 flex flex-col items-center"
                        :class="activeStep === 2 ? 'border-brand-500 shadow-xl shadow-amber-100 step-card active' :
                            'border-amber-100 hover:border-amber-300'">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center text-3xl mb-4"
                            :class="activeStep === 2 ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600'">
                            <i class="fas fa-hand-holding"></i>
                        </div>
                        <span class="text-xs font-bold px-3 py-1 rounded-full mb-2"
                            :class="activeStep === 2 ? 'bg-green-500 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600'">Langkah
                            2</span>
                        <h3 class="text-xl font-bold text-gray-800">Ambil Alat</h3>
                    </div>
                </div>

                <!-- Langkah 3: Mengembalikan Alat -->
                <div class="relative z-10 cursor-pointer" @click="activeStep = 3" data-aos="fade-up"
                    data-aos-delay="500">
                    <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 rounded-2xl shadow-md border-2 transition-all duration-300 p-6 flex flex-col items-center"
                        :class="activeStep === 3 ? 'border-brand-500 shadow-xl shadow-amber-100 step-card active' :
                            'border-amber-100 hover:border-amber-300'">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center text-3xl mb-4"
                            :class="activeStep === 3 ? 'bg-blue-100 text-blue-600' : 'bg-amber-100 text-amber-600'">
                            <i class="fas fa-undo-alt"></i>
                        </div>
                        <span class="text-xs font-bold px-3 py-1 rounded-full mb-2"
                            :class="activeStep === 3 ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600'">Langkah
                            3</span>
                        <h3 class="text-xl font-bold text-gray-800">Kembalikan Alat</h3>
                    </div>
                </div>
            </div>

            <!-- Detail langkah yang dipilih -->
            <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 rounded-3xl shadow-lg border border-amber-100 p-8 md:p-10 text-left max-w-4xl mx-auto transition-all duration-500"
                data-aos="fade-up">
                <!-- Langkah 1 Detail -->
                <div x-show="activeStep === 1" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-14 h-14 bg-brand-100 text-brand-600 rounded-2xl flex items-center justify-center text-2xl">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">1. Mencari & Melihat Detail Alat</h3>
                    </div>
                    <div class="space-y-4 text-gray-700">
                        <p><strong>Buka Katalog Alat:</strong> Setelah login, Anda akan melihat daftar alat ukur yang
                            terdaftar di sistem. Setiap alat ditampilkan dalam bentuk kartu dengan informasi ringkas
                            (nama, merk, status kalibrasi, kategori).</p>
                        <p><strong>Filter & Cari:</strong> Gunakan kolom pencarian untuk menemukan alat berdasarkan
                            nama, merk, atau tipe. Anda juga dapat menyaring berdasarkan kategori (misal: Timbangan,
                            Termometer) atau status (OK, Warning, Expired).</p>
                        <p><strong>Detail Lengkap:</strong> Klik tombol <span
                                class="bg-amber-100 px-2 py-0.5 rounded text-amber-700"><i class="fas fa-eye"></i>
                                Detail</span> pada kartu alat. Anda akan melihat informasi komprehensif: nomor seri,
                            kapasitas, kelas, masa berlaku kalibrasi, foto alat, QR Code, dan riwayat kalibrasi.</p>
                        <p><strong>QR Code:</strong> Setiap alat memiliki QR Code unik. Anda dapat menampilkannya
                            langsung dari halaman detail untuk keperluan identifikasi cepat di lapangan.</p>
                    </div>
                    <div class="mt-6 p-4 bg-amber-50 rounded-xl text-sm text-amber-800 flex items-start gap-2">
                        <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
                        <span><strong>Tips:</strong> Alat dengan status <span
                                class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-semibold">Expired</span>
                            berarti sudah melewati masa kalibrasi dan harus segera dikalibrasi ulang sebelum
                            digunakan.</span>
                    </div>
                </div>

                <!-- Langkah 2 Detail -->
                <div x-show="activeStep === 2" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-14 h-14 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-2xl">
                            <i class="fas fa-hand-holding"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">2. Mengambil Alat untuk Digunakan</h3>
                    </div>
                    <div class="space-y-4 text-gray-700">
                        <p><strong>Dua Cara Pengambilan:</strong></p>
                        <ul class="list-disc list-inside ml-4 space-y-2">
                            <li><strong>Scan QR Code:</strong> Arahkan kamera smartphone ke QR Code alat. Sistem akan
                                otomatis membuka halaman pengambilan untuk alat tersebut.</li>
                            <li><strong>Manual:</strong> Dari halaman detail alat, klik tombol <span
                                    class="bg-yellow-100 px-2 py-0.5 rounded text-yellow-700"><i
                                        class="fas fa-tools"></i> Kalibrasi / Ambil</span> atau gunakan menu khusus
                                pengambilan.</li>
                        </ul>
                        <p><strong>Isi Formulir:</strong> Masukkan jumlah alat yang diambil, keperluan (misal:
                            "Pengecekan lapangan", "Kalibrasi ulang"), dan nama pengambil (jika diperlukan).</p>
                        <p><strong>Konfirmasi & Cetak Bukti:</strong> Setelah data lengkap, sistem akan menyimpan
                            catatan pengambilan dan menampilkan ringkasan. Anda dapat langsung <strong>mencetak bukti
                                pengambilan</strong> dalam format PDF yang sudah mencantumkan nama alat, tanggal, dan
                            tanda tangan digital.</p>
                        <p><strong>Stok Otomatis Berkurang:</strong> Jumlah alat yang tersedia akan otomatis diperbarui.
                            Jika stok mencapai batas minimum, admin akan menerima notifikasi.</p>
                    </div>
                    <div class="mt-6 p-4 bg-green-50 rounded-xl text-sm text-green-800 flex items-start gap-2">
                        <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                        <span><strong>Keuntungan:</strong> Semua riwayat pengambilan tercatat rapi, sehingga Anda dapat
                            melacak siapa yang membawa alat, kapan, dan untuk keperluan apa.</span>
                    </div>
                </div>

                <!-- Langkah 3 Detail -->
                <div x-show="activeStep === 3" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-2xl">
                            <i class="fas fa-undo-alt"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">3. Mengembalikan Alat</h3>
                    </div>
                    <div class="space-y-4 text-gray-700">
                        <p><strong>Proses Pengembalian:</strong> Sama seperti pengambilan, pengembalian dapat dilakukan
                            melalui scan QR Code atau manual dari menu pengembalian.</p>
                        <p><strong>Verifikasi Jumlah:</strong> Pastikan jumlah alat yang dikembalikan sesuai dengan yang
                            diambil. Sistem akan menampilkan riwayat pengambilan terakhir untuk alat tersebut sehingga
                            Anda bisa mencocokkan.</p>
                        <p><strong>Catat Kondisi (Opsional):</strong> Anda dapat menambahkan catatan kondisi alat saat
                            dikembalikan, misalnya "Baik", "Perlu Kalibrasi", atau "Rusak Ringan". Ini membantu
                            pemeliharaan inventaris.</p>
                        <p><strong>Update Stok & Status:</strong> Setelah pengembalian dicatat, stok alat otomatis
                            bertambah. Status peminjaman akan berubah menjadi "Selesai". Jika alat dalam kondisi tidak
                            baik, admin dapat menandainya untuk segera dikalibrasi atau diperbaiki.</p>
                        <p><strong>Cetak Bukti Pengembalian:</strong> Sama seperti pengambilan, Anda bisa mencetak bukti
                            pengembalian sebagai arsip.</p>
                    </div>
                    <div class="mt-6 p-4 bg-blue-50 rounded-xl text-sm text-blue-800 flex items-start gap-2">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                        <span><strong>Catatan:</strong> Semua transaksi (ambil/kembali) tercatat dalam log sistem dan
                            dapat diakses oleh admin untuk audit.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Alat Unggulan -->
    <section id="alat" class="py-20 bg-gray-50 dark:bg-gray-900 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4" data-aos="fade-up">Alat yang <span
                    class="text-brand-600">Kami Kelola</span></h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto mb-12" data-aos="fade-up">Berbagai alat ukur
                dan instrumen yang
                terdata dalam sistem Tanaoroshi.</p>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @php
                    $sampleTools = [
                        ['name' => 'Multimeter Digital', 'icon' => 'fa-bolt', 'color' => 'blue'],
                        ['name' => 'Timbangan Elektronik', 'icon' => 'fa-weight-scale', 'color' => 'green'],
                        ['name' => 'Termometer Infrared', 'icon' => 'fa-temperature-high', 'color' => 'red'],
                        ['name' => 'Jangka Sorong', 'icon' => 'fa-ruler', 'color' => 'purple'],
                        ['name' => 'Pressure Gauge', 'icon' => 'fa-gauge', 'color' => 'orange'],
                        ['name' => 'Stopwatch Kalibrasi', 'icon' => 'fa-stopwatch', 'color' => 'teal'],
                    ];
                @endphp
                @foreach ($sampleTools as $tool)
                    <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 border border-amber-100 rounded-2xl p-6 shadow-md hover:shadow-lg transition transform hover:-translate-y-1"
                        data-aos="zoom-in" data-aos-delay="{{ $loop->index * 50 }}">
                        <div
                            class="w-16 h-16 bg-{{ $tool['color'] }}-100 text-{{ $tool['color'] }}-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">
                            <i class="fas {{ $tool['icon'] }}"></i>
                        </div>
                        <h4 class="font-semibold text-gray-800 text-sm">{{ $tool['name'] }}</h4>
                    </div>
                @endforeach
            </div>
            <div class="mt-10">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center gap-2 text-brand-600 hover:text-brand-800 font-semibold">
                        <i class="fas fa-tachometer-alt"></i> Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 text-brand-600 hover:text-brand-800 font-semibold">
                        <i class="fas fa-arrow-right"></i> Lihat Semua Alat (Login diperlukan)
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Tentang Metrologi -->
    <section id="tentang" class="py-20 bg-gradient-to-br from-amber-50 to-yellow-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-1/2" data-aos="fade-right">
                    <img src="https://perindag.slemankab.go.id/wp-content/uploads/2025/09/Logo-Metrologi-Diedit.png"
                        alt="Logo Metrologi" class="w-48 h-48 object-contain mx-auto lg:mx-0" />
                </div>
                <div class="lg:w-1/2 space-y-4" data-aos="fade-left">
                    <h2 class="text-3xl font-bold text-gray-800">Tentang <span class="text-brand-600">Unit
                            Metrologi</span></h2>
                    <p class="text-gray-700 leading-relaxed">Unit Metrologi Legal Dinas Perindustrian dan Perdagangan
                        Kabupaten Karawang bertugas melaksanakan pelayanan tera, tera ulang, dan pengawasan alat‑alat
                        ukur, takar, timbang, dan perlengkapannya (UTTP) sesuai peraturan perundangan. Kami memastikan
                        keakuratan alat ukur yang digunakan masyarakat dan pelaku usaha.</p>
                    <p class="text-gray-700 leading-relaxed">Tanaoroshi hadir sebagai sistem digital yang terintegrasi
                        untuk mendukung operasional unit metrologi – mencatat inventaris alat, memantau kalibrasi,
                        mencatat pengambilan dan pengembalian, serta memudahkan pelaporan. Semua proses dilakukan secara
                        transparan dan akuntabel.</p>
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 px-8 rounded-full mt-4 transition shadow-md shadow-amber-200">
                            <i class="fas fa-tachometer-alt"></i> Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 px-8 rounded-full mt-4 transition shadow-md shadow-amber-200">
                            <i class="fas fa-sign-in-alt"></i> Akses Sistem
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- Statistik -->
    <section id="statistik" class="py-20 bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-12" data-aos="fade-up">Dalam <span
                    class="text-brand-600">Angka</span></h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="p-6 bg-amber-50 rounded-2xl" data-aos="zoom-in" data-aos-delay="0">
                    <div class="text-4xl font-extrabold text-brand-600" x-data="{ count: 0, target: 102 }"
                        x-init="let s = setInterval(() => { count < target ? count += Math.ceil(target / 30) : (count = target, clearInterval(s)) }, 30)" x-text="count">0</div>
                    <div class="text-gray-600 mt-2">Alat Terdaftar</div>
                </div>
                <div class="p-6 bg-green-50 rounded-2xl" data-aos="zoom-in" data-aos-delay="100">
                    <div class="text-4xl font-extrabold text-green-600" x-data="{ count: 0, target: 15 }"
                        x-init="let s = setInterval(() => { count < target ? count += Math.ceil(target / 30) : (count = target, clearInterval(s)) }, 40)" x-text="count">0</div>
                    <div class="text-gray-600 mt-2">Kalibrasi Bulan Ini</div>
                </div>
                <div class="p-6 bg-blue-50 rounded-2xl" data-aos="zoom-in" data-aos-delay="200">
                    <div class="text-4xl font-extrabold text-blue-600" x-data="{ count: 0, target: 28 }"
                        x-init="let s = setInterval(() => { count < target ? count += Math.ceil(target / 30) : (count = target, clearInterval(s)) }, 40)" x-text="count">0</div>
                    <div class="text-gray-600 mt-2">Pengambilan Bulan Ini</div>
                </div>
                <div class="p-6 bg-purple-50 rounded-2xl" data-aos="zoom-in" data-aos-delay="300">
                    <div class="text-4xl font-extrabold text-purple-600" x-data="{ count: 0, target: 0 }"
                        x-init="let s = setInterval(() => { count < target ? count += Math.ceil(target / 30) : (count = target, clearInterval(s)) }, 40)" x-text="count">0</div>
                    <div class="text-gray-600 mt-2">Pelanggaran</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-20 bg-gradient-to-r from-amber-500 to-orange-600 text-white">
        <div class="max-w-4xl mx-auto px-4 text-center" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Siap Mengelola Alat Metrologi dengan Lebih Modern?</h2>
            <p class="text-xl opacity-90 mb-8">Mulai sekarang, tingkatkan akurasi pencatatan dan transparansi di unit
                metrologi Anda.</p>
            <div class="flex justify-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 text-brand-700 font-bold px-8 py-4 rounded-full text-lg shadow-xl hover:bg-gray-100 dark:bg-gray-800 transition">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 text-brand-700 font-bold px-8 py-4 rounded-full text-lg shadow-xl hover:bg-gray-100 dark:bg-gray-800 transition">
                        Masuk ke Aplikasi
                    </a>
                @endauth
                <a href="#fitur"
                    class="border-2 border-white text-white font-bold px-8 py-4 rounded-full text-lg hover:bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 hover:text-brand-700 transition">Fitur
                    Lengkap</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="kontak" class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-3 gap-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('images/logos.jpg') }}" alt="Tanaoroshi" class="h-8 w-auto" />
                    <span class="text-lg font-bold text-white">Tanaoroshi</span>
                </div>
                <p class="text-sm">Sistem Manajemen Alat Metrologi <br /> Dinas Perindag Kabupaten Karawang.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3">Kontak</h4>
                <ul class="space-y-2 text-sm">
                    <li><i class="fas fa-map-marker-alt w-5 text-amber-400"></i> Jl. Contoh No.123, Karawang, Jawa
                        Barat</li>
                    <li><i class="fas fa-phone w-5 text-amber-400"></i> (0267) 123456</li>
                    <li><i class="fas fa-envelope w-5 text-amber-400"></i> perindag@karawangkab.go.id</li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3">Navigasi</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#hero" class="hover:text-amber-400">Beranda</a></li>
                    <li><a href="#fitur" class="hover:text-amber-400">Fitur</a></li>
                    <li><a href="#alur" class="hover:text-amber-400">Alur Penggunaan</a></li>
                    <li><a href="#alat" class="hover:text-amber-400">Alat</a></li>
                    <li><a href="#tentang" class="hover:text-amber-400">Tentang</a></li>
                    @auth
                        <li><a href="{{ route('dashboard') }}" class="hover:text-amber-400">Dashboard</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-300 hover:text-red-400 text-sm">Keluar</button>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}" class="hover:text-amber-400">Login</a></li>
                    @endauth
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            &copy; {{ date('Y') }} Dinas Perindustrian dan Perdagangan Kabupaten Karawang. Seluruh hak cipta
            dilindungi.
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>

</html>

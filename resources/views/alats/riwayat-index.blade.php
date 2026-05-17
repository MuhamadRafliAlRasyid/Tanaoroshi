@extends('layouts.app')

@section('title', 'Alat dengan Riwayat')

@section('content')
    <div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8" x-data="{
        search: '',
        status: '',
        init() {
            const params = new URLSearchParams(window.location.search);
            this.status = params.get('status') || '';
            this.search = params.get('search') || '';
        },
        filterByStatus() {
            const url = new URL(window.location);
            url.searchParams.set('status', this.status);
            url.searchParams.delete('page');
            window.location = url.toString();
        },
        searchAlat() {
            const url = new URL(window.location);
            url.searchParams.set('search', this.search);
            url.searchParams.delete('page');
            window.location = url.toString();
        }
    }">

        <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fas fa-clipboard-list text-amber-500"></i> Alat yang Memiliki Riwayat
        </h1>

        {{-- Pencarian & Filter --}}
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="relative flex-1">
                <input type="text" x-model="search" @keyup.enter="searchAlat()" placeholder="Cari nama alat..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
            <div class="w-full sm:w-48">
                <select x-model="status" @change="filterByStatus()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition">
                    <option value="">Semua Status</option>
                    <option value="dipinjam">Dipinjam</option>
                    <option value="dikembalikan">Dikembalikan</option>
                    <option value="dikalibrasi">Dikalibrasi</option>
                </select>
            </div>
        </div>

        {{-- Konten --}}
        @if ($alats->isEmpty())
            <div class="text-center py-16 text-gray-500 dark:text-gray-400">
                <i class="fas fa-box-open text-5xl mb-4 text-gray-300"></i>
                <p>Belum ada alat yang memiliki riwayat</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($alats as $alat)
                    @php
                        // Cek apakah alat punya riwayat pengambilan (termasuk yang sudah dikembalikan)
                        $hasPengambilan = $alat->pengambilan->isNotEmpty();

                        // Jika ada pengambilan, tentukan statusnya
                        if ($hasPengambilan) {
                            $statusPinjam = $alat->pengambilan()->where('status', 'dipinjam')->exists()
                                ? 'dipinjam'
                                : 'dikembalikan';
                        } else {
                            $statusPinjam = null; // tidak ada riwayat peminjaman sama sekali
                        }

                        // Cek riwayat kalibrasi
                        $isDikalibrasi = $alat->kalibrasis->isNotEmpty();
                    @endphp

                    <a href="{{ route('alats.riwayat', $alat->hashid) }}"
                        class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 rounded-2xl shadow-sm border border-amber-100 hover:shadow-md hover:border-amber-300 transition-all duration-300 p-6 block transform hover:-translate-y-1 animate-fade-in-up"
                        style="animation-delay: {{ $loop->index * 0.1 }}s">

                        {{-- Foto Alat atau Icon Fallback --}}
                        <div
                            class="flex items-center justify-center w-16 h-16 bg-amber-100 text-amber-600 rounded-2xl mx-auto mb-4 text-3xl overflow-hidden relative">
                            {{-- Icon selalu ada sebagai fallback --}}
                            <i class="fas fa-tools"></i>
                            {{-- Gambar ditampilkan di atas icon jika ada --}}
                            @if ($alat->foto)
                                <img src="{{ $alat->foto_url }}" alt="{{ $alat->nama_alat }}"
                                    class="absolute inset-0 w-full h-full object-cover rounded-2xl"
                                    onerror="this.style.display='none';">
                            @endif
                        </div>

                        <h3 class="text-lg font-semibold text-gray-800 text-center">{{ $alat->nama_alat }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mt-1">{{ $alat->merk }}</p>

                        <div class="flex justify-center mt-3 flex-wrap gap-1">
                            @if ($hasPengambilan)
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $statusPinjam == 'dipinjam' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $statusPinjam == 'dipinjam' ? 'Dipinjam' : 'Dikembalikan' }}
                                </span>
                            @endif

                            @if ($isDikalibrasi)
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    Terkalibrasi
                                </span>
                            @endif

                            {{-- Fallback bila tidak ada riwayat apapun (seharusnya tidak terjadi) --}}
                            @if (!$hasPengambilan && !$isDikalibrasi)
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600">
                                    Tidak ada riwayat
                                </span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $alats->links() }}
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }
    </style>
@endpush

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

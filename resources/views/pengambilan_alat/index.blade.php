@extends('layouts.app')

@section('title', 'Pengambilan Alat')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header dengan gradient background --}}
        <div
            class="relative overflow-hidden bg-gradient-to-br from-amber-50 via-white to-orange-50 rounded-3xl p-6 sm:p-8 mb-8 border border-amber-100/50 shadow-sm">
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-amber-200/20 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl">
            </div>
            <div
                class="absolute bottom-0 left-0 w-48 h-48 bg-orange-200/20 rounded-full translate-y-1/2 -translate-x-1/2 blur-3xl">
            </div>

            <div class="relative flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span
                            class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-lg shadow-amber-200">
                            <i class="fas fa-hand-holding"></i>
                        </span>
                        <h2
                            class="text-3xl font-bold bg-gradient-to-r from-amber-700 to-orange-700 bg-clip-text text-transparent">
                            Pengambilan Alat</h2>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 ml-13">Riwayat peminjaman alat oleh pengguna</p>
                </div>
                <div class="flex gap-3 flex-wrap">
                    <a href="{{ route('pengambilan_alat.create') }}"
                        class="group relative inline-flex items-center gap-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-5 py-3 rounded-2xl font-medium transition-all duration-300 shadow-lg shadow-amber-200 hover:shadow-xl hover:shadow-amber-300 hover:-translate-y-0.5">
                        <i class="fas fa-plus group-hover:rotate-90 transition-transform duration-300"></i>
                        <span>Ambil Alat Baru</span>
                    </a>
                    @if (auth()->user()->role == 'admin')
                        <a href="{{ route('pengambilan_alat.export.pdf') }}"
                            class="inline-flex items-center gap-2 bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 hover:bg-red-50 text-red-600 border border-red-200 hover:border-red-300 px-5 py-3 rounded-2xl font-medium transition-all duration-300 shadow-sm hover:shadow-md">
                            <i class="fas fa-file-pdf"></i>
                            <span>Export PDF</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Alert Success yang ditingkatkan --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 4000)"
                class="mb-6 bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3 shadow-sm">
                <span class="flex-shrink-0 w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                </span>
                <span class="flex-1 font-medium">{{ session('success') }}</span>
                <button @click="show = false"
                    class="flex-shrink-0 w-8 h-8 rounded-xl hover:bg-emerald-100 flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-emerald-400"></i>
                </button>
            </div>
        @endif

        {{-- Search Bar Modern --}}
        <div class="mb-8">
            <div class="relative max-w-lg">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari berdasarkan alat, pengguna, atau bagian..."
                    class="w-full pl-11 pr-4 py-3.5 bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 rounded-2xl text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300 shadow-sm hover:shadow-md">
                @if (request('search'))
                    <a href="{{ route('pengambilan_alat.index') }}"
                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times-circle"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- List dengan Card Design Modern --}}
        <div class="space-y-4">
            @forelse($data as $i => $d)
                @php
                    $isDipinjam = $d->status === 'dipinjam';
                @endphp
                <div
                    class="group relative bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 transition-all duration-300 hover:shadow-xl hover:border-amber-200 overflow-hidden {{ $isDipinjam ? 'hover:border-red-200' : 'hover:border-emerald-200' }}">
                    {{-- Status indicator bar --}}
                    <div
                        class="absolute left-0 top-0 bottom-0 w-1 {{ $isDipinjam ? 'bg-gradient-to-b from-red-400 to-red-500' : 'bg-gradient-to-b from-emerald-400 to-emerald-500' }}">
                    </div>

                    <div class="p-5 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                            {{-- Thumbnail & Status Icon Combined --}}
                            <div class="flex-shrink-0 flex items-center gap-3">
                                <div class="relative">
                                    <div
                                        class="w-16 h-16 rounded-2xl bg-gray-50 dark:bg-gray-900 dark:bg-gray-900 flex items-center justify-center overflow-hidden border border-gray-100 dark:border-gray-700 group-hover:border-amber-200 transition-colors">
                                        @if ($d->alat && $d->alat->foto)
                                            <img src="{{ $d->alat->foto_thumb }}" alt="{{ $d->alat->nama_alat }}"
                                                loading="lazy" class="w-full h-full object-cover"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="w-full h-full items-center justify-center text-gray-400 hidden">
                                                <i class="fas fa-tools text-2xl"></i>
                                            </div>
                                        @else
                                            <i class="fas fa-tools text-2xl text-gray-300"></i>
                                        @endif
                                    </div>
                                    <div
                                        class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full {{ $isDipinjam ? 'bg-red-100 text-red-500' : 'bg-emerald-100 text-emerald-500' }} flex items-center justify-center border-2 border-white shadow-sm">
                                        <i
                                            class="fas {{ $isDipinjam ? 'fa-clock text-xs' : 'fa-check-circle text-xs' }}"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Info Utama --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div>
                                        <h3
                                            class="font-bold text-gray-800 text-lg truncate group-hover:text-amber-700 transition-colors">
                                            {{ $d->alat->nama_alat ?? 'Alat Tidak Diketahui' }}
                                        </h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isDipinjam ? 'bg-red-50 text-red-700 ring-1 ring-red-200' : 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' }}">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $isDipinjam ? 'bg-red-500' : 'bg-emerald-500' }}"></span>
                                                {{ $isDipinjam ? 'Dipinjam' : 'Dikembalikan' }}
                                            </span>
                                            @if ($d->alat->merk)
                                                <span class="text-xs text-gray-400">{{ $d->alat->merk }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Detail Info --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-3">
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <span
                                            class="flex-shrink-0 w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center">
                                            <i class="fas fa-user text-amber-500 text-xs"></i>
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-xs text-gray-400">Peminjam</p>
                                            <p class="font-medium text-gray-700 truncate">
                                                {{ $d->nama_peminjam ?? ($d->user->name ?? '-') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <span
                                            class="flex-shrink-0 w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center">
                                            <i class="fas fa-building text-amber-500 text-xs"></i>
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-xs text-gray-400">Bagian</p>
                                            <p class="font-medium text-gray-700 truncate">{{ $d->bagian->nama ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <span
                                            class="flex-shrink-0 w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center">
                                            <i class="fas fa-calendar-alt text-amber-500 text-xs"></i>
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-xs text-gray-400">Waktu</p>
                                            <p class="font-medium text-gray-700">
                                                {{ \Carbon\Carbon::parse($d->waktu_pengambilan)->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Jumlah Badge --}}
                                <div class="mt-3">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 text-sm font-medium">
                                        <i class="fas fa-boxes text-amber-400"></i>
                                        {{ $d->jumlah }} {{ $d->satuan ?? 'pcs' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex sm:flex-col gap-2 justify-end">
                                <a href="{{ route('pengambilan_alat.show', $d->hashid) }}"
                                    class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 hover:text-amber-700 transition-all duration-200 hover:scale-105"
                                    title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if (auth()->user()->role == 'admin')
                                    <a href="{{ route('pengambilan_alat.edit', $d->hashid) }}"
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition-all duration-200 hover:scale-105"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('pengambilan_alat.destroy', $d->hashid) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 transition-all duration-200 hover:scale-105"
                                            title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-20">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-amber-50 mb-6">
                        <i class="fas fa-hand-holding text-4xl text-amber-300"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Data</h3>
                    <p class="text-gray-400 mb-6">Belum ada riwayat pengambilan alat</p>
                    <a href="{{ route('pengambilan_alat.create') }}"
                        class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-6 py-3 rounded-2xl font-medium transition-all shadow-lg shadow-amber-200">
                        <i class="fas fa-plus"></i> Ambil Alat Baru
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Pagination yang dipercantik --}}
        @if ($data->hasPages())
            <div class="mt-8 flex justify-center">
                <div
                    class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 px-4 py-3">
                    {{ $data->onEachSide(1)->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        @endif
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Pengembalian Alat')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-8">
        <a href="{{ route('pengambilan_alat.index') }}"
            class="inline-flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 mb-6 group transition-colors">
            <span
                class="w-8 h-8 rounded-xl bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 flex items-center justify-center">
                <i class="fas fa-arrow-left text-sm"></i>
            </span>
            <span class="font-medium">Kembali</span>
        </a>

        <div
            class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 p-6 sm:p-8 border-b border-amber-100">
                <div class="flex items-center gap-4">
                    <span
                        class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 shadow-lg shadow-amber-100">
                        <i class="fas fa-undo-alt text-xl text-amber-500"></i>
                    </span>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Pengembalian Alat</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">
                            {{ $pengambilan->alat->nama_alat ?? 'Alat' }}</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('pengembalian_alat.store', $pengambilan->hashid) }}"
                enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
                @csrf

                <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl p-5 border border-amber-100">
                    <h3 class="text-xs font-semibold text-amber-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-lg bg-amber-200 flex items-center justify-center">
                            <i class="fas fa-info text-amber-600 text-xs"></i>
                        </span>
                        Informasi Peminjaman
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-amber-600/70">Peminjam</span>
                            <p class="font-medium text-gray-800">
                                {{ $pengambilan->nama_peminjam ?? ($pengambilan->user->name ?? '-') }}</p>
                        </div>
                        <div>
                            <span class="text-amber-600/70">Bagian</span>
                            <p class="font-medium text-gray-800">{{ $pengambilan->bagian->nama ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-amber-600/70">Jumlah Dipinjam</span>
                            <p class="font-medium text-gray-800">{{ $pengambilan->jumlah }} {{ $pengambilan->satuan }}</p>
                        </div>
                        <div>
                            <span class="text-amber-600/70">Keperluan</span>
                            <p class="font-medium text-gray-800 truncate">{{ $pengambilan->keperluan ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                            <i class="fas fa-boxes text-amber-600 text-xs"></i>
                        </span>
                        Jumlah Dikembalikan <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="jumlah" value="{{ old('jumlah') }}" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 rounded-2xl focus:bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300"
                        placeholder="Masukkan jumlah">
                </div>

                <div class="form-group">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-amber-600 text-xs"></i>
                        </span>
                        Tanggal Pengembalian
                    </label>
                    <input type="date" name="tanggal_pengembalian"
                        value="{{ old('tanggal_pengembalian', now()->format('Y-m-d')) }}"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 rounded-2xl focus:bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300">
                </div>

                <div class="form-group">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                            <i class="fas fa-sticky-note text-amber-600 text-xs"></i>
                        </span>
                        Keterangan
                    </label>
                    <textarea name="keterangan" rows="4"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 rounded-2xl focus:bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300 resize-none"
                        placeholder="Catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                </div>

                {{-- Upload Foto Bukti --}}
                <div class="form-group">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                            <i class="fas fa-camera text-amber-600 text-xs"></i>
                        </span>
                        Foto Bukti (opsional)
                    </label>
                    <input type="file" name="foto" accept="image/*"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 rounded-2xl focus:bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                    <p class="text-xs text-gray-400 mt-1">Format JPG/PNG/WebP, maks 2MB</p>
                </div>

                <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row gap-3 justify-end">
                    <a href="{{ route('pengembalian_alat.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3.5 border-2 border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 hover:bg-gray-50 dark:bg-gray-900 dark:bg-gray-900 text-gray-700 font-medium rounded-2xl transition-all">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold rounded-2xl transition-all duration-300 shadow-lg shadow-amber-200 hover:shadow-xl hover:-translate-y-0.5">
                        <i class="fas fa-save"></i> Simpan Pengembalian
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

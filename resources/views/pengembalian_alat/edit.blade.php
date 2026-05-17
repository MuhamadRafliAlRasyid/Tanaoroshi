@extends('layouts.app')

@section('title', 'Edit Pengembalian Alat')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-8">
        <a href="{{ route('pengembalian_alat.index') }}"
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
                        <i class="fas fa-edit text-xl text-amber-500"></i>
                    </span>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Edit Pengembalian Alat</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">
                            {{ $data->pengambilan->alat->nama_alat ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('pengembalian_alat.update', $data->hashid) }}"
                enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                            <i class="fas fa-boxes text-amber-600 text-xs"></i>
                        </span>
                        Jumlah Dikembalikan <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="jumlah" value="{{ old('jumlah', $data->jumlah) }}" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 rounded-2xl focus:bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300">
                </div>

                <div class="form-group">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-amber-600 text-xs"></i>
                        </span>
                        Tanggal Pengembalian
                    </label>
                    <input type="date" name="tanggal_pengembalian"
                        value="{{ old('tanggal_pengembalian', \Carbon\Carbon::parse($data->tanggal_pengembalian)->format('Y-m-d')) }}"
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
                        placeholder="Catatan tambahan">{{ old('keterangan', $data->keterangan) }}</textarea>
                </div>

                {{-- Foto Bukti --}}
                <div class="form-group">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                            <i class="fas fa-camera text-amber-600 text-xs"></i>
                        </span>
                        Foto Bukti (opsional)
                    </label>
                    @if ($data->foto)
                        <div class="mb-3 flex items-center gap-3">
                            <img src="{{ $data->foto_url }}" alt="Foto bukti"
                                class="w-24 h-24 rounded-xl object-cover border">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Biarkan kosong jika tidak ingin
                                mengubah.</span>
                        </div>
                    @endif
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
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

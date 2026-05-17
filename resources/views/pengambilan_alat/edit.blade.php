@extends('layouts.app')

@section('title', 'Edit Pengambilan Alat')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-8">
        {{-- Back Button --}}
        <a href="{{ route('pengambilan_alat.index') }}"
            class="inline-flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 mb-6 group transition-colors">
            <span
                class="w-8 h-8 rounded-xl bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 flex items-center justify-center transition-colors">
                <i class="fas fa-arrow-left text-sm"></i>
            </span>
            <span class="font-medium">Kembali</span>
        </a>

        <div
            class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            {{-- Header --}}
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 p-6 sm:p-8 border-b border-amber-100">
                <div class="flex items-center gap-4">
                    <span
                        class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 shadow-lg shadow-amber-100">
                        <i class="fas fa-edit text-xl text-amber-500"></i>
                    </span>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Edit Pengambilan Alat</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">Perbarui data peminjaman alat</p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('pengambilan_alat.update', $data->hashid) }}"
                enctype="multipart/form-data" class="p-6 sm:p-8">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    {{-- Nama Peminjam (Admin Only) --}}
                    @if (auth()->user()->role == 'admin')
                        <div class="form-group">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                                    <i class="fas fa-user text-amber-600 text-xs"></i>
                                </span>
                                Nama Peminjam
                            </label>
                            <input type="text" name="nama_peminjam"
                                value="{{ old('nama_peminjam', $data->nama_peminjam) }}"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 rounded-2xl focus:bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300"
                                placeholder="Biarkan kosong jika sama dengan user">
                        </div>
                    @endif

                    {{-- Pilih Alat --}}
                    <div class="form-group">
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                            <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                                <i class="fas fa-tools text-amber-600 text-xs"></i>
                            </span>
                            Nama Alat <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="alat_id" required
                                class="w-full appearance-none px-4 py-3 bg-gray-50 dark:bg-gray-900 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 rounded-2xl focus:bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300 pr-10">
                                @foreach ($alats as $alat)
                                    <option value="{{ $alat->hashid }}"
                                        {{ old('alat_id', $data->alat_id) == $alat->id ? 'selected' : '' }}>
                                        {{ $alat->nama_alat }} | {{ $alat->merk }} | {{ $alat->tipe }} | Seri:
                                        {{ $alat->no_seri }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Bagian --}}
                    <div class="form-group">
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                            <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                                <i class="fas fa-building text-amber-600 text-xs"></i>
                            </span>
                            Bagian / Departemen <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="bagian_id" required
                                class="w-full appearance-none px-4 py-3 bg-gray-50 dark:bg-gray-900 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 rounded-2xl focus:bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300 pr-10">
                                @foreach ($bagians as $b)
                                    <option value="{{ $b->id }}"
                                        {{ old('bagian_id', $data->bagian_id) == $b->id ? 'selected' : '' }}>
                                        {{ $b->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Jumlah & Satuan --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                                    <i class="fas fa-boxes text-amber-600 text-xs"></i>
                                </span>
                                Jumlah
                            </label>
                            <input type="number" name="jumlah" value="{{ old('jumlah', $data->jumlah) }}" min="1"
                                required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 rounded-2xl focus:bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300">
                        </div>
                        <div class="form-group">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                                    <i class="fas fa-weight-hanging text-amber-600 text-xs"></i>
                                </span>
                                Satuan
                            </label>
                            <input type="text" name="satuan" value="{{ old('satuan', $data->satuan) }}"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 rounded-2xl focus:bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300">
                        </div>
                    </div>

                    {{-- Keperluan --}}
                    <div class="form-group">
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                            <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-amber-600 text-xs"></i>
                            </span>
                            Keperluan Pengambilan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="keperluan" rows="4" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 rounded-2xl focus:bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300 resize-none"
                            placeholder="Jelaskan keperluan penggunaan alat...">{{ old('keperluan', $data->keperluan) }}</textarea>
                    </div>

                    {{-- Waktu --}}
                    <div class="form-group">
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                            <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-amber-600 text-xs"></i>
                            </span>
                            Waktu Pengambilan
                        </label>
                        <input type="datetime-local" name="waktu_pengambilan"
                            value="{{ old('waktu_pengambilan', \Carbon\Carbon::parse($data->waktu_pengambilan)->format('Y-m-d\TH:i')) }}"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 rounded-2xl focus:bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300">
                    </div>

                    {{-- Foto --}}
                    <div class="form-group">
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                            <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                                <i class="fas fa-camera text-amber-600 text-xs"></i>
                            </span>
                            Foto Bukti (opsional)
                        </label>
                        @if ($data->foto)
                            <div
                                class="mb-3 flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-700">
                                <img src="{{ $data->foto_url }}" alt="Foto bukti"
                                    class="w-16 h-16 rounded-xl object-cover border">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Biarkan kosong jika tidak ingin
                                    mengubah.</span>
                            </div>
                        @endif
                        <div class="relative">
                            <input type="file" name="foto" accept="image/*"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 rounded-2xl focus:bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i> Format JPG/PNG/WebP, maks 2MB
                        </p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div
                    class="mt-8 flex flex-col sm:flex-row gap-3 justify-end border-t border-gray-100 dark:border-gray-700 pt-8">
                    <a href="{{ route('pengambilan_alat.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3.5 border-2 border-gray-200 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 dark:border-gray-700 hover:bg-gray-50 dark:bg-gray-900 dark:bg-gray-900 text-gray-700 font-medium rounded-2xl transition-all duration-300 hover:border-gray-300">
                        <i class="fas fa-arrow-left"></i>
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold rounded-2xl transition-all duration-300 shadow-lg shadow-amber-200 hover:shadow-xl hover:shadow-amber-300 hover:-translate-y-0.5">
                        <i class="fas fa-save"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

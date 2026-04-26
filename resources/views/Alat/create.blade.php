@extends('layouts.app')

@section('title', 'Tambah Alat Baru')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-3xl shadow-xl p-8">

            <!-- Header -->
            <div class="flex items-center gap-3 mb-8">
                <i class="fas fa-plus-circle text-3xl text-green-600"></i>
                <h2 class="text-2xl font-bold text-gray-800">Tambah Alat Baru</h2>
            </div>

            <form method="POST" action="{{ route('alat.store') }}">
                @csrf

                <div class="space-y-6">

                    <!-- Nama Alat -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Alat <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama_alat" value="{{ old('nama_alat') }}"
                            class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Contoh: Multimeter Digital" required>
                    </div>

                    <!-- Merk -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Merk <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="merk" value="{{ old('merk') }}"
                            class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Contoh: Fluke, Bosch, dll" required>
                    </div>

                    <!-- Tipe -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                        <input type="text" name="tipe" value="{{ old('tipe') }}"
                            class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Contoh: FLUKE 87V">
                    </div>

                    <!-- No Seri -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Seri</label>
                        <input type="text" name="no_seri" value="{{ old('no_seri') }}"
                            class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Masukkan nomor seri jika ada">
                    </div>

                    <!-- Jumlah -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="jumlah" value="{{ old('jumlah') }}" min="1"
                            class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            required>
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select name="kategori_id"
                            class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategoris ?? [] as $kategori)
                                <option value="{{ $kategori->id }}"
                                    {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Masa Berlaku -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Masa Berlaku (Kalibrasi)</label>
                        <input type="date" name="masa_berlaku" value="{{ old('masa_berlaku') }}"
                            class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                </div>

                <!-- Tombol -->
                <div class="mt-10 flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-4 rounded-2xl transition shadow-md">
                        <i class="fas fa-save mr-2"></i> Simpan Alat
                    </button>

                    <a href="{{ route('alat.index') }}"
                        class="flex-1 text-center border border-gray-300 hover:bg-gray-50 font-medium py-4 rounded-2xl transition">
                        Batal
                    </a>
                </div>
            </form>

        </div>
    </div>
@endsection

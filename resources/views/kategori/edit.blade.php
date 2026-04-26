@extends('layouts.app')

@section('title', isset($kategori) ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-xl p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-8 flex items-center gap-3">
            <i class="fas fa-tags text-blue-600"></i>
            {{ isset($kategori) ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
        </h1>

        <form method="POST"
            action="{{ isset($kategori) ? route('kategori.update', $kategori->hashid) : route('kategori.store') }}">
            @csrf
            @if (isset($kategori))
                @method('PUT')
            @endif

            <div class="space-y-6">
                <!-- Nama Kategori -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori</label>
                    <input type="text" name="nama" value="{{ old('nama', $kategori->nama ?? '') }}"
                        placeholder="Masukkan nama kategori"
                        class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        required>
                    @error('nama')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="4" placeholder="Deskripsi atau catatan tambahan..."
                        class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">{{ old('keterangan', $kategori->keterangan ?? '') }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-4 mt-10">
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-2xl transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    {{ isset($kategori) ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                </button>

                <a href="{{ route('kategori.index') }}"
                    class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-4 rounded-2xl transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection

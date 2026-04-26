@extends('layouts.app')

@section('title', 'Detail Kategori')

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-xl p-8">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $kategori->nama }}</h1>
                <p class="text-gray-500 mt-1">Detail Kategori</p>
            </div>
            <a href="{{ route('kategori.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="bg-gray-50 rounded-2xl p-6">
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <span class="text-sm text-gray-500 block mb-1">Nama Kategori</span>
                    <p class="text-lg font-medium text-gray-800">{{ $kategori->nama }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 block mb-1">Keterangan</span>
                    <p class="text-gray-700 leading-relaxed">
                        {{ $kategori->keterangan ?? 'Tidak ada keterangan.' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex gap-4 mt-10">
            <a href="{{ route('kategori.edit', $kategori->hashid) }}"
                class="flex-1 bg-amber-600 hover:bg-amber-700 text-white text-center py-4 rounded-2xl font-medium transition">
                Edit Kategori
            </a>
            <a href="{{ route('kategori.index') }}"
                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-center py-4 rounded-2xl font-medium transition">
                Kembali ke Daftar
            </a>
        </div>
    </div>
@endsection

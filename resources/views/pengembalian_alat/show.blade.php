@extends('layouts.app')

@section('title', 'Detail Pengembalian Alat')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-3xl shadow-xl p-8">

            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold">Detail Pengembalian Alat</h2>
                <a href="{{ route('pengembalian_alat.index') }}"
                    class="text-gray-500 hover:text-gray-700 flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <p class="text-gray-500 text-sm">Nama Alat</p>
                    <p class="text-xl font-semibold">{{ $data->pengambilan->alat->nama_alat ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">User</p>
                    <p class="text-xl font-semibold">{{ $data->user->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Jumlah Dikembalikan</p>
                    <p class="text-3xl font-bold text-green-600">{{ $data->jumlah }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Tanggal Pengembalian</p>
                    <p class="text-lg">{{ $data->tanggal_pengembalian }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-gray-500 text-sm">Keterangan</p>
                    <p class="bg-gray-50 p-5 rounded-2xl">{{ $data->keterangan ?? 'Tidak ada keterangan' }}</p>
                </div>
            </div>

        </div>
    </div>
@endsection

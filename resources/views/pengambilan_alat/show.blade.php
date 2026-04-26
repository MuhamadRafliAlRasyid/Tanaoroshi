@extends('layouts.app')

@section('title', 'Detail Pengambilan')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-3xl shadow p-8">

            <!-- HEADER -->
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold">Detail Pengambilan Alat</h2>

                <a href="{{ route('pengambilan_alat.index') }}"
                    class="text-gray-500 hover:text-gray-700 flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <!-- DATA -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <div>
                    <p class="text-gray-500 text-sm">Nama Alat</p>
                    <p class="text-xl font-semibold">
                        {{ $data->alat->nama_alat }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 text-sm">User</p>
                    <p class="text-xl font-semibold">
                        {{ $data->user->name }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 text-sm">Bagian</p>
                    <p class="text-xl">
                        {{ $data->bagian->nama }}
                    </p>
                </div>

                <!-- 🔥 TAMBAHAN JUMLAH -->
                <div>
                    <p class="text-gray-500 text-sm">Jumlah Dipinjam</p>
                    <p class="text-xl font-bold text-blue-600">
                        {{ $data->jumlah }} {{ $data->satuan }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 text-sm">Status</p>
                    @if ($data->status == 'dipinjam')
                        <span class="inline-flex px-5 py-2 bg-red-100 text-red-700 rounded-2xl text-sm font-medium">
                            Sedang Dipinjam
                        </span>
                    @else
                        <span class="inline-flex px-5 py-2 bg-green-100 text-green-700 rounded-2xl text-sm font-medium">
                            Sudah Dikembalikan
                        </span>
                    @endif
                </div>

                <div class="md:col-span-2">
                    <p class="text-gray-500 text-sm">Keperluan</p>
                    <p class="bg-gray-50 p-5 rounded-2xl">
                        {{ $data->keperluan }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 text-sm">Waktu Pengambilan</p>
                    <p>
                        {{ $data->waktu_pengambilan }}
                    </p>
                </div>
            </div>

            <!-- 🔥 ACTION -->
            @if ($data->status == 'dipinjam')
                <div class="mt-10">

                    <a href="{{ route('pengembalian_alat.create', $data->hashid) }}"
                        class="block text-center bg-green-600 hover:bg-green-700 text-white py-4 rounded-2xl font-semibold text-lg transition">

                        <i class="fas fa-undo mr-2"></i>
                        Kembalikan Alat

                    </a>

                </div>
            @endif

        </div>
    </div>
@endsection

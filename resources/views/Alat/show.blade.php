@extends('layouts.app')

@section('title', 'Detail Alat')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-3xl shadow p-8">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold">{{ $alat->nama_alat }}</h2>
                <a href="{{ route('alat.index') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-500 text-sm">Merk</p>
                    <p class="text-lg font-medium">{{ $alat->merk }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Tipe</p>
                    <p class="text-lg font-medium">{{ $alat->tipe }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Kategori</p>
                    <p class="text-lg">{{ $alat->kategori->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Masa Berlaku</p>
                    <p class="text-lg">{{ $alat->masa_berlaku }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Status</p>
                    @if ($alat->status == 'expired')
                        <span class="text-red-600 font-bold">Expired</span>
                    @elseif($alat->status == 'warning')
                        <span class="text-yellow-600 font-bold">Warning</span>
                    @else
                        <span class="text-green-600 font-bold">OK</span>
                    @endif
                </div>
            </div>

            <!-- Riwayat Kalibrasi -->
            <div class="mt-10">
                <h3 class="font-semibold mb-4">Riwayat Kalibrasi</h3>
                @if ($alat->kalibrasis->count() > 0)
                    <div class="space-y-3">
                        @foreach ($alat->kalibrasis as $k)
                            <div class="bg-gray-50 p-4 rounded-2xl">
                                <div class="flex justify-between">
                                    <span>{{ $k->tanggal_kalibrasi }}</span>
                                    <span class="font-medium">{{ $k->no_sertifikat }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">Masa Berlaku Baru: {{ $k->masa_berlaku_baru }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 italic">Belum ada riwayat kalibrasi</p>
                @endif
            </div>
        </div>
    </div>
@endsection

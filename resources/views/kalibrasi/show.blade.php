@extends('layouts.app')

@section('title', 'Detail Alat')

@section('content')
    <div class="max-w-3xl mx-auto">

        @if (isset($alat))
            <div class="bg-white rounded-3xl shadow p-8">

                <!-- HEADER -->
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-bold">{{ $alat->nama_alat }}</h2>

                    <div class="flex gap-2">
                        <a href="{{ route('kalibrasi.create', $alat->hashid) }}"
                            class="bg-orange-600 text-white px-4 py-2 rounded-xl">
                            + Kalibrasi
                        </a>

                        <a href="{{ route('alat.index') }}" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <!-- DATA -->
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
                        <p class="text-lg">
                            {{ \Carbon\Carbon::parse($alat->masa_berlaku)->format('d M Y') }}
                        </p>
                    </div>

                    <!-- STATUS -->
                    <div>
                        <p class="text-gray-500 text-sm">Status</p>

                        @php
                            $expired = $alat->masa_berlaku
                                ? \Carbon\Carbon::parse($alat->masa_berlaku)->isPast()
                                : false;

                            $warning = $alat->masa_berlaku
                                ? \Carbon\Carbon::parse($alat->masa_berlaku)->diffInDays(now()) <= 7
                                : false;
                        @endphp

                        @if ($expired)
                            <span class="text-red-600 font-bold">❌ Expired</span>
                        @elseif($warning)
                            <span class="text-yellow-600 font-bold">⚠ Warning</span>
                        @else
                            <span class="text-green-600 font-bold">✔ OK</span>
                        @endif
                    </div>

                </div>

                <!-- RIWAYAT -->
                <div class="mt-10">
                    <h3 class="font-semibold mb-4">Riwayat Kalibrasi</h3>

                    @if ($alat->kalibrasis && $alat->kalibrasis->count() > 0)

                        <div class="space-y-3">
                            @foreach ($alat->kalibrasis as $k)
                                <div class="bg-gray-50 p-4 rounded-2xl">

                                    <div class="flex justify-between">
                                        <span>
                                            {{ \Carbon\Carbon::parse($k->tanggal_kalibrasi)->format('d M Y') }}
                                        </span>

                                        <span class="font-medium">
                                            {{ $k->no_sertifikat ?? '-' }}
                                        </span>
                                    </div>

                                    <p class="text-sm text-gray-600 mt-1">
                                        Masa Berlaku:
                                        {{ \Carbon\Carbon::parse($k->masa_berlaku_baru)->format('d M Y') }}
                                    </p>

                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">Belum ada riwayat kalibrasi</p>
                    @endif
                </div>

            </div>
        @else
            <div class="text-center text-red-500">
                Data alat tidak ditemukan
            </div>
        @endif

    </div>
@endsection

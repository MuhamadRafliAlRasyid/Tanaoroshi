@extends('layouts.app')

@section('title', 'Data Kalibrasi Alat')

@section('content')
    <div class="max-w-7xl mx-auto">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Kalibrasi Alat</h1>
                <p class="text-gray-500">Riwayat kalibrasi alat</p>
            </div>
        </div>

        <!-- 🔍 SEARCH -->
        <form method="GET" class="mb-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama alat..."
                class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:ring-2 focus:ring-orange-500">
        </form>

        <!-- TABLE -->
        <div class="bg-white rounded-3xl shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left">No</th>
                        <th class="px-6 py-4 text-left">Alat</th>
                        <th class="px-6 py-4 text-left">Tanggal Kalibrasi</th>
                        <th class="px-6 py-4 text-left">Masa Berlaku</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($data as $i => $item)
                        @php
                            $expired = \Carbon\Carbon::parse($item->masa_berlaku_baru)->isPast();
                        @endphp

                        <tr class="hover:bg-gray-50">

                            <!-- NO -->
                            <td class="px-6 py-4">
                                {{ $data->firstItem() + $i }}
                            </td>

                            <!-- ALAT -->
                            <td class="px-6 py-4">
                                <b>{{ $item->alat->nama_alat ?? '-' }}</b><br>
                                <small class="text-gray-500">
                                    {{ $item->alat->merk ?? '' }} | {{ $item->alat->tipe ?? '' }}
                                </small>
                            </td>

                            <!-- TANGGAL -->
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($item->tanggal_kalibrasi)->format('d M Y') }}
                            </td>

                            <!-- MASA BERLAKU -->
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($item->masa_berlaku_baru)->format('d M Y') }}
                            </td>

                            <!-- STATUS -->
                            <td class="px-6 py-4 text-center">
                                @if ($expired)
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">
                                        Expired
                                    </span>
                                @else
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                                        Aktif
                                    </span>
                                @endif
                            </td>

                            <!-- AKSI -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">

                                    <!-- DETAIL -->
                                    <a href="{{ route('kalibrasi.show', $item->hashid) }}"
                                        class="bg-blue-100 px-3 py-1 rounded hover:bg-blue-200">
                                        👁
                                    </a>

                                    <!-- EDIT -->
                                    <a href="{{ route('kalibrasi.edit', $item->hashid) }}"
                                        class="bg-yellow-100 px-3 py-1 rounded hover:bg-yellow-200">
                                        ✏️
                                    </a>

                                    <!-- DELETE -->
                                    <form method="POST" action="{{ route('kalibrasi.destroy', $item->hashid) }}"
                                        onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button class="bg-red-100 px-3 py-1 rounded hover:bg-red-200">
                                            🗑
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-500">
                                Tidak ada data kalibrasi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="mt-6">
            {{ $data->links() }}
        </div>

    </div>
@endsection

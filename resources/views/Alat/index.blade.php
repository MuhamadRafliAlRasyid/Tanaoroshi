@extends('layouts.app')

@section('title', 'Data Alat')

@section('content')
    <div class="max-w-7xl mx-auto px-4">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <h2 class="text-3xl font-bold text-indigo-700 flex items-center gap-3">
                <i class="fas fa-tools"></i>
                Data Alat
            </h2>

            <div class="flex gap-3">
                <a href="{{ route('alat.create') }}"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-2xl font-medium flex items-center gap-2 transition shadow">
                    <i class="fas fa-plus"></i> Tambah Alat
                </a>
                <a href="{{ route('alat.trashed') }}"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-2xl font-medium flex items-center gap-2 transition shadow">
                    <i class="fas fa-trash-restore"></i> Data Terhapus
                </a>
            </div>
        </div>

        <!-- Search & Filter -->
        <form method="GET" class="mb-8 flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama alat, merk, atau tipe..."
                    class="w-full border border-gray-300 rounded-2xl px-5 py-3 pl-12 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
            </div>

            <select name="kategori_id"
                class="border border-gray-300 rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">-- Semua Kategori --</option>
                @foreach ($kategoris ?? [] as $k)
                    <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-2xl font-medium transition">
                Filter
            </button>
        </form>

        <!-- Table -->
        <div class="bg-white rounded-3xl shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left font-medium">No</th>
                        <th class="px-6 py-4 text-left font-medium">Nama Alat</th>
                        <th class="px-6 py-4 text-left font-medium">Merk</th>
                        <th class="px-6 py-4 text-left font-medium">Tipe</th>
                        <th class="px-6 py-4 text-left font-medium">Kategori</th>
                        <th class="px-6 py-4 text-center font-medium">Masa Berlaku</th>
                        <th class="px-6 py-4 text-center font-medium">Status</th>
                        <th class="px-6 py-4 text-center font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($alats as $index => $alat)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-5">{{ $alats->firstItem() + $index }}</td>
                            <td class="px-6 py-5 font-medium">{{ $alat->nama_alat }}</td>
                            <td class="px-6 py-5">{{ $alat->merk }}</td>
                            <td class="px-6 py-5">{{ $alat->tipe }}</td>
                            <td class="px-6 py-5">{{ $alat->kategori->nama ?? '-' }}</td>
                            <td class="px-6 py-5 text-center">{{ $alat->masa_berlaku }}</td>
                            <td class="px-6 py-5 text-center">
                                @if ($alat->status == 'expired')
                                    <span
                                        class="inline-block px-4 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">Expired</span>
                                @elseif($alat->status == 'warning')
                                    <span
                                        class="inline-block px-4 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Warning</span>
                                @else
                                    <span
                                        class="inline-block px-4 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">OK</span>
                                @endif
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('alat.show', $alat->hashid) }}"
                                        class="p-2 bg-indigo-100 text-indigo-700 rounded-2xl hover:bg-indigo-200 transition">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('alat.edit', $alat->hashid) }}"
                                        class="p-2 bg-blue-100 text-blue-700 rounded-2xl hover:bg-blue-200 transition">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if (in_array($alat->status, ['expired', 'warning']))
                                        <a href="{{ route('kalibrasi.create', $alat->hashid) }}"
                                            class="p-2 bg-yellow-100 text-yellow-700 rounded-2xl hover:bg-yellow-200 transition">
                                            <i class="fas fa-tools"></i>
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('alat.destroy', $alat->hashid) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus alat ini?')">
                                        @csrf @method('DELETE')
                                        <button class="p-2 bg-red-100 text-red-700 rounded-2xl hover:bg-red-200 transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-16 text-gray-500">
                                <i class="fas fa-box-open text-5xl mb-4 block"></i>
                                Tidak ada data alat
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8 flex justify-center">
            {{ $alats->withQueryString()->links() }}
        </div>
    </div>
@endsection

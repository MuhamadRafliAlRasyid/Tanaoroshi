@extends('layouts.app')

@section('title', 'Pengembalian Alat')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-undo-alt text-green-600"></i>
                Pengembalian Alat
            </h2>


            @if (auth()->user()->role == 'admin')
                <a href="{{ route('pengambilan_alat.index') }}" class="bg-green-600 text-white px-6 py-3 rounded-xl">
                    + Pilih dari Pengambilan
                </a>
                <a href="{{ route('pengembalian_alat.export') }}"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-2xl font-medium flex items-center gap-2 transition">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            @endif
        </div>

        <!-- Search & Filter -->
        <form method="GET" class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama alat atau user..."
                    class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                    class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-3 rounded-2xl transition">
                Filter
            </button>
        </form>

        <!-- Table -->
        <div class="bg-white rounded-3xl shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left">No</th>
                        <th class="px-6 py-4 text-left">Alat</th>
                        <th class="px-6 py-4 text-left">User</th>
                        <th class="px-6 py-4 text-center">Jumlah Dikembalikan</th>
                        <th class="px-6 py-4 text-center">Tanggal</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($data as $i => $d)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-5 text-center">{{ $data->firstItem() + $i }}</td>
                            <td class="px-6 py-5">
                                <div class="font-medium">{{ $d->pengambilan->alat->nama_alat ?? '-' }}</div>
                                <small class="text-gray-500">
                                    {{ $d->pengambilan->alat->merk ?? '' }}
                                    {{ $d->pengambilan->alat->tipe ? " - {$d->pengambilan->alat->tipe}" : '' }}
                                </small>
                            </td>
                            <td class="px-6 py-5">{{ $d->user->name ?? '-' }}</td>
                            <td class="px-6 py-5 text-center font-semibold text-green-600">{{ $d->jumlah }}</td>
                            <td class="px-6 py-5 text-center text-sm text-gray-600">{{ $d->tanggal_pengembalian }}</td>
                            <td class="px-6 py-5">

                                <div class="flex justify-center gap-2">

                                    <a href="{{ route('pengembalian_alat.show', $d->hashid) }}"
                                        class="p-2 bg-blue-100 text-blue-700 rounded-2xl hover:bg-blue-200 transition">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pengembalian_alat.edit', $d->hashid) }}"
                                        class="p-2 bg-amber-100 text-amber-700 rounded-2xl hover:bg-amber-200 transition">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('pengembalian_alat.destroy', $d->hashid) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
                            <td colspan="6" class="text-center py-16 text-gray-500">
                                <i class="fas fa-undo-alt text-5xl mb-4 block"></i>
                                Belum ada data pengembalian alat
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8 flex justify-center">
            {{ $data->links() }}
        </div>
    </div>
@endsection

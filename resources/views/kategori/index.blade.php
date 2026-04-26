@extends('layouts.app')

@section('title', 'Daftar Kategori')

@section('content')
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Daftar Kategori</h1>
                <p class="text-gray-500 mt-1">Kelola kategori alat dan sparepart</p>
            </div>
            <a href="{{ route('kategori.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2 font-medium transition">
                <i class="fas fa-plus"></i> Tambah Kategori
            </a>
        </div>

        <!-- Search -->
        <form method="GET" class="flex gap-2">

            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori..."
                class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">

            <button class="bg-blue-600 text-white px-4 rounded-2xl">
                Cari
            </button>

            <a href="{{ route('kategori.index') }}" class="bg-gray-300 px-4 rounded-2xl flex items-center">
                Reset
            </a>

        </form>

        <!-- Tabel -->
        <div class="bg-white rounded-3xl shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-8 py-5 font-medium text-gray-600">Nama Kategori</th>
                        <th class="text-left px-8 py-5 font-medium text-gray-600">Keterangan</th>
                        <th class="text-center px-8 py-5 font-medium text-gray-600 w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($data as $kategori)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-8 py-5 font-medium text-gray-800">{{ $kategori->nama }}</td>
                            <td class="px-8 py-5 text-gray-600">
                                {{ $kategori->keterangan ? Str::limit($kategori->keterangan, 80) : '-' }}
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('kategori.show', $kategori->hashid) }}"
                                        class="text-blue-600 hover:text-blue-700">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('kategori.edit', $kategori->hashid) }}"
                                        class="text-amber-600 hover:text-amber-700">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('kategori.destroy', $kategori->hashid) }}"
                                        class="inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-12 text-gray-500">
                                Belum ada data kategori.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-center">
            {{ $data->links() }}
        </div>
    </div>
@endsection

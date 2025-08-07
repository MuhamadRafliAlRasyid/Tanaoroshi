@extends('layouts.app')

@section('title', 'Sparepart List')

@section('content')
    <div class="flex items-center justify-center min-h-screen">
        <div class="w-full max-w-4xl">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Daftar Sparepart</h2>

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form Pencarian -->
            <form action="{{ route('spareparts.index') }}" method="GET" class="mb-4">
                <div class="flex">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama part, model, atau merk..."
                        class="w-full border border-gray-300 rounded-l-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-r-md">
                        Cari
                    </button>
                </div>
            </form>

            <a href="{{ route('spareparts.create') }}"
                class="mb-4 inline-block bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md py-2 px-4">Tambah
                Sparepart</a>

            <div class="overflow-x-auto">
                <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="py-2 px-4 text-left text-gray-700">Nama Part</th>
                            <th class="py-2 px-4 text-left text-gray-700">Model</th>
                            <th class="py-2 px-4 text-left text-gray-700">Merk</th>
                            <th class="py-2 px-4 text-left text-gray-700">Jumlah Baru</th>
                            <th class="py-2 px-4 text-left text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($spareparts as $sparepart)
                            <tr class="border-b">
                                <td class="py-2 px-4">{{ $sparepart->nama_part }}</td>
                                <td class="py-2 px-4">{{ $sparepart->model }}</td>
                                <td class="py-2 px-4">{{ $sparepart->merk }}</td>
                                <td class="py-2 px-4">{{ $sparepart->jumlah_baru }}</td>
                                <td class="py-2 px-4">
                                    <a href="{{ route('spareparts.show', $sparepart->id) }}"
                                        class="text-indigo-600 hover:underline mr-2">Lihat</a>
                                    <a href="{{ route('spareparts.edit', $sparepart->id) }}"
                                        class="text-yellow-600 hover:underline mr-2">Edit</a>
                                    <form action="{{ route('spareparts.destroy', $sparepart->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline"
                                            onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-2 px-4 text-center">Tidak ada data sparepart.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $spareparts->links('pagination::tailwind') }}
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Daftar Pengambilan Sparepart')

@section('content')
    <main class="p-6 flex flex-col items-center space-y-8">
        <section class="w-full max-w-4xl bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-2">Daftar Pengambilan Sparepart</h1>
            @if (session('success'))
                <div
                    class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-md text-center">
                    {{ session('success') }}
                </div>
            @endif
            <div class="mb-4 text-right">
                <a href="{{ route('pengambilan.create') }}"
                    class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i> Tambah Pengambilan
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-gray-700">
                    <thead class="bg-gray-100 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2 text-center">No</th>
                            <th class="px-4 py-2">User</th>
                            <th class="px-4 py-2">Bagian</th>
                            <th class="px-4 py-2">Sparepart</th>
                            <th class="px-4 py-2 text-center">Jumlah</th>
                            <th class="px-4 py-2">Satuan</th>
                            <th class="px-4 py-2">Keperluan</th>
                            <th class="px-4 py-2">Waktu Pengambilan</th>
                            <th class="px-4 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengambilanSpareparts as $index => $item)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-4 py-2 text-center">{{ $index + 1 }}</td>
                                <td class="px-4 py-2">{{ $item->user->name ?? 'Tidak diketahui' }}</td>
                                <td class="px-4 py-2">{{ $item->bagian->nama ?? 'Tidak diketahui' }}</td>
                                <td class="px-4 py-2">{{ $item->sparepart->nama_part ?? 'Tidak diketahui' }}</td>
                                <td class="px-4 py-2 text-center">{{ $item->jumlah }}</td>
                                <td class="px-4 py-2">{{ $item->satuan }}</td>
                                <td class="px-4 py-2">{{ $item->keperluan }}</td>
                                <td class="px-4 py-2">{{ $item->waktu_pengambilan }}</td>
                                <td class="px-4 py-2 text-center flex justify-center space-x-2">
                                    <a href="{{ route('pengambilan.edit', $item->id) }}"
                                        class="text-yellow-600 hover:text-yellow-800">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('pengambilan.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin hapus data ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-4 text-center text-gray-500">Tidak ada data pengambilan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection

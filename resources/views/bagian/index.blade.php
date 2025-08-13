@extends('layouts.app')

@section('title', 'Bagian List')

@section('content')
    <div class="flex items-center justify-center min-h-screen bg-gray-100 py-6">
        <div class="w-full max-w-5xl">
            <h2 class="text-3xl font-bold text-indigo-700 mb-6 text-center border-b-2 border-indigo-200 pb-2">
                <i class="fas fa-list mr-2"></i>Daftar Bagian
            </h2>

            @if (session('success'))
                <div
                    class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-md text-center">
                    {{ session('success') }}
                </div>
            @endif

            <a href="{{ route('bagian.create') }}"
                class="mb-6 inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-md shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1">
                <i class="fas fa-plus mr-2"></i>Tambah Bagian
            </a>

            <div class="overflow-x-auto bg-white rounded-lg shadow-lg">
                <table class="w-full text-left">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Nama Bagian</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bagians as $bagian)
                            <tr class="border-b hover:bg-gray-50 transition duration-200">
                                <td class="py-3 px-4">{{ $bagian->nama }}</td>
                                <td class="py-3 px-4 flex space-x-2">
                                    <a href="{{ route('bagian.show', $bagian->id) }}"
                                        class="text-indigo-600 hover:text-indigo-800 font-medium">
                                        <i class="fas fa-eye mr-1"></i>Lihat
                                    </a>
                                    <a href="{{ route('bagian.edit', $bagian->id) }}"
                                        class="text-yellow-600 hover:text-yellow-800 font-medium">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <form action="{{ route('bagian.destroy', $bagian->id) }}" method="POST"
                                        style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                                            <i class="fas fa-trash mr-1"></i>Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-4 text-center text-gray-500">Tidak ada data bagian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

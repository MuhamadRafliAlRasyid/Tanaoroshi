@extends('layouts.app')

@section('title', 'Pengambilan Alat')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-hand-holding"></i>
                Pengambilan Alat
            </h2>

            <a href="{{ route('pengambilan_alat.create') }}"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-medium flex items-center gap-2 transition shadow-md">
                <i class="fas fa-plus"></i> Ambil Alat Baru
            </a>
        </div>

        <!-- Search -->
        <div class="mb-6">
            <form method="GET">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari alat, user, atau bagian..."
                        class="w-full border border-gray-300 rounded-2xl px-5 py-3 pl-12 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-3xl shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left font-medium">No</th>
                        <th class="px-6 py-4 text-left font-medium">Alat</th>
                        <th class="px-6 py-4 text-left font-medium">User</th>
                        <th class="px-6 py-4 text-left font-medium">Bagian</th>
                        <th class="px-6 py-4 text-center font-medium">Status</th>
                        <th class="px-6 py-4 text-center font-medium">Waktu Pengambilan</th>
                        <th class="px-6 py-4 text-center font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($data as $i => $d)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-5">{{ $data->firstItem() + $i }}</td>
                            <td class="px-6 py-5 font-medium">{{ $d->alat->nama_alat }}</td>
                            <td class="px-6 py-5">{{ $d->user->name }}</td>
                            <td class="px-6 py-5">{{ $d->bagian->nama }}</td>
                            <td class="px-6 py-5 text-center">
                                @if ($d->status == 'dipinjam')
                                    <span
                                        class="inline-block px-4 py-1.5 bg-red-100 text-red-700 text-xs font-semibold rounded-full">
                                        Dipinjam
                                    </span>
                                @else
                                    <span
                                        class="inline-block px-4 py-1.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                        Sudah Kembali
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-center text-sm text-gray-600">
                                {{ $d->waktu_pengambilan }}
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('pengambilan_alat.show', $d->hashid) }}"
                                        class="p-2 bg-blue-100 text-blue-700 rounded-xl hover:bg-blue-200 transition">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if (auth()->user()->role == 'admin')
                                        <a href="{{ route('pengambilan_alat.edit', $d->hashid) }}"
                                            class="p-2 bg-amber-100 text-amber-700 rounded-xl hover:bg-amber-200 transition">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('pengambilan_alat.destroy', $d->hashid) }}"
                                            onsubmit="return confirm('Yakin ingin menghapus?')">
                                            @csrf @method('DELETE')
                                            <button
                                                class="p-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-500">
                                <i class="fas fa-box-open text-4xl mb-3 block"></i>
                                Belum ada data pengambilan alat
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-center">
            {{ $data->links() }}
        </div>
    </div>
@endsection

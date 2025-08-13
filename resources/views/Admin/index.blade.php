@extends('layouts.app')

@section('title', 'Daftar User')

@section('content')
    <main class="p-6 flex flex-col items-center space-y-8">
        <!-- Tombol Tambah User -->
        <form action="{{ route('admin.create') }}" method="get" class="w-full max-w-4xl">
            <button type="submit"
                class="bg-[#1e53e4] text-white font-semibold text-lg px-6 py-3 rounded-lg shadow-md hover:bg-[#1749c6] transition duration-200 w-full max-w-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2"></i> Tambah User
            </button>
        </form>

        <!-- Tabel Daftar User -->
        <section class="w-full max-w-4xl bg-white rounded-lg shadow-lg overflow-hidden">
            <h1 class="text-2xl font-semibold text-gray-800 px-6 py-4 bg-gray-50 border-b">Daftar User</h1>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-gray-700">
                    <thead class="bg-gray-100 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-center">No</th>
                            <th class="px-4 py-3 pl-6">Nama</th>
                            <th class="px-4 py-3 pl-6">Email</th>
                            <th class="px-4 py-3 text-center">Bagian</th>
                            <th class="px-4 py-3 text-center">Role</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $index => $user)
                            <tr class="border-b hover:bg-gray-50 transition duration-150">
                                <td class="px-4 py-3 text-center">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 pl-6 break-words">{{ $user->name }}</td>
                                <td class="px-4 py-3 pl-6 break-words">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-center">{{ $user->bagian->nama ?? '-' }}</td>
                                <td class="px-4 py-3 text-center capitalize">{{ $user->role }}</td>
                                <td class="px-4 py-3 text-center flex justify-center space-x-2">
                                    <a href="{{ route('admin.edit', $user->id) }}"
                                        class="bg-blue-100 text-blue-600 px-3 py-1 rounded-md text-sm font-medium hover:bg-blue-200 transition">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin hapus user ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-100 text-red-600 px-3 py-1 rounded-md text-sm font-medium hover:bg-red-200 transition">
                                            <i class="fas fa-trash mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-4 text-center text-gray-500">Tidak ada data user.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection

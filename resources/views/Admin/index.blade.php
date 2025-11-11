@extends('layouts.app')

@section('title', 'Daftar User')

@section('content')
    <main class="p-6 flex flex-col items-center space-y-8 bg-gradient-to-br from-gray-100 to-white min-h-[calc(100vh-4rem)]">
        <section
            class="w-full max-w-5xl bg-white rounded-xl shadow-2xl p-6 transform transition-all duration-300 hover:shadow-3xl">
            <h2 class="text-3xl font-bold text-indigo-800 mb-6 border-b-2 border-indigo-200 pb-3 flex items-center">
                Daftar User
            </h2>

            @if (session('success'))
                <div
                    class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-md text-center animate-fade-in">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Search and Action -->
            <div class="mb-6 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0 md:space-x-4">
                <form action="{{ route('admin.index') }}" method="GET" class="w-full md:w-auto">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama, email, atau bagian..."
                            class="w-full md:w-72 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm pr-10">
                        <button type="submit"
                            class="absolute right-2 top-2 text-gray-500 hover:text-indigo-600 focus:outline-none">
                            Search
                        </button>
                    </div>
                </form>

                <a href="{{ route('admin.create') }}"
                    class="bg-green-600 text-white font-semibold px-4 py-2 rounded-lg shadow-md hover:bg-green-700 transition transform hover:scale-105 flex items-center">
                    Tambah User
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-gray-700 border-collapse">
                    <thead class="bg-gray-200 text-xs uppercase font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-center border-b">No</th>
                            <th class="px-4 py-3 border-b">Nama</th>
                            <th class="px-4 py-3 border-b">Email</th>
                            <th class="px-4 py-3 text-center border-b">Bagian</th>
                            <th class="px-4 py-3 text-center border-b">Role</th>
                            <th class="px-4 py-3 text-center border-b">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $index => $user)
                            <tr class="border-b hover:bg-gray-50 transition-all duration-200">
                                <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 break-words text-gray-900 font-medium">{{ $user->name }}</td>
                                <td class="px-4 py-3 break-words text-gray-700">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ $user->bagian?->nama ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="px-3 py-1 text-xs font-semibold rounded-full
                                        @if ($user->role === 'admin') bg-purple-100 text-purple-800
                                        @elseif($user->role === 'super') bg-red-100 text-red-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center flex justify-center space-x-2">
                                    <!-- EDIT -->
                                    <a href="{{ route('admin.edit', $user->hashid) }}"
                                        class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm font-medium hover:bg-blue-200 transition-all duration-200 transform hover:scale-105 relative group"
                                        title="Edit User">
                                        Edit
                                    </a>

                                    <!-- DELETE -->
                                    <form action="{{ route('admin.destroy', $user->hashid) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus user {{ $user->name }}?');"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm font-medium hover:bg-red-200 transition-all duration-200 transform hover:scale-105 relative group"
                                            title="Hapus User">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    Belum ada data user.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 text-center">
                {{ $users->links('pagination::tailwind') }}
            </div>
        </section>
    </main>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }
    </style>
@endsection

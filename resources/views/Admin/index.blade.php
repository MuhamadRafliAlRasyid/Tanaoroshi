@extends('layouts.app')

@section('content')
    <main class="p-6 flex flex-col items-center space-y-12">
        <form action="{{ route('admin.create') }}" method="get">
            <button class="bg-[#1e53e4] text-white text-lg px-16 py-6 w-full max-w-md" type="submit">
                Tambah User
            </button>
        </form>

        <section class="bg-[#1e49e2] text-white w-full max-w-4xl rounded-md shadow-lg overflow-x-auto">
            <h1 class="text-3xl font-normal px-6 pt-6 pb-4 text-center">Daftar User</h1>
            <ul class="min-w-[800px]">
                <li
                    class="flex justify-between items-center border-b border-white border-opacity-50 py-3 font-semibold text-white bg-[#1641c2] sticky top-0 z-10">
                    <div class="w-1/12 text-center text-lg">No</div>
                    <div class="w-2/12 text-lg pl-3">Nama</div>
                    <div class="w-3/12 text-lg pl-3">Email</div>
                    <div class="w-2/12 text-center text-lg">Bagian</div>
                    <div class="w-2/12 text-center text-lg">Role</div>
                    <div class="w-2/12 text-center text-lg">Aksi</div>
                </li>
                @foreach ($users as $index => $user)
                    <li
                        class="flex justify-between items-center border-b border-white border-opacity-30 py-3 hover:bg-[#2a5ae8] transition">
                        <div class="w-1/12 text-center text-base">{{ $index + 1 }}</div>
                        <div class="w-2/12 text-base pl-3 break-words">{{ $user->name }}</div>
                        <div class="w-3/12 text-base pl-3 break-words">{{ $user->email }}</div>
                        <div class="w-2/12 text-center text-base">{{ $user->bagian->nama ?? '-' }}</div>
                        <div class="w-2/12 text-center text-base capitalize">{{ $user->role }}</div>
                        <div class="w-2/12 flex space-x-3 justify-center">
                            <a href="{{ route('admin.edit', $user->id) }}"
                                class="bg-white text-[#1e53e4] px-4 py-2 rounded-md text-sm font-semibold shadow hover:bg-gray-100 transition">Edit</a>
                            <form action="{{ route('admin.destroy', $user->id) }}" method="POST"
                                onsubmit="return confirm('Yakin hapus user ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-600 px-4 py-2 rounded-md text-sm font-semibold shadow hover:bg-red-700 transition">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    </main>
@endsection

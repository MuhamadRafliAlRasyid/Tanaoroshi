@extends('layouts.app')

@section('title', 'Tambah Pengambilan Sparepart')

@section('content')
    <main class="p-6 flex flex-col items-center space-y-8">
        <section class="w-full max-w-4xl bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-2">Tambah Pengambilan Sparepart</h1>
            <form action="{{ route('pengambilan.store') }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">
                @csrf

                <!-- User -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">User</label>
                    <select id="user_id" name="user_id" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Bagian -->
                <div>
                    <label for="bagian_id" class="block text-sm font-medium text-gray-700 mb-1">Bagian</label>
                    <select id="bagian_id" name="bagian_id" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach ($bagians as $bagian)
                            <option value="{{ $bagian->id }}">{{ $bagian->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sparepart -->
                <div>
                    <label for="spareparts_id" class="block text-sm font-medium text-gray-700 mb-1">Sparepart</label>
                    <select id="spareparts_id" name="spareparts_id" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach ($spareparts as $sparepart)
                            <option value="{{ $sparepart->id }}">{{ $sparepart->nama_part }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Jumlah -->
                <div>
                    <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                    <input id="jumlah" name="jumlah" type="number" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <!-- Satuan -->
                <div>
                    <label for="satuan" class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                    <input id="satuan" name="satuan" type="text" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <!-- Keperluan -->
                <div class="col-span-full">
                    <label for="keperluan" class="block text-sm font-medium text-gray-700 mb-1">Keperluan</label>
                    <input id="keperluan" name="keperluan" type="text" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <!-- Waktu Pengambilan -->
                <div class="col-span-full">
                    <label for="waktu_pengambilan" class="block text-sm font-medium text-gray-700 mb-1">Waktu
                        Pengambilan</label>
                    <input id="waktu_pengambilan" name="waktu_pengambilan" type="datetime-local" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="col-span-full flex items-center gap-4 mt-6">
                    <button type="submit"
                        class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                    <a href="{{ route('pengambilan.index') }}"
                        class="text-gray-600 font-semibold hover:text-blue-600 transition">
                        <i class="fas fa-times mr-2"></i> Batal
                    </a>
                </div>
            </form>
        </section>
    </main>
@endsection

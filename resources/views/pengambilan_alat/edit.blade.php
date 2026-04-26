@extends('layouts.app')

@section('title', 'Edit Pengambilan Alat')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-3xl shadow-xl p-8">

            <!-- Header -->
            <div class="flex items-center gap-3 mb-8">
                <i class="fas fa-edit text-3xl text-amber-600"></i>
                <h2 class="text-2xl font-bold text-gray-800">Edit Pengambilan Alat</h2>
            </div>

            <form method="POST" action="{{ route('pengambilan_alat.update', $data->hashid) }}">
                @csrf
                @method('PUT')

                <div class="space-y-6">

                    <!-- Pilih Alat -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Alat</label>
                        <select name="alat_id" required
                            class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach ($alats as $alat)
                                <option value="{{ $alat->hashid }}"
                                    {{ old('alat_id', $data->alat_id) == $alat->id ? 'selected' : '' }}>
                                    {{ $alat->nama_alat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pilih Bagian -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bagian / Departemen</label>
                        <select name="bagian_id" required
                            class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach ($bagians as $b)
                                <option value="{{ $b->id }}"
                                    {{ old('bagian_id', $data->bagian_id) == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Keperluan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keperluan Pengambilan</label>
                        <textarea name="keperluan" rows="4" required
                            class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Jelaskan keperluan penggunaan alat...">{{ old('keperluan', $data->keperluan) }}</textarea>
                    </div>

                    <!-- Waktu Pengambilan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Pengambilan</label>
                        <input type="datetime-local" name="waktu_pengambilan"
                            value="{{ old('waktu_pengambilan', \Carbon\Carbon::parse($data->waktu_pengambilan)->format('Y-m-d\TH:i')) }}"
                            class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" value="{{ $data->jumlah }}" class="border w-full mb-3">
                    </div>

                    <div>
                        <label>Satuan</label>
                        <input type="text" name="satuan" value="{{ $data->satuan }}" class="border w-full mb-3">
                    </div>

                </div>

                <!-- Tombol Aksi -->
                <div class="mt-10 flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-2xl transition shadow-md">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>

                    <a href="{{ route('pengambilan_alat.index') }}"
                        class="flex-1 text-center border border-gray-300 hover:bg-gray-50 font-medium py-4 rounded-2xl transition">
                        Batal
                    </a>
                </div>
            </form>

        </div>
    </div>
@endsection

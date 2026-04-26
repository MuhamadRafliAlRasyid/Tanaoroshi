@extends('layouts.app')

@section('title', 'Edit Pengembalian')

@section('content')
    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-3xl shadow-xl p-8">

            <div class="flex items-center gap-3 mb-8">
                <i class="fas fa-edit text-3xl text-amber-600"></i>
                <h2 class="text-2xl font-bold">Edit Pengembalian Alat</h2>
            </div>

            <form method="POST" action="{{ route('pengembalian_alat.update', $data->hashid) }}">
                @csrf
                @method('PUT')

                <div class="space-y-6">

                    <div>
                        <label class="block text-sm font-medium mb-2">Jumlah Dikembalikan</label>
                        <input type="number" name="jumlah" value="{{ old('jumlah', $data->jumlah) }}"
                            class="w-full border border-gray-300 rounded-2xl px-5 py-3" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Keterangan</label>
                        <textarea name="keterangan" rows="4" class="w-full border border-gray-300 rounded-2xl px-5 py-3">{{ old('keterangan', $data->keterangan) }}</textarea>
                    </div>

                </div>

                <div class="mt-10 flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-2xl transition">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('pengembalian_alat.index') }}"
                        class="flex-1 text-center border py-4 rounded-2xl font-medium">
                        Batal
                    </a>
                </div>
            </form>

        </div>
    </div>
@endsection

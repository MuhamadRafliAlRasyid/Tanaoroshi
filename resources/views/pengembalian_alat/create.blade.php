@extends('layouts.app')

@section('title', 'Pengembalian Alat')

@section('content')
    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-3xl shadow-xl p-8">

            <div class="flex items-center gap-3 mb-8">
                <i class="fas fa-undo-alt text-3xl text-green-600"></i>
                <div>
                    <h2 class="text-2xl font-bold">Pengembalian Alat</h2>
                    <p class="text-gray-600">{{ $pengambilan->alat->nama_alat ?? 'Alat' }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('pengembalian_alat.store', $pengambilan->hashid) }}">
                @csrf

                <div class="space-y-6">

                    <div>
                        <label class="block text-sm font-medium mb-2">Jumlah Dikembalikan</label>
                        <input type="number" name="jumlah" placeholder="Masukkan jumlah yang dikembalikan"
                            class="w-full border border-gray-300 rounded-2xl px-5 py-3" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Keterangan</label>
                        <textarea name="keterangan" rows="4" placeholder="Catatan tambahan (opsional)"
                            class="w-full border border-gray-300 rounded-2xl px-5 py-3"></textarea>
                    </div>

                </div>

                <div class="mt-10">
                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-4 rounded-2xl transition">
                        Simpan Pengembalian
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection

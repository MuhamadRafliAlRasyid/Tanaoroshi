@extends('layouts.app')

@section('title', 'Ambil Alat')

@section('content')
    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-3xl shadow p-8">
            <h2 class="text-2xl font-bold mb-8">Ambil Alat</h2>

            <form method="POST" action="{{ route('pengambilan_alat.store') }}">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium mb-2">Pilih Alat</label>
                        <select name="alat_id" required class="w-full border rounded-2xl px-4 py-3">
                            @foreach ($alats as $alat)
                                <option value="{{ $alat->hashid }}">
                                    {{ $alat->nama_alat }} | {{ $alat->merk }} | {{ $alat->tipe }} |
                                    {{ $alat->no_seri }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Bagian</label>
                        <select name="bagian_id" required class="w-full border rounded-2xl px-4 py-3">
                            @foreach ($bagians as $b)
                                <option value="{{ $b->id }}">{{ $b->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" class="border w-full mb-3">
                    </div>

                    <div>
                        <label>Satuan</label>
                        <input type="text" name="satuan" class="border w-full mb-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Keperluan</label>
                        <textarea name="keperluan" rows="4" required class="w-full border rounded-2xl px-4 py-3"
                            placeholder="Tulis keperluan penggunaan alat..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Waktu Pengambilan</label>
                        <input type="datetime-local" name="waktu_pengambilan" class="w-full border rounded-2xl px-4 py-3">
                    </div>
                </div>

                <div class="mt-10 flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-2xl transition">
                        Simpan Pengambilan
                    </button>
                    <a href="{{ route('pengambilan_alat.index') }}"
                        class="flex-1 text-center border border-gray-300 py-4 rounded-2xl font-medium">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

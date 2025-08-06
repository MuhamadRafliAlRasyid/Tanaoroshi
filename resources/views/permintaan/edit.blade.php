@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Edit Permintaan</h2>

        <form action="{{ route('permintaan.update', $permintaan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="barang_id" class="block font-medium">Barang</label>
                <select name="barang_id" id="barang_id" class="w-full border rounded px-3 py-2">
                    @foreach ($barangs as $barang)
                        <option value="{{ $barang->id }}" {{ $permintaan->barang_id == $barang->id ? 'selected' : '' }}>
                            {{ $barang->nama_barang }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="jumlah" class="block font-medium">Jumlah</label>
                <input type="number" name="jumlah" id="jumlah" class="w-full border rounded px-3 py-2"
                    value="{{ $permintaan->jumlah }}">
            </div>

            <div class="mb-4">
                <label for="keterangan" class="block font-medium">Keterangan</label>
                <textarea name="keterangan" id="keterangan" class="w-full border rounded px-3 py-2">{{ $permintaan->keterangan }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto mt-10 bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Edit Beberapa Permintaan</h2>

        <form action="{{ route('permintaan.user.updateMultiple') }}" method="POST">
            @csrf
            @method('PUT')

            @foreach ($permintaans as $permintaan)
                <div class="border rounded p-4 mb-4">
                    {{-- <h3 class="font-semibold mb-2">Permintaan #{{ $permintaan->id }}</h3> --}}

                    @foreach ($permintaan->details as $detail)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                            <input type="hidden" name="permintaans[{{ $permintaan->id }}][details][{{ $detail->id }}][id]"
                                value="{{ $detail->id }}">

                            <div>
                                <label class="block font-medium">Barang</label>
                                <select name="permintaans[{{ $permintaan->id }}][details][{{ $detail->id }}][barang_id]"
                                    class="w-full border rounded px-3 py-2">
                                    @foreach ($barangs as $barang)
                                        <option value="{{ $barang->id }}"
                                            {{ $detail->barang_id == $barang->id ? 'selected' : '' }}>
                                            {{ $barang->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium">Jumlah</label>
                                <input type="number"
                                    name="permintaans[{{ $permintaan->id }}][details][{{ $detail->id }}][jumlah]"
                                    class="w-full border rounded px-3 py-2" value="{{ $detail->jumlah }}">
                            </div>

                            <div>
                                <label class="block font-medium">Satuan</label>
                                <input type="text"
                                    name="permintaans[{{ $permintaan->id }}][details][{{ $detail->id }}][satuan]"
                                    class="w-full border rounded px-3 py-2" value="{{ $detail->satuan }}">
                            </div>

                            <div>
                                <label class="block font-medium">Tanggal</label>
                                <input type="date"
                                    name="permintaans[{{ $permintaan->id }}][details][{{ $detail->id }}][tanggal]"
                                    class="w-full border rounded px-3 py-2" value="{{ $detail->tanggal }}">
                            </div>

                            <div>
                                <label class="block font-medium">Keterangan</label>
                                <input type="text"
                                    name="permintaans[{{ $permintaan->id }}][details][{{ $detail->id }}][keterangan]"
                                    class="w-full border rounded px-3 py-2" value="{{ $detail->keterangan }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update
                    Semua</button>
            </div>
        </form>
    </div>
@endsection

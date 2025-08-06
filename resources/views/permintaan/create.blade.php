@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold text-blue-700 mb-4">Ajukan Permintaan Barang</h2>

        <form action="{{ route('permintaan.store') }}" method="POST">
            @csrf

            <div id="barang-form-wrapper">
                <div class="barang-form mb-6 border-b pb-4">
                    <!-- Nama Barang -->
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Nama Barang</label>
                        <select name="barang_id[]" class="w-full border border-gray-300 px-4 py-2 rounded" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($barangs as $barang)
                                <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jumlah -->
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Jumlah</label>
                        <input type="number" name="jumlah[]" class="w-full border border-gray-300 px-4 py-2 rounded"
                            min="1" required>
                    </div>

                    <!-- Satuan (Baru) -->
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Satuan</label>
                        <input type="text" name="satuan[]" class="w-full border border-gray-300 px-4 py-2 rounded"
                            placeholder="Contoh: pcs, liter, box">
                    </div>

                    <!-- Tanggal (Baru) -->
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Tanggal Dibutuhkan</label>
                        <input type="date" name="tanggal[]" class="w-full border border-gray-300 px-4 py-2 rounded">
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Keterangan</label>
                        <textarea name="keterangan[]" class="w-full border border-gray-300 px-4 py-2 rounded" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <button type="button" onclick="addBarangForm()"
                class="mb-4 text-sm bg-gray-200 px-3 py-1 rounded hover:bg-gray-300">+ Tambah Barang</button>

            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Ajukan Permintaan
                </button>
            </div>
        </form>
    </div>

    <script>
        function addBarangForm() {
            const wrapper = document.getElementById('barang-form-wrapper');
            const newForm = wrapper.firstElementChild.cloneNode(true);
            Array.from(newForm.querySelectorAll('input, select, textarea')).forEach(field => {
                field.value = '';
            });
            wrapper.appendChild(newForm);
        }
    </script>
@endsection

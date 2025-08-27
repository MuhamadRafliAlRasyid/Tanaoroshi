@extends('layouts.app')

@section('title', 'Edit Sparepart')

@section('content')
    <div class="flex items-center justify-center min-h-screen bg-gray-100 py-6">
        <div class="w-full max-w-2xl">
            <h2 class="text-3xl font-bold text-yellow-700 mb-6 text-center border-b-2 border-yellow-200 pb-2">
                <i class="fas fa-edit mr-2"></i>Edit Sparepart
            </h2>

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-md text-center">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('spareparts.update', $sparepart->id) }}" method="POST"
                class="bg-white p-6 rounded-lg shadow-lg space-y-4" id="sparepartForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="nama_part" class="block text-sm font-medium text-gray-700">Nama Part</label>
                        <input type="text" id="nama_part" name="nama_part"
                            value="{{ old('nama_part', $sparepart->nama_part) }}" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="form-group">
                        <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
                        <input type="text" id="model" name="model" value="{{ old('model', $sparepart->model) }}"
                            required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="form-group">
                        <label for="merk" class="block text-sm font-medium text-gray-700">Merk</label>
                        <input type="text" id="merk" name="merk" value="{{ old('merk', $sparepart->merk) }}"
                            required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="form-group">
                        <label for="jumlah_baru" class="block text-sm font-medium text-gray-700">Jumlah Baru</label>
                        <input type="number" id="jumlah_baru" name="jumlah_baru"
                            value="{{ old('jumlah_baru', $sparepart->jumlah_baru) }}" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="form-group">
                        <label for="jumlah_bekas" class="block text-sm font-medium text-gray-700">Jumlah Bekas</label>
                        <input type="number" id="jumlah_bekas" name="jumlah_bekas"
                            value="{{ old('jumlah_bekas', $sparepart->jumlah_bekas) }}" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="form-group">
                        <label for="supplier" class="block text-sm font-medium text-gray-700">Supplier</label>
                        <input type="text" id="supplier" name="supplier"
                            value="{{ old('supplier', $sparepart->supplier) }}" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="form-group">
                        <label for="patokan_harga" class="block text-sm font-medium text-gray-700">Patokan Harga</label>
                        <input type="text" id="patokan_harga" name="patokan_harga" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm"
                            value="{{ old('patokan_harga', $sparepart->patokan_harga ? 'Rp ' . number_format((float) preg_replace('/[^0-9.]/', '', $sparepart->patokan_harga ?? '0'), 2, '.', ',') : '') }}"
                            placeholder="Masukkan nominal">
                    </div>
                    <div class="form-group">
                        <label for="total" class="block text-sm font-medium text-gray-700">Total</label>
                        <input type="text" id="total" name="total" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm"
                            value="{{ old('total', $sparepart->total ? 'Rp ' . number_format((float) preg_replace('/[^0-9.]/', '', $sparepart->total ?? '0'), 2, '.', ',') : '') }}"
                            placeholder="Masukkan nominal">
                    </div>
                    <div class="form-group">
                        <label for="ruk_no" class="block text-sm font-medium text-gray-700">RUK No</label>
                        <input type="text" id="ruk_no" name="ruk_no" value="{{ old('ruk_no', $sparepart->ruk_no) }}"
                            required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="form-group">
                        <label for="purchase_date" class="block text-sm font-medium text-gray-700">Purchase Date</label>
                        <input type="date" id="purchase_date" name="purchase_date"
                            value="{{ old('purchase_date', $sparepart->purchase_date) }}" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="form-group">
                        <label for="delivery_date" class="block text-sm font-medium text-gray-700">Delivery Date</label>
                        <input type="date" id="delivery_date" name="delivery_date"
                            value="{{ old('delivery_date', $sparepart->delivery_date) }}" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="form-group">
                        <label for="po_number" class="block text-sm font-medium text-gray-700">PO Number</label>
                        <input type="text" id="po_number" name="po_number"
                            value="{{ old('po_number', $sparepart->po_number) }}" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="form-group">
                        <label for="titik_pesanan" class="block text-sm font-medium text-gray-700">Titik Pesanan</label>
                        <input type="text" id="titik_pesanan" name="titik_pesanan"
                            value="{{ old('titik_pesanan', $sparepart->titik_pesanan) }}" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="form-group">
                        <label for="jumlah_pesanan" class="block text-sm font-medium text-gray-700">Jumlah Pesanan</label>
                        <input type="number" id="jumlah_pesanan" name="jumlah_pesanan"
                            value="{{ old('jumlah_pesanan', $sparepart->jumlah_pesanan) }}" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="form-group">
                        <label for="cek" class="block text-sm font-medium text-gray-700">Cek</label>
                        <select id="cek" name="cek" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                            <option value="1" {{ old('cek', $sparepart->cek) == 1 ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ old('cek', $sparepart->cek) == 0 ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="pic" class="block text-sm font-medium text-gray-700">PIC</label>
                        <input type="text" id="pic" name="pic" value="{{ old('pic', $sparepart->pic) }}"
                            required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="form-group">
                        <label for="location" class="block text-sm font-medium text-gray-700">Lokasi</label>
                        <input type="text" id="location" name="location"
                            value="{{ old('location', $sparepart->location) }}"
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm"
                            placeholder="Contoh: Lemari A-1">
                    </div>
                    <div class="form-group">
                        <label for="qr_code" class="block text-sm font-medium text-gray-700">QR Code (Opsional)</label>
                        <input type="text" id="qr_code" name="qr_code"
                            value="{{ old('qr_code', $sparepart->qr_code) }}"
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-md shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                        <i class="fas fa-save mr-2"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const formatRupiah = (input) => {
                    let value = input.value.replace(/[^0-9.]/g, ''); // Hanya ambil angka dan titik
                    if (value === '') value = '0';
                    let number = parseFloat(value) || 0;
                    input.value = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(number).replace('Rp', 'Rp ');
                };

                const unformatRupiah = (input) => {
                    let value = input.value.replace(/[^0-9.]/g, ''); // Hanya ambil angka dan titik
                    if (value === '') value = '0';
                    input.value = value; // Mengembalikan ke format numerik sebelum submit
                };

                ['patokan_harga', 'total'].forEach(id => {
                    const input = document.getElementById(id);
                    // Tampilkan nilai awal yang sudah diformat saat halaman dimuat
                    if (input.value) {
                        formatRupiah(input);
                    }
                    input.addEventListener('input', () => formatRupiah(input));
                    input.addEventListener('blur', () => {
                        if (input.value === 'Rp 0.00') input.value = '';
                    });
                    input.addEventListener('focus', () => {
                        if (input.value === 'Rp 0.00' || input.value === '') input.value = '';
                    });
                });

                document.getElementById('sparepartForm').addEventListener('submit', (e) => {
                    ['patokan_harga', 'total'].forEach(id => {
                        const input = document.getElementById(id);
                        unformatRupiah(input);
                    });
                });
            });
        </script>
    @endpush
@endsection

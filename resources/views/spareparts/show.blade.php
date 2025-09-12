@extends('layouts.app')

@section('title', 'Detail Sparepart')

@section('content')
    <div class="flex items-center justify-center min-h-screen bg-gray-100 py-6">
        <div class="w-full max-w-3xl">
            <div class="bg-white shadow-xl rounded-lg p-6">
                <h2 class="text-3xl font-bold text-indigo-700 mb-6 text-center border-b-2 border-indigo-200 pb-2">
                    <i class="fas fa-info-circle mr-2"></i> Detail Sparepart
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-tag mr-2"></i>Nama Part:</p>
                        <p class="text-lg text-gray-800">{{ $sparepart->nama_part }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-cogs mr-2"></i>Model:</p>
                        <p class="text-lg text-gray-800">{{ $sparepart->model }}</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-trademark mr-2"></i>Merk:</p>
                        <p class="text-lg text-gray-800">{{ $sparepart->merk }}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-box-open mr-2"></i>Jumlah Baru:</p>
                        <p class="text-lg text-gray-800">{{ $sparepart->jumlah_baru }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-box mr-2"></i>Jumlah Bekas:</p>
                        <p class="text-lg text-gray-800">{{ $sparepart->jumlah_bekas }}</p>
                    </div>
                    <div class="bg-indigo-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-truck mr-2"></i>Supplier:</p>
                        <p class="text-lg text-gray-800">{{ $sparepart->supplier }}</p>
                    </div>
                    <div class="bg-teal-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-dollar-sign mr-2"></i>Patokan Harga:</p>
                        <p class="text-lg text-gray-800">
                            Rp
                            {{ number_format((float) preg_replace('/[^0-9.]/', '', $sparepart->patokan_harga ?? '0'), 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-money-bill-wave mr-2"></i>Total:</p>
                        <p class="text-lg text-gray-800">
                            Rp
                            {{ number_format((float) preg_replace('/[^0-9.]/', '', $sparepart->total ?? '0'), 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-pink-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-warehouse mr-2"></i>RUK No:</p>
                        <p class="text-lg text-gray-800">{{ $sparepart->ruk_no }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-calendar-alt mr-2"></i>Purchase Date:</p>
                        <p class="text-lg text-gray-800">{{ $sparepart->purchase_date ?: 'Tidak ada' }}</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-truck-loading mr-2"></i>Delivery Date:</p>
                        <p class="text-lg text-gray-800">{{ $sparepart->delivery_date ?: 'Tidak ada' }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-file-alt mr-2"></i>PO Number:</p>
                        <p class="text-lg text-gray-800">{{ $sparepart->po_number }}</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-map-marker-alt mr-2"></i>Titik Pesanan:</p>
                        <p class="text-lg text-gray-800">{{ $sparepart->titik_pesanan }}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-shopping-cart mr-2"></i>Jumlah Pesanan:</p>
                        <p class="text-lg text-gray-800">{{ $sparepart->jumlah_pesanan }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-check-circle mr-2"></i>Cek:</p>
                        <p class="text-lg text-gray-800">{{ $sparepart->cek ? 'Ya' : 'Tidak' }}</p>
                    </div>
                    <div class="bg-indigo-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-user mr-2"></i>PIC:</p>
                        <p class="text-lg text-gray-800">{{ $sparepart->pic ?: 'Tidak ada' }}</p>
                    </div>
                    <div class="bg-teal-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-map-pin mr-2"></i>Lokasi:</p>
                        <p class="text-lg text-gray-800">{{ $sparepart->location ?: 'Tidak ada' }}</p>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg shadow-md col-span-2">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-qrcode mr-2"></i>QR Code:</p>
                        @if ($sparepart->qr_code && file_exists(storage_path('app/public/' . $sparepart->qr_code)))
                            <img src="{{ asset('storage/' . $sparepart->qr_code) }}" alt="QR Code"
                                class="w-64 h-64 mx-auto">
                            <a href="{{ asset('storage/' . $sparepart->qr_code) }}" download
                                class="mt-2 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-center w-full">
                                <i class="fas fa-download mr-2"></i>Download QR Code
                            </a>
                            <a href="{{ route('spareparts.pdf', $sparepart->id) }}"
                                class="mt-2 inline-block bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 text-center w-full">
                                <i class="fas fa-download mr-2"></i>Download PDF
                            </a>
                        @else
                            <p class="text-red-500 text-center">QR Code belum di-generate.</p>
                        @endif
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <a href="{{ route('spareparts.index') }}"
                        class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

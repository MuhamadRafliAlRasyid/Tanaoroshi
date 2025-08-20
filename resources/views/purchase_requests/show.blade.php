@extends('layouts.app')

@section('title', 'Detail Purchase Request')

@section('content')
    <main class="p-6 flex flex-col items-center space-y-8 bg-gradient-to-br from-gray-100 to-white min-h-[calc(100vh-4rem)]">
        <section
            class="w-full max-w-4xl bg-white rounded-xl shadow-2xl p-6 transform transition-all duration-300 hover:shadow-3xl">
            <h1 class="text-3xl font-bold text-indigo-800 mb-6 border-b-2 border-indigo-200 pb-3 flex items-center">
                <i class="fas fa-info-circle mr-2 text-indigo-600"></i> Detail Purchase Request
            </h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700">
                <!-- Nama Part -->
                <div class="bg-gray-50 p-4 rounded-lg shadow-inner">
                    <p class="font-semibold text-indigo-600">Nama Part:</p>
                    <p class="mt-1 text-lg font-medium">{{ $purchaseRequest->nama_part }}</p>
                </div>

                <!-- Part Number -->
                <div class="bg-gray-50 p-4 rounded-lg shadow-inner">
                    <p class="font-semibold text-indigo-600">Part Number:</p>
                    <p class="mt-1 text-lg font-medium">{{ $purchaseRequest->part_number }}</p>
                </div>

                <!-- Link Website -->
                <div class="col-span-full bg-gray-50 p-4 rounded-lg shadow-inner">
                    <p class="font-semibold text-indigo-600">Link Website:</p>
                    <p class="mt-1 text-lg font-medium break-words">{{ $purchaseRequest->link_website ?? 'Tidak ada' }}</p>
                </div>

                <!-- Waktu Request -->
                <div class="bg-gray-50 p-4 rounded-lg shadow-inner">
                    <p class="font-semibold text-indigo-600">Tanggal Request:</p>
                    <p class="mt-1 text-lg font-medium">{{ $purchaseRequest->waktu_request }}</p>
                </div>

                <!-- Quantity -->
                <div class="bg-gray-50 p-4 rounded-lg shadow-inner">
                    <p class="font-semibold text-indigo-600">Quantity:</p>
                    <p class="mt-1 text-lg font-medium">{{ $purchaseRequest->quantity }}</p>
                </div>

                <!-- Satuan -->
                <div class="bg-gray-50 p-4 rounded-lg shadow-inner">
                    <p class="font-semibold text-indigo-600">Satuan:</p>
                    <p class="mt-1 text-lg font-medium">{{ $purchaseRequest->satuan }}</p>
                </div>

                <!-- Mas Deliver -->
                <div class="bg-gray-50 p-4 rounded-lg shadow-inner">
                    <p class="font-semibold text-indigo-600">Masa Delivery:</p>
                    <p class="mt-1 text-lg font-medium">{{ $purchaseRequest->mas_deliver }}</p>
                </div>

                <!-- Untuk Apa -->
                <div class="col-span-full bg-gray-50 p-4 rounded-lg shadow-inner">
                    <p class="font-semibold text-indigo-600">Keterangan:</p>
                    <p class="mt-1 text-lg font-medium">{{ $purchaseRequest->untuk_apa }}</p>
                </div>

                <!-- PIC -->
                <div class="bg-gray-50 p-4 rounded-lg shadow-inner">
                    <p class="font-semibold text-indigo-600">PIC:</p>
                    <p class="mt-1 text-lg font-medium">{{ $purchaseRequest->pic }}</p>
                </div>

                <!-- Quotation Lead Time -->
                <div class="bg-gray-50 p-4 rounded-lg shadow-inner">
                    <p class="font-semibold text-indigo-600">Quotation Lead Time:</p>
                    <p class="mt-1 text-lg font-medium">{{ $purchaseRequest->quotation_lead_time ?? 'Tidak ada' }}</p>
                </div>

                <!-- Status -->
                <div class="bg-gray-50 p-4 rounded-lg shadow-inner">
                    <p class="font-semibold text-indigo-600">Status:</p>
                    <p
                        class="mt-1 text-lg font-medium {{ $purchaseRequest->status === 'Completed' ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ $purchaseRequest->status }}
                    </p>
                </div>

                <!-- User -->
                <div class="col-span-full bg-gray-50 p-4 rounded-lg shadow-inner">
                    <p class="font-semibold text-indigo-600">User:</p>
                    <p class="mt-1 text-lg font-medium">{{ $purchaseRequest->user->name }}</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex justify-between items-center">
                <a href="{{ route('purchase_requests.index') }}"
                    class="flex items-center text-gray-700 font-semibold hover:text-indigo-700 transition duration-300 transform hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <div class="space-x-4">
                    @if ($purchaseRequest->status !== 'Completed')
                        <a href="{{ route('purchase_requests.edit', $purchaseRequest->id) }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-700 transition transform hover:scale-105">
                            <i class="fas fa-edit mr-2"></i> Edit
                        </a>
                    @endif
                    <button onclick="window.print()"
                        class="bg-gray-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-gray-700 transition transform hover:scale-105">
                        <i class="fas fa-print mr-2"></i> Cetak
                    </button>
                </div>
            </div>
        </section>
    </main>
@endsection

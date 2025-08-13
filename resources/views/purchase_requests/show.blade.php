@extends('layouts.app')

@section('title', 'Detail Purchase Request')

@section('content')
    <main class="p-6 flex flex-col items-center space-y-8">
        <section class="w-full max-w-4xl bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-2">Detail Purchase Request</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">
                <div>
                    <p class="font-medium">Nama Part:</p>
                    <p>{{ $purchaseRequest->nama_part }}</p>
                </div>
                <div>
                    <p class="font-medium">Part Number:</p>
                    <p>{{ $purchaseRequest->part_number }}</p>
                </div>
                <div class="col-span-full">
                    <p class="font-medium">Link Website:</p>
                    <p>{{ $purchaseRequest->link_website ?? 'Tidak ada' }}</p>
                </div>
                <div>
                    <p class="font-medium">Waktu Request:</p>
                    <p>{{ $purchaseRequest->waktu_request }}</p>
                </div>
                <div>
                    <p class="font-medium">Quantity:</p>
                    <p>{{ $purchaseRequest->quantity }}</p>
                </div>
                <div>
                    <p class="font-medium">Satuan:</p>
                    <p>{{ $purchaseRequest->satuan }}</p>
                </div>
                <div>
                    <p class="font-medium">Mas Deliver:</p>
                    <p>{{ $purchaseRequest->mas_deliver }}</p>
                </div>
                <div class="col-span-full">
                    <p class="font-medium">Untuk Apa:</p>
                    <p>{{ $purchaseRequest->untuk_apa }}</p>
                </div>
                <div>
                    <p class="font-medium">PIC:</p>
                    <p>{{ $purchaseRequest->pic }}</p>
                </div>
                <div>
                    <p class="font-medium">Quotation Lead Time:</p>
                    <p>{{ $purchaseRequest->quotation_lead_time ?? 'Tidak ada' }}</p>
                </div>
                <div>
                    <p class="font-medium">Status:</p>
                    <p>{{ $purchaseRequest->status }}</p>
                </div>
                <div class="col-span-full">
                    <p class="font-medium">User:</p>
                    <p>{{ $purchaseRequest->user->name }}</p>
                </div>
            </div>
            <div class="mt-6 text-right">
                <a href="{{ route('purchase_requests.index') }}"
                    class="text-gray-600 font-semibold hover:text-blue-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </section>
    </main>
@endsection

@extends('layouts.app')

@section('title', 'Tambah Purchase Request')

@section('content')
    <main class="p-6 flex flex-col items-center space-y-8">
        <section class="w-full max-w-4xl bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-2">Tambah Purchase Request</h1>
            <form action="{{ route('purchase_requests.store') }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">
                @csrf

                <!-- Nama Part -->
                <div>
                    <label for="nama_part" class="block text-sm font-medium text-gray-700 mb-1">Nama Part</label>
                    <input id="nama_part" name="nama_part" type="text" value="{{ old('nama_part', $nama_part) }}" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        @if ($nama_part) readonly @endif />
                    @error('nama_part')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Part Number -->
                <div>
                    <label for="part_number" class="block text-sm font-medium text-gray-700 mb-1">Part Number</label>
                    <input id="part_number" name="part_number" type="text" value="{{ old('part_number', $part_number) }}"
                        required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        @if ($part_number) readonly @endif />
                    @error('part_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Link Website -->
                <div class="col-span-full">
                    <label for="link_website" class="block text-sm font-medium text-gray-700 mb-1">Link Website
                        (Opsional)</label>
                    <input id="link_website" name="link_website" type="url" value="{{ old('link_website') }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    @error('link_website')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Waktu Request -->
                <div>
                    <label for="waktu_request" class="block text-sm font-medium text-gray-700 mb-1">Waktu Request</label>
                    <input id="waktu_request" name="waktu_request" type="date" value="{{ old('waktu_request') }}"
                        required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    @error('waktu_request')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quantity -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input id="quantity" name="quantity" type="number" min="1" value="{{ old('quantity') }}"
                        required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    @error('quantity')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Satuan -->
                <div>
                    <label for="satuan" class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                    <input id="satuan" name="satuan" type="text" value="{{ old('satuan') }}" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    @error('satuan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mas Deliver -->
                <div>
                    <label for="mas_deliver" class="block text-sm font-medium text-gray-700 mb-1">Mas Deliver</label>
                    <input id="mas_deliver" name="mas_deliver" type="date" value="{{ old('mas_deliver') }}" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    @error('mas_deliver')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Untuk Apa -->
                <div class="col-span-full">
                    <label for="untuk_apa" class="block text-sm font-medium text-gray-700 mb-1">Untuk Apa</label>
                    <input id="untuk_apa" name="untuk_apa" type="text" value="{{ old('untuk_apa') }}" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    @error('untuk_apa')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PIC -->
                <div>
                    <label for="pic" class="block text-sm font-medium text-gray-700 mb-1">PIC</label>
                    <input id="pic" name="pic" type="text" value="{{ old('pic') }}" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    @error('pic')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quotation Lead Time -->
                <div>
                    <label for="quotation_lead_time" class="block text-sm font-medium text-gray-700 mb-1">Quotation Lead
                        Time (Opsional)</label>
                    <input id="quotation_lead_time" name="quotation_lead_time" type="text"
                        value="{{ old('quotation_lead_time') }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    @error('quotation_lead_time')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-full flex items-center gap-4 mt-6">
                    <button type="submit"
                        class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                    <a href="{{ route('purchase_requests.index') }}"
                        class="text-gray-600 font-semibold hover:text-blue-600 transition">
                        <i class="fas fa-times mr-2"></i> Batal
                    </a>
                </div>
            </form>
        </section>
    </main>
@endsection

@extends('layouts.app')

@section('title', 'Edit Kalibrasi')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-3xl shadow-xl p-8">

            <!-- HEADER -->
            <div class="flex items-center gap-3 mb-8">
                <i class="fas fa-tools text-3xl text-orange-600"></i>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Edit Kalibrasi</h2>
                    <p class="text-gray-600">
                        {{ $data->alat->nama_alat ?? '-' }}
                    </p>
                </div>
            </div>

            <!-- ERROR VALIDATION -->
            @if ($errors->any())
                <div class="mb-6 bg-red-100 text-red-700 p-4 rounded-xl">
                    <ul class="list-disc ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- FORM -->
            <form method="POST" action="{{ route('kalibrasi.update', $data->hashid) }}">
                @csrf
                @method('PUT')

                <div class="space-y-6">

                    <!-- TANGGAL KALIBRASI -->
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Tanggal Kalibrasi <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_kalibrasi"
                            value="{{ old('tanggal_kalibrasi', $data->tanggal_kalibrasi) }}" required
                            class="w-full border rounded-2xl px-5 py-3 focus:ring-2 focus:ring-orange-500">
                    </div>

                    <!-- MASA BERLAKU -->
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Masa Berlaku Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="masa_berlaku_baru"
                            value="{{ old('masa_berlaku_baru', $data->masa_berlaku_baru) }}" required
                            class="w-full border rounded-2xl px-5 py-3 focus:ring-2 focus:ring-orange-500">
                    </div>

                    <!-- NO SERTIFIKAT -->
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            No Sertifikat
                        </label>
                        <input type="text" name="no_sertifikat" value="{{ old('no_sertifikat', $data->no_sertifikat) }}"
                            class="w-full border rounded-2xl px-5 py-3 focus:ring-2 focus:ring-orange-500">
                    </div>

                    <!-- KETERANGAN -->
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Keterangan
                        </label>
                        <textarea name="keterangan" rows="4"
                            class="w-full border rounded-2xl px-5 py-3 focus:ring-2 focus:ring-orange-500">{{ old('keterangan', $data->keterangan) }}</textarea>
                    </div>

                </div>

                <!-- BUTTON -->
                <div class="mt-10 flex gap-4">

                    <button type="submit"
                        class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-semibold py-4 rounded-2xl transition">
                        <i class="fas fa-save mr-2"></i> Update Kalibrasi
                    </button>

                    <a href="{{ route('kalibrasi.show', $data->hashid) }}"
                        class="flex-1 text-center border border-gray-300 py-4 rounded-2xl font-medium">
                        Batal
                    </a>

                </div>
            </form>

        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Kalibrasi Alat')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-3xl shadow-xl p-8">

            <h2 class="text-2xl font-bold mb-6">
                Kalibrasi: {{ $alat->nama_alat }}
            </h2>

            <form method="POST" action="{{ route('kalibrasi.store', $alat->hashid) }}">
                @csrf

                <div class="space-y-4">

                    <div>
                        <label>Tanggal Kalibrasi</label>
                        <input type="date" name="tanggal_kalibrasi" class="w-full border p-3 rounded">
                    </div>

                    <div>
                        <label>Masa Berlaku Baru</label>
                        <input type="date" name="masa_berlaku_baru" class="w-full border p-3 rounded">
                    </div>

                    <div>
                        <label>No Sertifikat</label>
                        <input type="text" name="no_sertifikat" class="w-full border p-3 rounded">
                    </div>

                    <div>
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="w-full border p-3 rounded"></textarea>
                    </div>

                </div>

                <button class="mt-6 bg-orange-600 text-white px-6 py-3 rounded">
                    Simpan Kalibrasi
                </button>
            </form>

        </div>
    </div>
@endsection

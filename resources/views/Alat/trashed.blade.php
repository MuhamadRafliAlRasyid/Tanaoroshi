@extends('layouts.app')

@section('title', 'Data Alat Terhapus')

@section('content')
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold text-red-700 mb-6">Data Alat Terhapus</h2>

        @if ($data->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($data as $alat)
                    <div class="bg-white rounded-3xl shadow p-6">
                        <h3 class="font-semibold">{{ $alat->nama_alat }}</h3>
                        <p class="text-sm text-gray-500">{{ $alat->merk }} - {{ $alat->tipe }}</p>

                        <div class="mt-6">
                            <form method="POST" action="{{ route('alat.restore', $alat->hashid) }}">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-2xl font-medium">
                                    Restore Alat
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-12">Tidak ada data terhapus.</p>
        @endif
    </div>
@endsection

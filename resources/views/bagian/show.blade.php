@extends('layouts.app')

@section('title', 'Detail Bagian')

@section('content')
    <div class="flex items-center justify-center min-h-screen bg-gray-100 py-6">
        <div class="w-full max-w-2xl">
            <div class="bg-white shadow-xl rounded-lg p-6">
                <h2 class="text-3xl font-bold text-indigo-700 mb-6 text-center border-b-2 border-indigo-200 pb-2">
                    <i class="fas fa-info-circle mr-2"></i>Detail Bagian
                </h2>

                <div class="grid grid-cols-1 gap-4">
                    <div class="bg-indigo-50 p-4 rounded-lg shadow-md">
                        <p class="text-gray-600 font-semibold"><i class="fas fa-tag mr-2"></i>Nama Bagian:</p>
                        <p class="text-lg text-gray-800">
                            {{ $bagian->nama ?? 'Data nama tidak tersedia (ID: ' . ($bagian->id ?? 'tidak diketahui') . ')' }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <a href="{{ route('bagian.index') }}"
                        class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-md shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Create Bagian')

@section('content')
    <div class="flex items-center justify-center min-h-screen bg-gray-100 py-6">
        <div class="w-full max-w-2xl">
            <h2 class="text-3xl font-bold text-green-700 mb-6 text-center border-b-2 border-green-200 pb-2">
                <i class="fas fa-plus-circle mr-2"></i>Tambah Bagian Baru
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

            <form action="{{ route('bagian.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-lg space-y-4">
                @csrf

                <div class="form-group">
                    <label for="nama" class="block text-sm font-medium text-gray-700">Nama Bagian</label>
                    <input type="text" id="nama" name="nama" required
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                </div>

                <div class="mt-6 text-center">
                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-md shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.app')
@section('content')

    <div class="max-w-xl mx-auto mt-10 p-6 bg-white border border-gray-200 rounded-xl shadow-sm">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Edit Barang</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('barangs.update', $barang->id) }}" enctype="multipart/form-data" class="space-y-5"
            novalidate>
            @csrf
            @method('PUT')

            <div>
                <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                <input type="text" name="nama_barang" id="nama_barang"
                    value="{{ old('nama_barang', $barang->nama_barang) }}" required maxlength="255"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-600 @error('nama_barang') border-red-500 @enderror" />
                @error('nama_barang')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="stok" class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                <input type="number" name="stok" id="stok" value="{{ old('stok', $barang->stok) }}" required
                    min="0"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-600 @error('stok') border-red-500 @enderror" />
                @error('stok')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="foto_barang" class="block text-sm font-medium text-gray-700 mb-1">Foto Barang</label>
                <input type="file" name="foto_barang" id="foto_barang" accept="image/*"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:border-0 file:rounded file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer @error('foto_barang') border-red-500 @enderror">
                @error('foto_barang')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div id="previewContainer" class="hidden mb-4">
                <p class="text-sm font-medium text-gray-700 mb-2">Preview Gambar Baru:</p>
                <img id="previewImage" src="#" alt="Preview" class="w-32 h-32 object-cover rounded shadow">
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="submit"
                    class="bg-blue-600 text-white text-sm font-medium px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600">
                    Update
                </button>
                <a href="{{ route('barangs.index') }}"
                    class="bg-gray-100 text-blue-600 text-sm font-medium px-6 py-2 rounded-md hover:underline hover:bg-gray-200">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        const fotoInput = document.getElementById('foto_barang');
        const previewContainer = document.getElementById('previewContainer');
        const previewImage = document.getElementById('previewImage');

        fotoInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                previewImage.src = '#';
                previewContainer.classList.add('hidden');
            }
        });
    </script>
@endsection

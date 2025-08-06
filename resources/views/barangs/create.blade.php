@extends('layouts.app')

@section('content')
    <section class="max-w-xl mx-auto mt-10 p-6 bg-white border border-gray-200 rounded-xl shadow-sm">
        <h2 class="text-sm font-semibold text-gray-700 mb-1">Informasi Barang</h2>
        <p class="text-xs text-gray-400 mb-6">Masukkan informasi barang di bawah ini.</p>

        <form method="POST" action="{{ route('barangs.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="nama_barang" class="block text-xs font-semibold text-gray-700 mb-1">Nama Barang</label>
                <input id="nama_barang" name="nama_barang" type="text" placeholder="Masukkan nama barang"
                    class="w-full rounded-md border border-gray-300 text-xs text-gray-600 placeholder:text-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600"
                    required>
            </div>

            <div>
                <label for="stok" class="block text-xs font-semibold text-gray-700 mb-1">Stok</label>
                <input id="stok" name="stok" type="number" min="0" placeholder="Masukkan jumlah stok"
                    class="w-full rounded-md border border-gray-300 text-xs text-gray-600 placeholder:text-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600"
                    required>
            </div>

            <div>
                <label for="foto_barang" class="block text-xs font-semibold text-gray-700 mb-1">Foto Barang</label>
                <input id="foto_barang" name="foto_barang" type="file" accept="image/*"
                    class="w-full rounded-md border border-gray-300 text-xs px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600"
                    required>
            </div>

            <div class="text-center">
                <img id="preview" src="#" alt="Preview Gambar" class="w-full max-h-60 rounded-md shadow hidden" />
            </div>

            <button type="submit"
                class="bg-blue-600 text-white text-xs font-semibold rounded-md px-4 py-2 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600">
                Simpan
            </button>
        </form>
    </section>
    </main>

    <script>
        document.getElementById('foto_barang').addEventListener('change', function(event) {
            const [file] = event.target.files;
            const preview = document.getElementById('preview');
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
            } else {
                preview.src = '#';
                preview.classList.add('hidden');
            }
        });
    </script>
@endsection

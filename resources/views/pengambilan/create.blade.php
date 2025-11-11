@extends('layouts.app')

@section('title', 'Tambah Pengambilan Sparepart')

@section('content')
    <main class="p-6 flex flex-col items-center space-y-8">
        <section class="w-full max-w-4xl bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-2">Tambah Pengambilan Sparepart</h1>
            <form action="{{ route('pengambilan.store') }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">
                @csrf

                {{-- User & Bagian --}}
                @if (Auth::user()->role === 'admin')
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">User</label>
                        <select id="user_id" name="user_id" required class="w-full border rounded-md px-3 py-2">
                            @foreach ($users as $user)
                                <option value="{{ $user->hashid }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="bagian_id" class="block text-sm font-medium text-gray-700 mb-1">Bagian</label>
                        <select id="bagian_id" name="bagian_id" required class="w-full border rounded-md px-3 py-2">
                            @foreach ($bagians as $bagian)
                                <option value="{{ $bagian->hashid }}">{{ $bagian->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                        <input type="text" value="{{ Auth::user()->name }}" readonly
                            class="w-full border rounded-md px-3 py-2 bg-gray-100 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bagian</label>
                        <input type="text" value="{{ Auth::user()->bagian->nama ?? 'Tidak ada bagian' }}" readonly
                            class="w-full border rounded-md px-3 py-2 bg-gray-100 cursor-not-allowed">
                    </div>
                @endif

                {{-- Sparepart --}}
                {{-- Sparepart --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sparepart</label>

                    @if ($qrSparepartHashid)
                        <input type="hidden" name="spareparts_id" value="{{ $qrSparepartHashid }}">
                        <input type="text" value="{{ $spareparts->first()->nama_part ?? 'Tidak ditemukan' }}" readonly
                            class="w-full border rounded-md px-3 py-2 bg-gray-100 cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">
                            Stok Baru: {{ $spareparts->first()->jumlah_baru ?? 0 }} |
                            Stok Bekas: {{ $spareparts->first()->jumlah_bekas ?? 0 }}
                        </p>
                    @else
                        <select name="spareparts_id" required class="w-full border rounded-md px-3 py-2">
                            @foreach ($spareparts as $sparepart)
                                <option value="{{ $sparepart->hashid }}">{{ $sparepart->nama_part }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                {{-- Jenis Part --}}
                <div>
                    <label for="part_type" class="block text-sm font-medium text-gray-700 mb-1">Jenis Part</label>
                    <select id="part_type" name="part_type" required class="w-full border rounded-md px-3 py-2">
                        <option value="baru">Part Baru</option>
                        <option value="bekas">Part Bekas</option>
                    </select>
                </div>

                {{-- Jumlah --}}
                <div>
                    <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                    <input id="jumlah" name="jumlah" type="number" required
                        class="w-full border rounded-md px-3 py-2" />
                    <p id="jumlah-warning" class="text-sm text-red-600 mt-1 hidden">Jumlah melebihi stok!</p>
                </div>

                {{-- Satuan --}}
                <div>
                    <label for="satuan" class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                    <input id="satuan" name="satuan" type="text" required
                        class="w-full border rounded-md px-3 py-2" />
                </div>

                {{-- Keperluan --}}
                <div class="col-span-full">
                    <label for="keperluan" class="block text-sm font-medium text-gray-700 mb-1">Keperluan</label>
                    <input id="keperluan" name="keperluan" type="text" required
                        class="w-full border rounded-md px-3 py-2" />
                </div>

                {{-- Waktu Pengambilan --}}
                <div class="col-span-full">
                    <label for="waktu_pengambilan" class="block text-sm font-medium text-gray-700 mb-1">Waktu
                        Pengambilan</label>
                    <input id="waktu_pengambilan" name="waktu_pengambilan" type="datetime-local" required
                        class="w-full border rounded-md px-3 py-2" />
                </div>

                {{-- Tombol --}}
                <div class="col-span-full flex items-center gap-4 mt-6">
                    <button type="submit" id="submit-btn"
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        Simpan
                    </button>
                    <a href="{{ route('pengambilan.index') }}"
                        class="text-gray-600 font-semibold hover:text-blue-600">Batal</a>
                </div>
            </form>
        </section>
    </main>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const partTypeSelect = document.getElementById('part_type');
                const jumlahInput = document.getElementById('jumlah');
                const submitBtn = document.getElementById('submit-btn');
                const warningMsg = document.getElementById('jumlah-warning');
                const stockBaru = {{ $spareparts->first()->jumlah_baru ?? 0 }};
                const stockBekas = {{ $spareparts->first()->jumlah_bekas ?? 0 }};

                function checkStock() {
                    const jumlah = parseInt(jumlahInput.value) || 0;
                    const stock = (partTypeSelect.value === 'baru') ? stockBaru : stockBekas;
                    if (jumlah > stock) {
                        warningMsg.classList.remove('hidden');
                        submitBtn.disabled = true;
                    } else {
                        warningMsg.classList.add('hidden');
                        submitBtn.disabled = false;
                    }
                }

                partTypeSelect.addEventListener('change', checkStock);
                jumlahInput.addEventListener('input', checkStock);
                checkStock();
            });
        </script>
    @endpush
@endsection

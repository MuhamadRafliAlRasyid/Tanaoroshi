@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto p-6 bg-white rounded-2xl shadow">
        <h2 class="text-2xl font-bold text-blue-700 mb-6">Detail Permintaan</h2>

        <div class="mb-4">
            <p><strong>Nama User:</strong> {{ $permintaan->user->name }}</p>
            <p><strong>Tanggal Permintaan:</strong> {{ $permintaan->created_at->format('d M Y') }}</p>
        </div>

        <table class="w-full text-sm text-left text-gray-700 border border-gray-300">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="px-4 py-2">No</th>
                    <th class="px-4 py-2">Nama Barang</th>
                    <th class="px-4 py-2">Jumlah</th>
                    <th class="px-4 py-2">Satuan</th>
                    <th class="px-4 py-2">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($permintaan->details as $index => $detail)
                    <tr class="bg-white border-b">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">{{ $detail->barang->nama_barang }}</td>
                        <td class="px-4 py-2">{{ $detail->jumlah }}</td>
                        <td class="px-4 py-2">{{ $detail->satuan ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $detail->keterangan ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Tombol Mengetahui untuk SPV --}}
        @if (Auth::user()->hasRole('spv') && !$permintaan->mengetahui)
            <div class="mt-6">
                <form action="{{ route('permintaan.spv.setujui', $permintaan->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 text-sm font-semibold transition">
                        Mengetahui Permintaan
                    </button>
                </form>
            </div>
        @endif

        {{-- Tombol Setujui untuk Admin --}}
        @if (Auth::user()->hasRole('admin') && $permintaan->mengetahui && !$permintaan->approval)
            <div class="mt-6">
                <form action="{{ route('permintaan.admin.setujui', $permintaan->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-800 text-sm font-semibold transition">
                        Setujui Permintaan
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection

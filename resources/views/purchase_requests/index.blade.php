@extends('layouts.app')

@section('title', 'Daftar Purchase Request')

@section('content')
    <main class="p-6 flex flex-col items-center space-y-8">
        <section class="w-full max-w-4xl bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-2">Daftar Purchase Request</h1>
            @if (session('success'))
                <div
                    class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-md text-center">
                    {{ session('success') }}
                </div>
            @endif
            <div class="mb-4 text-right">
                <a href="{{ route('purchase_requests.create') }}"
                    class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i> Tambah Purchase Request
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-gray-700">
                    <thead class="bg-gray-100 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2 text-center">No</th>
                            <th class="px-4 py-2">User ID</th>
                            <th class="px-4 py-2">Nama Part</th>
                            <th class="px-4 py-2">Part Number</th>
                            <th class="px-4 py-2">Link Website</th>
                            <th class="px-4 py-2">Waktu Request</th>
                            <th class="px-4 py-2">Quantity</th>
                            <th class="px-4 py-2">Satuan</th>
                            <th class="px-4 py-2">Mas Deliver</th>
                            <th class="px-4 py-2">Untuk Apa</th>
                            <th class="px-4 py-2">PIC</th>
                            <th class="px-4 py-2">Quotation Lead Time</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Status Approval</th>
                            <th class="px-4 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchaseRequests as $index => $item)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-4 py-2 text-center">{{ $index + 1 }}</td>
                                <td class="px-4 py-2">{{ $item->user_id }}</td>
                                <td class="px-4 py-2">{{ $item->nama_part }}</td>
                                <td class="px-4 py-2">{{ $item->part_number }}</td>
                                <td class="px-4 py-2">
                                    @if ($item->link_website)
                                        <a href="{{ $item->link_website }}" target="_blank"
                                            class="text-blue-600 hover:underline">
                                            Lihat Website
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $item->waktu_request }}</td>
                                <td class="px-4 py-2">{{ $item->quantity }}</td>
                                <td class="px-4 py-2">{{ $item->satuan }}</td>
                                <td class="px-4 py-2">{{ $item->mas_deliver }}</td>
                                <td class="px-4 py-2">{{ $item->untuk_apa }}</td>
                                <td class="px-4 py-2">{{ $item->pic }}</td>
                                <td class="px-4 py-2">{{ $item->quotation_lead_time ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ $item->status }}</td>
                                <td class="px-4 py-2">
                                    @php
                                        $latestLog = $item->logs()->latest()->first();
                                        $approvalStatus =
                                            $latestLog && $latestLog->action === 'approved'
                                                ? 'Approved by ' .
                                                    ($latestLog->approvedBy->name ?? 'Unknown') .
                                                    ' on ' .
                                                    $latestLog->created_at
                                                : 'Pending';
                                    @endphp
                                    {{ $approvalStatus }}
                                </td>
                                <td class="px-4 py-2 text-center flex justify-center space-x-2">
                                    <a href="{{ route('purchase_requests.show', $item->id) }}"
                                        class="bg-indigo-100 text-indigo-600 px-3 py-1 rounded-full text-sm font-medium hover:bg-indigo-200 transition-all duration-200 transform hover:scale-105 relative group"
                                        title="Lihat Detail">
                                        <i class="fas fa-eye mr-1"></i> Lihat
                                    </a>
                                    <a href="{{ route('purchase_requests.edit', $item->id) }}"
                                        class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm font-medium hover:bg-blue-200 transition-all duration-200 transform hover:scale-105 relative group"
                                        title="Edit Pengajuan">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <form action="{{ route('purchase_requests.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin hapus data ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm font-medium hover:bg-red-200 transition-all duration-200 transform hover:scale-105 relative group"
                                            title="Hapus Pengajuan">
                                            <i class="fas fa-trash mr-1"></i> Hapus
                                        </button>
                                    </form>
                                    @if (Auth::user()->role === 'super' && $item->status === 'PR')
                                        <form action="{{ route('purchase_requests.approve', $item->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm font-medium hover:bg-green-200 transition-all duration-200 transform hover:scale-105 relative group"
                                                title="Setujui Pengajuan">
                                                <i class="fas fa-check mr-1"></i> Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('purchase_requests.reject', $item->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <input type="hidden" name="notes" value="Rejected">
                                            <button type="submit"
                                                class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm font-medium hover:bg-red-200 transition-all duration-200 transform hover:scale-105 relative group"
                                                title="Tolak Pengajuan">
                                                <i class="fas fa-times mr-1"></i> Tolak
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="px-4 py-4 text-center text-gray-500">Tidak ada data purchase
                                    request.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection

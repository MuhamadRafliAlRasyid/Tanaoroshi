@extends('layouts.app')

@section('title', 'Riwayat Alat: ' . $alat->nama_alat)

@section('content')
    <div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8" x-data="{
        tab: 'pengambilan',
        modalOpen: false,
        modalData: null,
        modalType: ''
    }">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-history text-amber-500"></i>
                    Riwayat Alat: <span class="text-amber-600">{{ $alat->nama_alat }}</span>
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $alat->merk }} | {{ $alat->tipe ?? 'Tanpa Tipe' }} |
                    Stok:
                    {{ $alat->jumlah }}</p>
            </div>
            <a href="{{ route('alats.show', $alat->hashid) }}"
                class="inline-flex items-center gap-2 mt-4 sm:mt-0 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg transition transform hover:scale-105">
                <i class="fas fa-arrow-left"></i> Kembali ke Detail Alat
            </a>
        </div>

        {{-- Tabs --}}
        <div
            class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 rounded-2xl shadow-sm border border-amber-100 overflow-hidden">
            <div class="border-b border-gray-100 dark:border-gray-700">
                <nav class="flex flex-wrap -mb-px">
                    <button @click="tab = 'pengambilan'"
                        :class="tab === 'pengambilan' ? 'border-amber-500 text-amber-600' :
                            'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700'"
                        class="px-6 py-4 font-medium text-sm border-b-2 transition flex items-center gap-2">
                        <i class="fas fa-hand-holding"></i> Pengambilan ({{ $pengambilan->count() }})
                    </button>
                    <button @click="tab = 'pengembalian'"
                        :class="tab === 'pengembalian' ? 'border-amber-500 text-amber-600' :
                            'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700'"
                        class="px-6 py-4 font-medium text-sm border-b-2 transition flex items-center gap-2">
                        <i class="fas fa-undo-alt"></i> Pengembalian ({{ $pengembalian->count() }})
                    </button>
                    <button @click="tab = 'kalibrasi'"
                        :class="tab === 'kalibrasi' ? 'border-amber-500 text-amber-600' :
                            'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700'"
                        class="px-6 py-4 font-medium text-sm border-b-2 transition flex items-center gap-2">
                        <i class="fas fa-calendar-check"></i> Kalibrasi ({{ $kalibrasis->count() }})
                    </button>
                </nav>
            </div>

            {{-- Tab Pengambilan --}}
            <div x-show="tab === 'pengambilan'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100" class="p-6">
                @if ($pengambilan->isEmpty())
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                        <p>Belum ada riwayat pengambilan</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-amber-50 text-amber-800">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                                    <th class="px-4 py-3 text-left font-semibold">Peminjam</th>
                                    <th class="px-4 py-3 text-left font-semibold">Bagian</th>
                                    <th class="px-4 py-3 text-left font-semibold">Jumlah</th>
                                    <th class="px-4 py-3 text-left font-semibold">Keperluan</th>
                                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($pengambilan as $item)
                                    <tr class="hover:bg-amber-50/50 transition cursor-pointer transform hover:scale-[1.01] hover:shadow-sm"
                                        @click="modalOpen = true; modalData = {{ json_encode($item) }}; modalType = 'pengambilan'">
                                        <td class="px-4 py-3">
                                            {{ \Carbon\Carbon::parse($item->waktu_pengambilan)->format('d M Y, H:i') }}</td>
                                        <td class="px-4 py-3 font-medium">
                                            {{ $item->nama_peminjam ?? ($item->user->name ?? '-') }}</td>
                                        <td class="px-4 py-3">{{ $item->bagian->nama ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $item->jumlah }} {{ $item->satuan }}</td>
                                        <td class="px-4 py-3">{{ $item->keperluan }}</td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2 py-1 rounded-full text-xs font-semibold animate-pulse
                                            {{ $item->status === 'dipinjam' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Tab Pengembalian --}}
            <div x-show="tab === 'pengembalian'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100" class="p-6">
                @if ($pengembalian->isEmpty())
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-undo-alt text-4xl mb-3 text-gray-300"></i>
                        <p>Belum ada riwayat pengembalian</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-green-50 text-green-800">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                                    <th class="px-4 py-3 text-left font-semibold">Pengembali</th>
                                    <th class="px-4 py-3 text-left font-semibold">Jumlah</th>
                                    <th class="px-4 py-3 text-left font-semibold">Keterangan</th>
                                    <th class="px-4 py-3 text-left font-semibold">Foto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($pengembalian as $item)
                                    <tr class="hover:bg-green-50/50 transition cursor-pointer transform hover:scale-[1.01] hover:shadow-sm"
                                        @click="modalOpen = true; modalData = {{ json_encode($item) }}; modalType = 'pengembalian'">
                                        <td class="px-4 py-3">
                                            {{ \Carbon\Carbon::parse($item->tanggal_pengembalian)->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-4 py-3 font-medium">
                                            {{ $item->nama_peminjam ?? ($item->user->name ?? '-') }}</td>
                                        <td class="px-4 py-3">{{ $item->jumlah }}</td>
                                        <td class="px-4 py-3">{{ $item->keterangan ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            @if ($item->foto)
                                                <span
                                                    class="text-blue-600 hover:underline flex items-center gap-1 cursor-pointer"
                                                    @click.stop="modalOpen = true; modalData = {{ json_encode($item) }}; modalType = 'foto'">
                                                    <i class="fas fa-image"></i> Lihat
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Tab Kalibrasi --}}
            <div x-show="tab === 'kalibrasi'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100" class="p-6">
                @if ($kalibrasis->isEmpty())
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-calendar-times text-4xl mb-3 text-gray-300"></i>
                        <p>Belum ada riwayat kalibrasi</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-blue-50 text-blue-800">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">Tanggal Kalibrasi</th>
                                    <th class="px-4 py-3 text-left font-semibold">Masa Berlaku</th>
                                    <th class="px-4 py-3 text-left font-semibold">No. Sertifikat</th>
                                    <th class="px-4 py-3 text-left font-semibold">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($kalibrasis as $item)
                                    <tr class="hover:bg-blue-50/50 transition cursor-pointer transform hover:scale-[1.01] hover:shadow-sm"
                                        @click="modalOpen = true; modalData = {{ json_encode($item) }}; modalType = 'kalibrasi'">
                                        <td class="px-4 py-3">
                                            {{ \Carbon\Carbon::parse($item->tanggal_kalibrasi)->format('d M Y') }}</td>
                                        <td class="px-4 py-3">
                                            {{ \Carbon\Carbon::parse($item->masa_berlaku_baru)->format('d M Y') }}</td>
                                        <td class="px-4 py-3">{{ $item->no_sertifikat ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $item->keterangan ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Modal Detail --}}
        <div x-show="modalOpen" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @click.self="modalOpen = false">
            <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6 relative"
                @click.stop>
                <button @click="modalOpen = false"
                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition text-xl">&times;</button>
                <template x-if="modalType === 'pengambilan' && modalData">
                    <div>
                        <h3 class="text-lg font-bold text-amber-600 mb-3">Detail Pengambilan</h3>
                        <p><strong>Peminjam:</strong> <span
                                x-text="modalData.nama_peminjam || modalData.user?.name || '-'"></span></p>
                        <p><strong>Bagian:</strong> <span x-text="modalData.bagian?.nama || '-'"></span></p>
                        <p><strong>Jumlah:</strong> <span
                                x-text="modalData.jumlah + ' ' + (modalData.satuan || '')"></span></p>
                        <p><strong>Keperluan:</strong> <span x-text="modalData.keperluan || '-'"></span></p>
                        <p><strong>Waktu:</strong> <span
                                x-text="new Date(modalData.waktu_pengambilan).toLocaleString('id-ID')"></span></p>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold mt-2 inline-block"
                            :class="modalData.status === 'dipinjam' ? 'bg-yellow-100 text-yellow-700' :
                                'bg-green-100 text-green-700'"
                            x-text="modalData.status"></span>
                    </div>
                </template>
                <template x-if="modalType === 'pengembalian' && modalData">
                    <div>
                        <h3 class="text-lg font-bold text-green-600 mb-3">Detail Pengembalian</h3>
                        <p><strong>Pengembali:</strong> <span
                                x-text="modalData.nama_peminjam || modalData.user?.name || '-'"></span></p>
                        <p><strong>Jumlah:</strong> <span x-text="modalData.jumlah"></span></p>
                        <p><strong>Keterangan:</strong> <span x-text="modalData.keterangan || '-'"></span></p>
                        <p><strong>Tanggal:</strong> <span
                                x-text="new Date(modalData.tanggal_pengembalian).toLocaleString('id-ID')"></span></p>
                        <template x-if="modalData.foto">
                            <img :src="'{{ asset('storage/pengembalian') }}/' + modalData.foto"
                                class="mt-2 rounded-lg max-h-48 object-cover" alt="Foto Pengembalian">
                        </template>
                    </div>
                </template>
                <template x-if="modalType === 'foto' && modalData">
                    <div>
                        <h3 class="text-lg font-bold text-blue-600 mb-3">Foto Pengembalian</h3>
                        <img :src="'{{ asset('storage/pengembalian') }}/' + modalData.foto"
                            class="rounded-xl max-h-96 object-contain w-full" alt="Foto">
                    </div>
                </template>
                <template x-if="modalType === 'kalibrasi' && modalData">
                    <div>
                        <h3 class="text-lg font-bold text-blue-600 mb-3">Detail Kalibrasi</h3>
                        <p><strong>Tanggal Kalibrasi:</strong> <span
                                x-text="new Date(modalData.tanggal_kalibrasi).toLocaleDateString('id-ID')"></span></p>
                        <p><strong>Masa Berlaku:</strong> <span
                                x-text="new Date(modalData.masa_berlaku_baru).toLocaleDateString('id-ID')"></span></p>
                        <p><strong>No. Sertifikat:</strong> <span x-text="modalData.no_sertifikat || '-'"></span></p>
                        <p><strong>Keterangan:</strong> <span x-text="modalData.keterangan || '-'"></span></p>
                    </div>
                </template>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

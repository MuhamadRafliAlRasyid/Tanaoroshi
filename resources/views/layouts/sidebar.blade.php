<aside class="w-64 bg-white shadow-md h-screen hidden md:block">
    <div class="p-4 text-xl font-semibold border-b">
        BuhinCore
    </div>
    <nav class="p-4 space-y-2">
        @if (Auth::user()->role === 'admin')
            <a href="#" class="block py-2 px-4 rounded hover:bg-gray-100">Dashboard Admin</a>
            <a href="#" class="block py-2 px-4 rounded hover:bg-gray-100">Manajemen User</a>
            <a href="#" class="block py-2 px-4 rounded hover:bg-gray-100">Laporan</a>
        @elseif(Auth::user()->role === 'gudang')
            <a href="#" class="block py-2 px-4 rounded hover:bg-gray-100">Dashboard Gudang</a>
            <a href="#" class="block py-2 px-4 rounded hover:bg-gray-100">Input Sparepart</a>
            <a href="#" class="block py-2 px-4 rounded hover:bg-gray-100">Riwayat Barang</a>
        @elseif(Auth::user()->role === 'kepala')
            <a href="#" class="block py-2 px-4 rounded hover:bg-gray-100">Dashboard Kepala</a>
            <a href="#" class="block py-2 px-4 rounded hover:bg-gray-100">Approval Barang</a>
            <a href="#" class="block py-2 px-4 rounded hover:bg-gray-100">Laporan Gudang</a>
        @endif
    </nav>
</aside>

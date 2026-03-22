<?php

namespace App\Http\Controllers\Api;

use App\Models\Spareparts;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SparepartController extends Controller
{
    public function index(Request $request)
    {
        $query = Spareparts::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('nama_part', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%");
        }

        $spareparts = $query->latest()->paginate(15);

        return response()->json([
            'status' => true,
            'data' => $spareparts->items(),
            'meta' => [
                'current_page' => $spareparts->currentPage(),
                'last_page' => $spareparts->lastPage(),
                'total' => $spareparts->total(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_part' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'jumlah_baru' => 'required|numeric|min:0',
            'jumlah_bekas' => 'required|numeric|min:0',
            'supplier' => 'required|string|max:255',
            'patokan_harga' => 'required|numeric',
            'ruk_no' => 'required|string|max:100',
            'purchase_date' => 'required|date',
            'delivery_date' => 'required|date',
        ]);

        $sparepart = Spareparts::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Sparepart berhasil ditambahkan',
            'data' => $sparepart
        ], 201);
    }

    public function show(Spareparts $sparepart)
    {
        return response()->json([
            'status' => true,
            'data' => $sparepart
        ]);
    }

    public function update(Request $request, Spareparts $sparepart)
    {
        $request->validate([
            'nama_part' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'jumlah_baru' => 'required|numeric',
            'jumlah_bekas' => 'required|numeric',
            'supplier' => 'required|string|max:255',
            'patokan_harga' => 'required|numeric',
            'ruk_no' => 'required|string|max:100',
        ]);

        $sparepart->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Sparepart berhasil diperbarui',
            'data' => $sparepart
        ]);
    }

    public function destroy(Spareparts $sparepart)
    {
        $sparepart->delete();
        return response()->json([
            'status' => true,
            'message' => 'Sparepart berhasil dihapus'
        ]);
    }

    public function checkStock()
    {
        $critical = Spareparts::whereColumn('jumlah_baru', '<=', 'titik_pesanan')->count();

        return response()->json([
            'status' => true,
            'critical_count' => $critical,
        ]);
    }

    public function regenerateQrCode(Spareparts $sparepart)
    {
        // Panggil logic QR generation Anda di sini
        // $this->generateQrCode($sparepart);

        return response()->json([
            'status' => true,
            'message' => 'QR Code berhasil diregenerate untuk ' . $sparepart->nama_part
        ]);
    }

    public function generateAllQrCodes()
    {
        // Logic generate semua QR
        return response()->json([
            'status' => true,
            'message' => 'Semua QR Code sedang diproses'
        ]);
    }
}

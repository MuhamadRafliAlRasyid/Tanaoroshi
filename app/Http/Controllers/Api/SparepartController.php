<?php

namespace App\Http\Controllers\Api;

use App\Models\Spareparts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class SparepartController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Spareparts::query();

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where('nama_part', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%")
                      ->orWhere('merk', 'like', "%{$search}%")
                      ->orWhere('ruk_no', 'like', "%{$search}%");
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
        } catch (\Exception $e) {
            Log::error('Sparepart Index Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal mengambil data sparepart'], 500);
        }
    }

    public function show(Spareparts $sparepart)
    {
        return response()->json([
            'status' => true,
            'data' => $sparepart
        ]);
    }

    public function store(Request $request)
    {
        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Sparepart Store Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal menyimpan sparepart'], 500);
        }
    }

    public function update(Request $request, Spareparts $sparepart)
    {
        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Sparepart Update Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal memperbarui sparepart'], 500);
        }
    }

    public function destroy(Spareparts $sparepart)
    {
        try {
            $sparepart->delete();
            return response()->json([
                'status' => true,
                'message' => 'Sparepart berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Sparepart Destroy Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal menghapus sparepart'], 500);
        }
    }

    public function checkStock()
    {
        try {
            $critical = Spareparts::whereColumn('jumlah_baru', '<=', 'titik_pesanan')->count();

            return response()->json([
                'status' => true,
                'critical_count' => $critical,
            ]);
        } catch (\Exception $e) {
            Log::error('Check Stock Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal memeriksa stok'], 500);
        }
    }

    public function regenerateQrCode(Spareparts $sparepart)
    {
        try {
            // Tambahkan logic generate QR Anda di sini
            return response()->json([
                'status' => true,
                'message' => 'QR Code berhasil diregenerate'
            ]);
        } catch (\Exception $e) {
            Log::error('Regenerate QR Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal meregenerate QR Code'], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\PengambilanSparepart;
use App\Models\Spareparts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PengambilanSparepartController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = PengambilanSparepart::with(['user', 'bagian', 'sparepart']);

            if (Auth::user()->role !== 'admin') {
                $query->where('user_id', Auth::id());
            }

            $data = $query->latest()->paginate(15);

            return response()->json([
                'status' => true,
                'data' => $data->items(),
                'meta' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'total' => $data->total(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Pengambilan Index Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data pengambilan'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'spareparts_id' => 'required|exists:spareparts,id',
                'jumlah' => 'required|integer|min:1',
                'satuan' => 'required|string|max:50',
                'keperluan' => 'required|string|max:255',
                'waktu_pengambilan' => 'required|date',
                'part_type' => 'required|in:baru,bekas',
            ]);

            $sparepart = Spareparts::findOrFail($request->spareparts_id);

            if ($request->part_type === 'baru' && $sparepart->jumlah_baru < $request->jumlah) {
                return response()->json(['status' => false, 'message' => 'Stok baru tidak mencukupi'], 422);
            }
            if ($request->part_type === 'bekas' && $sparepart->jumlah_bekas < $request->jumlah) {
                return response()->json(['status' => false, 'message' => 'Stok bekas tidak mencukupi'], 422);
            }

            $pengambilan = PengambilanSparepart::create([
                'user_id' => Auth::id(),
                'bagian_id' => Auth::user()->bagian_id,
                'spareparts_id' => $request->spareparts_id,
                'jumlah' => $request->jumlah,
                'satuan' => $request->satuan,
                'keperluan' => $request->keperluan,
                'waktu_pengambilan' => $request->waktu_pengambilan,
                'part_type' => $request->part_type,
            ]);

            // Kurangi stok
            if ($request->part_type === 'baru') {
                $sparepart->decrement('jumlah_baru', $request->jumlah);
            } else {
                $sparepart->decrement('jumlah_bekas', $request->jumlah);
            }

            return response()->json([
                'status' => true,
                'message' => 'Pengambilan sparepart berhasil dicatat',
                'data' => $pengambilan->load('sparepart')
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Pengambilan Store Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal menyimpan pengambilan'], 500);
        }
    }

    public function show(PengambilanSparepart $pengambilan)
    {
        return response()->json([
            'status' => true,
            'data' => $pengambilan->load(['user', 'bagian', 'sparepart'])
        ]);
    }

    public function update(Request $request, PengambilanSparepart $pengambilan)
    {
        try {
            $request->validate([
                'jumlah' => 'sometimes|integer|min:1',
                'keperluan' => 'sometimes|string|max:255',
                'waktu_pengambilan' => 'sometimes|date',
            ]);

            $pengambilan->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Data pengambilan berhasil diperbarui',
                'data' => $pengambilan
            ]);
        } catch (\Exception $e) {
            Log::error('Pengambilan Update Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal memperbarui data'], 500);
        }
    }

    public function destroy(PengambilanSparepart $pengambilan)
    {
        try {
            $pengambilan->delete();
            return response()->json([
                'status' => true,
                'message' => 'Pengambilan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Pengambilan Destroy Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal menghapus data'], 500);
        }
    }
}

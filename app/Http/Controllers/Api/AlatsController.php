<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AlatsController extends Controller
{
    // ==================== INDEX ====================
    public function index(Request $request)
    {
        try {
            $query = Alat::query();

            if ($request->filled('search')) {
                $query->search($request->search);
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
            Log::error('Alat Index Error: '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data'
            ], 500);
        }
    }

    // ==================== SHOW ====================
    public function show($hashid)
    {
        try {
            $id = app(\App\Services\HashIdService::class)->decode($hashid);

            if (!$id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Hash ID tidak valid'
                ], 404);
            }

            $alat = Alat::find($id);

            if (!$alat) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $alat
            ]);

        } catch (\Exception $e) {
            Log::error('Alat Show Error: '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data'
            ], 500);
        }
    }

    // ==================== STORE ====================
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_alat' => 'required|string|max:255',
                'kode' => 'nullable|string|max:100',
                'merk' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'jumlah' => 'required|numeric|min:0',
                'satuan' => 'nullable|string|max:50',
                'lokasi' => 'nullable|string|max:255',
                'kondisi' => 'required|in:baik,rusak',
                'keterangan' => 'nullable|string',
            ]);

            $alat = Alat::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil ditambahkan',
                'data' => $alat
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Alat Store Error: '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data'
            ], 500);
        }
    }

    // ==================== UPDATE ====================
    public function update(Request $request, $hashid)
    {
        try {
            $id = app(\App\Services\HashIdService::class)->decode($hashid);

            if (!$id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Hash ID tidak valid'
                ], 404);
            }

            $alat = Alat::find($id);

            if (!$alat) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $validated = $request->validate([
                'nama_alat' => 'required|string|max:255',
                'kode' => 'nullable|string|max:100',
                'merk' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'jumlah' => 'required|numeric|min:0',
                'satuan' => 'nullable|string|max:50',
                'lokasi' => 'nullable|string|max:255',
                'kondisi' => 'required|in:baik,rusak',
                'keterangan' => 'nullable|string',
            ]);

            $alat->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diperbarui',
                'data' => $alat
            ]);

        } catch (\Exception $e) {
            Log::error('Alat Update Error: '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal update data'
            ], 500);
        }
    }

    // ==================== DELETE ====================
    public function destroy($hashid)
    {
        try {
            $id = app(\App\Services\HashIdService::class)->decode($hashid);

            $alat = Alat::find($id);

            if (!$alat) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $alat->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Alat Delete Error: '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data'
            ], 500);
        }
    }

    // ==================== TRASH ====================
    public function trashed()
    {
        $data = Alat::onlyTrashed()->paginate(15);

        return response()->json([
            'status' => true,
            'data' => $data->items()
        ]);
    }

    // ==================== RESTORE ====================
    public function restore($hashid)
    {
        $id = app(\App\Services\HashIdService::class)->decode($hashid);

        $alat = Alat::onlyTrashed()->find($id);

        if (!$alat) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        $alat->restore();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil direstore'
        ]);
    }

    // ==================== FORCE DELETE ====================
    public function forceDelete($hashid)
    {
        $id = app(\App\Services\HashIdService::class)->decode($hashid);

        $alat = Alat::onlyTrashed()->find($id);

        if (!$alat) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        $alat->forceDelete();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil dihapus permanen'
        ]);
    }
}

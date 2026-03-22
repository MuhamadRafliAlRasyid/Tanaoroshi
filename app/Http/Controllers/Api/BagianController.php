<?php

namespace App\Http\Controllers\Api;

use App\Models\Bagian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BagianController extends Controller
{
    public function index(Request $request)
    {
        try {
            $bagians = Bagian::latest()->paginate(15);

            return response()->json([
                'status' => true,
                'data' => $bagians->items(),
                'meta' => [
                    'current_page' => $bagians->currentPage(),
                    'last_page' => $bagians->lastPage(),
                    'total' => $bagians->total(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Bagian Index Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data bagian'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_bagian' => 'required|string|max:255|unique:bagian,nama_bagian',
                'deskripsi' => 'nullable|string',
            ]);

            $bagian = Bagian::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Bagian berhasil ditambahkan',
                'data' => $bagian
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Bagian Store Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal menyimpan data'], 500);
        }
    }

    public function show(Bagian $bagian)
    {
        return response()->json(['status' => true, 'data' => $bagian]);
    }

    public function update(Request $request, Bagian $bagian)
    {
        try {
            $request->validate([
                'nama_bagian' => 'required|string|max:255|unique:bagian,nama_bagian,' . $bagian->id,
                'deskripsi' => 'nullable|string',
            ]);

            $bagian->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Bagian berhasil diperbarui',
                'data' => $bagian
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Bagian Update Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal memperbarui data'], 500);
        }
    }

    public function destroy(Bagian $bagian)
    {
        try {
            $bagian->delete();
            return response()->json([
                'status' => true,
                'message' => 'Bagian berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Bagian Destroy Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal menghapus data'], 500);
        }
    }
}

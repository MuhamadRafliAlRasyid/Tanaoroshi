<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KategoriResource;
use App\Models\Kategori;
use App\Services\HashIdService;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    private function findByHash($hashid)
    {
        $id = app(HashIdService::class)->decode($hashid);
        if (!$id) {
            abort(404, 'Kategori tidak ditemukan');
        }
        return Kategori::findOrFail($id);
    }

    /**
     * Daftar kategori
     * GET /api/kategori
     */
    public function index(Request $request)
    {
        $query = Kategori::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        $perPage = $request->input('per_page', 10);
        $data = $query->latest()->paginate($perPage);

        return KategoriResource::collection($data);
    }

    /**
     * Simpan kategori
     * POST /api/kategori
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'        => 'required|string|max:255',
            'keterangan'  => 'nullable|string',
        ]);

        $kategori = Kategori::create($data);

        return (new KategoriResource($kategori))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Detail kategori
     * GET /api/kategori/{hashid}
     */
    public function show($hashid)
    {
        $kategori = $this->findByHash($hashid);
        return new KategoriResource($kategori);
    }

    /**
     * Update kategori
     * PUT/PATCH /api/kategori/{hashid}
     */
    public function update(Request $request, $hashid)
    {
        $kategori = $this->findByHash($hashid);

        $data = $request->validate([
            'nama'        => 'required|string|max:255',
            'keterangan'  => 'nullable|string',
        ]);

        $kategori->update($data);

        return new KategoriResource($kategori);
    }

    /**
     * Hapus kategori
     * DELETE /api/kategori/{hashid}
     */
    public function destroy($hashid)
    {
        $kategori = $this->findByHash($hashid);
        $kategori->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus']);
    }
}

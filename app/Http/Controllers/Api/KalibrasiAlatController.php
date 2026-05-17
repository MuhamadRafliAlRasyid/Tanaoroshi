<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KalibrasiAlatResource;
use App\Models\Alat;
use App\Models\KalibrasiAlat;
use App\Services\HashIdService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KalibrasiAlatController extends Controller
{
    protected function decode($hashid)
    {
        $id = app(HashIdService::class)->decode($hashid);
        if (!$id) {
            abort(404, 'Data tidak ditemukan');
        }
        return $id;
    }

    /**
     * Daftar kalibrasi (bisa filter alat_id)
     */
    public function index(Request $request)
    {
        $query = KalibrasiAlat::with('alat');

        if ($request->filled('search')) {
            $query->whereHas('alat', function ($q) use ($request) {
                $q->where('nama_alat', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('alat_id')) {
            $query->where('alat_id', $request->alat_id); // bisa integer atau decode hash jika perlu
        }


        $data = $query->get();

        return KalibrasiAlatResource::collection($data);
    }

    /**
     * Tambah kalibrasi untuk alat tertentu
     * POST /api/alat/{hashid}/kalibrasi
     */
    public function store(Request $request, $hashid)
    {
        $alat = Alat::findOrFail($this->decode($hashid));

        $validated = $request->validate([
            'tanggal_kalibrasi' => 'required|date|before_or_equal:today',
            'masa_berlaku_baru' => 'required|date',
            'no_sertifikat'     => 'nullable|string|max:255',
            'keterangan'        => 'nullable|string',
        ]);

        $tanggalKalibrasi = Carbon::parse($validated['tanggal_kalibrasi']);
        $masaBerlakuBaru   = Carbon::parse($validated['masa_berlaku_baru']);

        $lastKalibrasi = KalibrasiAlat::where('alat_id', $alat->id)
            ->latest('tanggal_kalibrasi')
            ->first();

        // Validasi bisnis
        if ($lastKalibrasi && $tanggalKalibrasi->lt($lastKalibrasi->tanggal_kalibrasi)) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => ['tanggal_kalibrasi' => ['Tanggal tidak boleh lebih lama dari sebelumnya']],
            ], 422);
        }

        if ($masaBerlakuBaru->lte($tanggalKalibrasi)) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => ['masa_berlaku_baru' => ['Masa berlaku harus setelah tanggal kalibrasi']],
            ], 422);
        }

        if ($lastKalibrasi && $masaBerlakuBaru->lte($lastKalibrasi->masa_berlaku_baru)) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => ['masa_berlaku_baru' => ['Harus lebih besar dari masa berlaku sebelumnya']],
            ], 422);
        }

        $kalibrasi = KalibrasiAlat::create([
            'alat_id' => $alat->id,
            ...$validated
        ]);

        $alat->update([
            'masa_berlaku'    => $validated['masa_berlaku_baru'],
            'last_notified_at'=> null
        ]);

        DB::table('notifications')
            ->where('data->alat_id', $alat->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return (new KalibrasiAlatResource($kalibrasi))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Detail kalibrasi
     * GET /api/kalibrasi/{hashid}
     */
    public function show($hashid)
    {
        $kalibrasi = KalibrasiAlat::with('alat')->findOrFail($this->decode($hashid));
        return new KalibrasiAlatResource($kalibrasi);
    }

    /**
     * Update kalibrasi
     * PUT/PATCH /api/kalibrasi/{hashid}
     */
    public function update(Request $request, $hashid)
    {
        $data = KalibrasiAlat::with('alat')->findOrFail($this->decode($hashid));
        $alat = $data->alat;

        $validated = $request->validate([
            'tanggal_kalibrasi' => 'required|date|before_or_equal:today',
            'masa_berlaku_baru' => 'required|date',
            'no_sertifikat'     => 'nullable|string|max:255',
            'keterangan'        => 'nullable|string',
        ]);

        $tanggalKalibrasi = Carbon::parse($validated['tanggal_kalibrasi']);
        $masaBerlakuBaru   = Carbon::parse($validated['masa_berlaku_baru']);

        $lastKalibrasi = KalibrasiAlat::where('alat_id', $alat->id)
            ->where('id', '!=', $data->id)
            ->latest('tanggal_kalibrasi')
            ->first();

        if ($lastKalibrasi && $tanggalKalibrasi->lt($lastKalibrasi->tanggal_kalibrasi)) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => ['tanggal_kalibrasi' => ['Tanggal tidak boleh lebih lama dari data lain']],
            ], 422);
        }

        if ($masaBerlakuBaru->lte($tanggalKalibrasi)) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => ['masa_berlaku_baru' => ['Masa berlaku harus setelah tanggal kalibrasi']],
            ], 422);
        }

        if ($lastKalibrasi && $masaBerlakuBaru->lte($lastKalibrasi->masa_berlaku_baru)) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => ['masa_berlaku_baru' => ['Harus lebih besar dari data sebelumnya']],
            ], 422);
        }

        $data->update($validated);

        $latest = KalibrasiAlat::where('alat_id', $alat->id)
            ->latest('tanggal_kalibrasi')
            ->first();

        if ($latest && $latest->id == $data->id) {
            $alat->update(['masa_berlaku' => $validated['masa_berlaku_baru']]);

            DB::table('notifications')
                ->where('data->alat_id', $alat->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return new KalibrasiAlatResource($data->fresh('alat'));
    }

    /**
     * Hapus kalibrasi
     * DELETE /api/kalibrasi/{hashid}
     */
    public function destroy($hashid)
    {
        $data = KalibrasiAlat::findOrFail($this->decode($hashid));
        $alat = $data->alat;

        $data->delete();

        $latest = KalibrasiAlat::where('alat_id', $alat->id)
            ->latest('tanggal_kalibrasi')
            ->first();

        if ($latest) {
            $alat->update(['masa_berlaku' => $latest->masa_berlaku_baru]);
        } else {
            $alat->update(['masa_berlaku' => null]);
        }

        return response()->json(['message' => 'Data kalibrasi berhasil dihapus']);
    }
}

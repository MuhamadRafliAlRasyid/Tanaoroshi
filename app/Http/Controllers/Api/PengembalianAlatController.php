<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PengembalianAlatResource;
use App\Models\Alat;
use App\Models\PengambilanAlat;
use App\Models\PengembalianAlat;
use App\Services\HashIdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class PengembalianAlatController extends Controller
{
    protected function resolveHashid($hashid)
    {
        $id = app(HashIdService::class)->decode($hashid);
        if (!$id) {
            abort(404, 'Data tidak ditemukan');
        }
        return $id;
    }

    /**
     * Daftar pengembalian
     * GET /api/pengembalian_alat
     */
    public function index(Request $request)
    {
        $query = PengembalianAlat::with(['pengambilan.alat', 'user']);

        if (Auth::user()->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }
        if ($request->filled('alat_id')) {
        $alatId = $this->resolveHashid($request->alat_id);
        $query->whereHas('pengambilan', function ($q) use ($alatId) {
            $q->where('alat_id', $alatId);
        });
    }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('pengambilan.alat', function ($q2) use ($request) {
                    $q2->where('nama_alat', 'like', '%' . $request->search . '%');
                })->orWhereHas('user', function ($q2) use ($request) {
                    $q2->where('name', 'like', '%' . $request->search . '%');
                });
            });
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_pengembalian', $request->tanggal);
        }

        $perPage = $request->input('per_page', 10);
        $data = $query->latest()->paginate($perPage);

        return PengembalianAlatResource::collection($data);
    }

    /**
     * Simpan pengembalian (berdasarkan pengambilan)
     * POST /api/pengembalian_alat/{pengambilanHashid}
     */
    public function store(Request $request, $pengambilanHashid)
    {
        $pengambilan = PengambilanAlat::findOrFail($this->resolveHashid($pengambilanHashid));

        $validated = $request->validate([
            'jumlah'        => 'required|integer|min:1',
            'keterangan'    => 'nullable|string',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'nama_peminjam' => 'nullable|string|max:255',
        ]);

        $totalKembali = $pengambilan->pengembalians()->sum('jumlah');
        $sisa = $pengambilan->jumlah - $totalKembali;

        if ($validated['jumlah'] > $sisa) {
            return response()->json(['message' => 'Jumlah melebihi sisa pinjaman'], 422);
        }

        // Upload foto
        if ($request->hasFile('foto')) {
            $validated['foto'] = $this->uploadFotoPengembalian($request->file('foto'));
        }

        $pengembalian = DB::transaction(function () use ($validated, $pengambilan) {
            $pengembalian = PengembalianAlat::create([
                'pengambilan_alat_id' => $pengambilan->id,
                'user_id'             => Auth::id(),
                'jumlah'              => $validated['jumlah'],
                'tanggal_pengembalian'=> now(),
                'keterangan'          => $validated['keterangan'] ?? null,
                'foto'                => $validated['foto'] ?? null,
            ]);

            $alat = Alat::findOrFail($pengambilan->alat_id);
            $alat->increment('jumlah', $validated['jumlah']);

            $totalBaru = $pengambilan->pengembalians()->sum('jumlah');
            if ($totalBaru >= $pengambilan->jumlah) {
                $pengambilan->update(['status' => 'kembali']);
            }

            return $pengembalian;
        });

        return (new PengembalianAlatResource($pengembalian->load(['pengambilan.alat', 'user'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Detail pengembalian
     * GET /api/pengembalian_alat/{hashid}
     */
    public function show($hashid)
    {
        $data = PengembalianAlat::with(['pengambilan.alat', 'user'])
            ->findOrFail($this->resolveHashid($hashid));

        if (Auth::user()->role !== 'admin' && Auth::id() !== $data->user_id) {
            abort(403, 'Akses ditolak');
        }

        return new PengembalianAlatResource($data);
    }

    /**
     * Update pengembalian
     * PUT/PATCH /api/pengembalian_alat/{hashid}
     */
    public function update(Request $request, $hashid)
    {
        $data = PengembalianAlat::findOrFail($this->resolveHashid($hashid));

        $validated = $request->validate([
            'jumlah'        => 'required|integer|min:1',
            'keterangan'    => 'nullable|string',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'nama_peminjam' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($data, $request, $validated) {
                $pengambilan = $data->pengambilan;
                $alat = Alat::findOrFail($pengambilan->alat_id);

                // Kembalikan stok lama
                $alat->decrement('jumlah', $data->jumlah);

                // Validasi sisa
                $totalKembali = $pengambilan->pengembalians()->sum('jumlah') - $data->jumlah;
                $sisa = $pengambilan->jumlah - $totalKembali;
                if ($validated['jumlah'] > $sisa) {
                    throw new \Exception('Jumlah melebihi sisa pinjaman');
                }

                // Stok baru
                $alat->increment('jumlah', $validated['jumlah']);

                // Update foto
                if ($request->hasFile('foto')) {
                    if ($data->foto) {
                        $this->deleteFotoPengembalian($data->foto);
                    }
                    $validated['foto'] = $this->uploadFotoPengembalian($request->file('foto'));
                }

                $data->update([
                    'jumlah'     => $validated['jumlah'],
                    'keterangan' => $validated['keterangan'],
                    'foto'       => $validated['foto'] ?? $data->foto,
                ]);

                // Update status pengambilan
                $totalBaru = $pengambilan->pengembalians()->sum('jumlah');
                $pengambilan->update([
                    'status' => $totalBaru >= $pengambilan->jumlah ? 'kembali' : 'dipinjam'
                ]);
            });

            return new PengembalianAlatResource($data->fresh(['pengambilan.alat', 'user']));

        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Hapus pengembalian
     * DELETE /api/pengembalian_alat/{hashid}
     */
    public function destroy($hashid)
    {
        $data = PengembalianAlat::findOrFail($this->resolveHashid($hashid));

        DB::transaction(function () use ($data) {
            $pengambilan = $data->pengambilan;
            $alat = Alat::findOrFail($pengambilan->alat_id);

            $alat->decrement('jumlah', $data->jumlah);

            if ($data->foto) {
                $this->deleteFotoPengembalian($data->foto);
            }

            $data->delete();

            $total = $pengambilan->pengembalians()->sum('jumlah');
            if ($total < $pengambilan->jumlah) {
                $pengambilan->update(['status' => 'dipinjam']);
            }
        });

        return response()->json(['message' => 'Data pengembalian berhasil dihapus']);
    }

    /* ================= FOTO HELPER ================= */


protected function uploadFotoPengembalian($file): string
{
    try {
        $sourcePath = $file->getRealPath();
        [$width, $height, $type] = getimagesize($sourcePath);

        switch ($type) {
            case IMAGETYPE_JPEG: $source = imagecreatefromjpeg($sourcePath); break;
            case IMAGETYPE_PNG:  $source = imagecreatefrompng($sourcePath); break;
            case IMAGETYPE_WEBP: $source = imagecreatefromwebp($sourcePath); break;
            default: throw new \Exception('Unsupported image type');
        }

        $ext = match ($type) {
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG  => 'png',
            IMAGETYPE_WEBP => 'webp',
        };
        $filename = date('YmdHis') . '_' . Str::random(10) . '.' . $ext;

        // Original
        $origMaxWidth = 1200;
        if ($width <= $origMaxWidth) {
            $original = $source;
        } else {
            $origWidth = $origMaxWidth;
            $origHeight = (int) ($height * ($origMaxWidth / $width));
            $original = imagecreatetruecolor($origWidth, $origHeight);
            imagecopyresampled($original, $source, 0, 0, 0, 0, $origWidth, $origHeight, $width, $height);
        }

        ob_start();
        match ($ext) {
            'webp' => imagewebp($original, null, 80),
            'jpg'  => imagejpeg($original, null, 85),
            'png'  => imagepng($original, null, 8),
        };
        $origData = ob_get_clean();
        if ($original !== $source) imagedestroy($original);
        Storage::disk('public')->put('pengembalian/' . $filename, $origData);

        // Thumbnail
        $thumb = imagecreatetruecolor(200, 200);
        if ($width > $height) {
            $srcSize = $height;
            $srcX = ($width - $height) / 2;
            $srcY = 0;
        } else {
            $srcSize = $width;
            $srcX = 0;
            $srcY = ($height - $width) / 2;
        }
        imagecopyresampled($thumb, $source, 0, 0, (int)$srcX, (int)$srcY, 200, 200, $srcSize, $srcSize);

        ob_start();
        match ($ext) {
            'webp' => imagewebp($thumb, null, 70),
            'jpg'  => imagejpeg($thumb, null, 75),
            'png'  => imagepng($thumb, null, 7),
        };
        $thumbData = ob_get_clean();
        imagedestroy($thumb);
        imagedestroy($source);

        Storage::disk('public')->put('pengembalian/thumb/' . $filename, $thumbData);

        return $filename;
    } catch (\Throwable $e) {
        Log::error('Resize foto pengembalian gagal, simpan mentah: ' . $e->getMessage());
        $filename = date('YmdHis') . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('pengembalian', $file, $filename);
        return $filename;
    }
}

protected function deleteFotoPengembalian($filename): void
{
    if ($filename) {
        Storage::disk('public')->delete('pengembalian/' . $filename);
        Storage::disk('public')->delete('pengembalian/thumb/' . $filename);
    }
}
}

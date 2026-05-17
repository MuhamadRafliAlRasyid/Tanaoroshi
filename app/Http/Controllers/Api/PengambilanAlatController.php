<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PengambilanAlatResource;
use App\Models\Alat;
use App\Models\PengambilanAlat;
use App\Services\HashIdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengambilanAlatController extends Controller
{
    protected function resolveHashid($hashid)
    {
        $id = app(HashIdService::class)->decode($hashid);
        if (! $id) {
            abort(404, 'Data tidak ditemukan');
        }

        return $id;
    }

    /**
     * Daftar pengambilan alat
     * GET /api/pengambilan_alat
     */
    public function index(Request $request)
    {
        $query = PengambilanAlat::with(['user', 'bagian', 'alat']);

        // Non-admin hanya lihat miliknya
        if (Auth::user()->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }
        if ($request->filled('alat_id')) {
            $alatId = $this->resolveHashid($request->alat_id);
            $query->where('alat_id', $alatId);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('waktu_pengambilan', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('bagian', fn ($q) => $q->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('alat', fn ($q) => $q->where('nama_alat', 'like', "%{$search}%"));
            });
        }

        $perPage = $request->input('per_page', 10);
        $data = $query->latest()->paginate($perPage);

        return PengambilanAlatResource::collection($data);
    }

    /**
     * Simpan pengambilan alat
     * POST /api/pengambilan_alat
     */
    public function store(Request $request)
    {
        Log::info('API STORE PENGAMBILAN ALAT', ['request' => $request->all()]);

        try {
            // Non-admin otomatis set user_id & bagian_id
            if (Auth::user()->role !== 'admin') {
                $request->merge([
                    'user_id' => Auth::id(),
                    'bagian_id' => Auth::user()->bagian_id ?? null,
                ]);
            }

            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'bagian_id' => 'required|exists:bagian,id',
                'alat_id' => 'required|string', // hashid
                'nama_peminjam' => 'nullable|string|max:255',
                'jumlah' => 'required|integer|min:1',
                'satuan' => 'required|string|max:20',
                'keperluan' => 'required|string|max:255',
                'waktu_pengambilan' => 'required|date',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            // Upload foto (jika ada)
            if ($request->hasFile('foto')) {
                $validated['foto'] = $this->uploadFotoPengambilan($request->file('foto'));
            }

            $pengambilan = DB::transaction(function () use ($validated) {
                $alatId = $this->resolveHashid($validated['alat_id']);
                $alat = Alat::findOrFail($alatId);

                if ($alat->jumlah < $validated['jumlah']) {
                    throw new \Exception('Stok alat tidak mencukupi');
                }

                $alat->decrement('jumlah', $validated['jumlah']);

                $validated['alat_id'] = $alatId;
                $validated['status'] = 'dipinjam';

                return PengambilanAlat::create($validated);
            });

            return (new PengambilanAlatResource($pengambilan->load(['user', 'bagian', 'alat'])))
                ->response()
                ->setStatusCode(201);

        } catch (\Throwable $e) {
            Log::error('API STORE PENGAMBILAN ERROR', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Detail pengambilan
     * GET /api/pengambilan_alat/{hashid}
     */
    public function show($hashid)
    {
        $data = PengambilanAlat::with(['user', 'bagian', 'alat'])
            ->findOrFail($this->resolveHashid($hashid));

        // Otorisasi
        if (Auth::user()->role !== 'admin' && Auth::id() !== $data->user_id) {
            abort(403, 'Akses ditolak');
        }

        return new PengambilanAlatResource($data);
    }

    /**
     * Update pengambilan
     * PUT/PATCH /api/pengambilan_alat/{hashid}
     */
    public function update(Request $request, $hashid)
    {
        $data = PengambilanAlat::findOrFail($this->resolveHashid($hashid));

        if (Auth::user()->role !== 'admin' && Auth::id() !== $data->user_id) {
            abort(403, 'Akses ditolak');
        }

        $validated = $request->validate([
            'bagian_id' => 'required|exists:bagian,id',
            'alat_id' => 'required|string', // hashid
            'nama_peminjam' => 'nullable|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:20',
            'keperluan' => 'required|string|max:255',
            'waktu_pengambilan' => 'required|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Upload foto baru
        if ($request->hasFile('foto')) {
            if ($data->foto) {
                $this->deleteFotoPengambilan($data->foto);
            }
            $validated['foto'] = $this->uploadFotoPengambilan($request->file('foto'));
        }

        $validated['user_id'] = $data->user_id;

        try {
            DB::transaction(function () use ($data, $validated) {
                $oldAlat = Alat::findOrFail($data->alat_id);
                $newAlatId = $this->resolveHashid($validated['alat_id']);
                $newAlat = Alat::findOrFail($newAlatId);

                // Kembalikan stok lama
                $oldAlat->increment('jumlah', $data->jumlah);

                if ($newAlat->jumlah < $validated['jumlah']) {
                    throw new \Exception('Stok alat tidak mencukupi');
                }

                $newAlat->decrement('jumlah', $validated['jumlah']);

                $validated['alat_id'] = $newAlatId;
                $data->update($validated);
            });

            return new PengambilanAlatResource($data->fresh(['user', 'bagian', 'alat']));

        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Hapus pengambilan
     * DELETE /api/pengambilan_alat/{hashid}
     */
    public function destroy($hashid)
    {
        $data = PengambilanAlat::findOrFail($this->resolveHashid($hashid));

        if (Auth::user()->role !== 'admin' && Auth::id() !== $data->user_id) {
            abort(403, 'Akses ditolak');
        }

        DB::transaction(function () use ($data) {
            $alat = Alat::find($data->alat_id);
            if ($alat) {
                $alat->increment('jumlah', $data->jumlah);
            }
            if ($data->foto) {
                $this->deleteFotoPengambilan($data->foto);
            }
            $data->delete();
        });

        return response()->json(['message' => 'Data pengambilan berhasil dihapus']);
    }

    /* ================= FOTO HELPER ================= */

    protected function uploadFotoPengambilan($file): string
    {
        try {
            $sourcePath = $file->getRealPath();
            [$width, $height, $type] = getimagesize($sourcePath);

            switch ($type) {
                case IMAGETYPE_JPEG: $source = imagecreatefromjpeg($sourcePath);
                    break;
                case IMAGETYPE_PNG:  $source = imagecreatefrompng($sourcePath);
                    break;
                case IMAGETYPE_WEBP: $source = imagecreatefromwebp($sourcePath);
                    break;
                default: throw new \Exception('Unsupported image type');
            }

            // Ekstensi sesuai format asli
            $ext = match ($type) {
                IMAGETYPE_JPEG => 'jpg',
                IMAGETYPE_PNG => 'png',
                IMAGETYPE_WEBP => 'webp',
            };
            $filename = date('YmdHis').'_'.Str::random(10).'.'.$ext;

            // Original (maks lebar 1200, jangan upscale)
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
                'jpg' => imagejpeg($original, null, 85),
                'png' => imagepng($original, null, 8),
            };
            $origData = ob_get_clean();
            if ($original !== $source) {
                imagedestroy($original);
            }
            Storage::disk('public')->put('pengambilan/'.$filename, $origData);

            // Thumbnail (cover 200x200, crop center)
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
            imagecopyresampled($thumb, $source, 0, 0, (int) $srcX, (int) $srcY, 200, 200, $srcSize, $srcSize);

            ob_start();
            match ($ext) {
                'webp' => imagewebp($thumb, null, 70),
                'jpg' => imagejpeg($thumb, null, 75),
                'png' => imagepng($thumb, null, 7),
            };
            $thumbData = ob_get_clean();
            imagedestroy($thumb);
            imagedestroy($source);

            Storage::disk('public')->put('pengambilan/thumb/'.$filename, $thumbData);

            return $filename;
        } catch (\Throwable $e) {
            Log::error('Resize foto pengambilan gagal, simpan mentah: '.$e->getMessage());
            $filename = date('YmdHis').'_'.Str::random(10).'.'.$file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('pengambilan', $file, $filename);

            return $filename;
        }
    }

    protected function deleteFotoPengambilan($filename): void
    {
        if ($filename) {
            Storage::disk('public')->delete('pengambilan/'.$filename);
            Storage::disk('public')->delete('pengambilan/thumb/'.$filename);  // sebelumnya salah pakai thumb_
        }
    }
}

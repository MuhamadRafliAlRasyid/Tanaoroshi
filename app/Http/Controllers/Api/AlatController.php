<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlatResource;
use App\Models\Alat;
use App\Models\User;
use App\Notifications\AlatExpiredNotification;
use App\Services\HashIdService;
use Carbon\Carbon;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;


class AlatController extends Controller
{
    protected function resolveHashid(string $hashid): Alat
    {
        $id = app(HashIdService::class)->decode($hashid);
        if (!$id) {
            abort(404, 'Alat tidak ditemukan');
        }
        return Alat::findOrFail($id);
    }

    protected function resolveHashidWithTrashed(string $hashid): Alat
    {
        $id = app(HashIdService::class)->decode($hashid);
        if (!$id) {
            abort(404, 'Alat tidak ditemukan');
        }
        return Alat::withTrashed()->findOrFail($id);
    }
public function index(Request $request)
{
    $query = Alat::with('kategori');

    if ($request->filled('search')) {
        $query->search($request->search);
    }
    if ($request->filled('kategori_id')) {
        $query->where('kategori_id', $request->kategori_id);
    }

    // Ambil semua data tanpa paginasi
    $alats = $query->get();

    $this->checkExpired(); // tetap pantau expired

    return AlatResource::collection($alats);
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_alat'     => 'required|string|max:255',
            'kelas'         => 'nullable|string|max:100',
            'merk'          => 'required|string|max:255',
            'tipe'          => 'nullable|string|max:255',
            'no_seri'       => 'nullable|string|max:255',
            'no_identitas'  => 'nullable|string|max:255',
            'kapasitas'     => 'nullable|string|max:255',
            'daya_baca'     => 'nullable|string|max:255',
            'jumlah'        => 'required|integer|min:1',
            'no_sertifikat' => 'nullable|string|max:255',
            'kategori_id'   => 'nullable|exists:kategoris,id',
            'masa_berlaku'  => 'nullable|date',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $this->uploadAndResizeFoto($request->file('foto'));
        }

        $alat = Alat::create($data);
        $this->generateQrCode($alat);

        return (new AlatResource($alat))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $hashid)
    {
        $alat = $this->resolveHashid($hashid);
        $alat->load('kategori', 'kalibrasis');
        return new AlatResource($alat);
    }

    public function update(Request $request, string $hashid)
    {
        $alat = $this->resolveHashid($hashid);

        $data = $request->validate([
            'nama_alat'     => 'required|string|max:255',
            'kelas'         => 'nullable|string|max:100',
            'merk'          => 'required|string|max:255',
            'tipe'          => 'nullable|string|max:255',
            'no_seri'       => 'nullable|string|max:255',
            'no_identitas'  => 'nullable|string|max:255',
            'kapasitas'     => 'nullable|string|max:255',
            'daya_baca'     => 'nullable|string|max:255',
            'jumlah'        => 'required|integer|min:1',
            'no_sertifikat' => 'nullable|string|max:255',
            'kategori_id'   => 'nullable|exists:kategoris,id',
            'masa_berlaku'  => 'nullable|date',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $this->deleteFoto($alat);
            $data['foto'] = $this->uploadAndResizeFoto($request->file('foto'));
        }

        $alat->update($data);

        if ($alat->wasChanged('nama_alat')) {
            $this->generateQrCode($alat);
        }

        return new AlatResource($alat->fresh());
    }

    public function destroy(string $hashid)
    {
        $alat = $this->resolveHashid($hashid);
        $alat->delete();

        return response()->json([
            'message' => 'Alat dipindahkan ke tempat sampah.'
        ]);
    }

    public function trashed()
    {
        $data = Alat::onlyTrashed()->paginate(10);
        return AlatResource::collection($data);
    }

    public function restore(string $hashid)
    {
        $alat = $this->resolveHashidWithTrashed($hashid);
        $alat->restore();

        return response()->json([
            'message' => 'Alat berhasil dipulihkan.'
        ]);
    }

    public function forceDelete(string $hashid)
    {
        $alat = $this->resolveHashidWithTrashed($hashid);

        if ($alat->qr_code && Storage::disk('public')->exists($alat->qr_code)) {
            Storage::disk('public')->delete($alat->qr_code);
        }
        $this->deleteFoto($alat);
        $alat->forceDelete();

        return response()->json([
            'message' => 'Alat dihapus secara permanen.'
        ]);
    }

    // ================= QR CODE =================
    protected function generateQrCode(Alat $alat): void
    {
        Storage::makeDirectory('public/qrcodes', 0755, true);

        $qrCodePath = 'qrcodes/alat_' . $alat->hashid . '_' . Str::slug($alat->nama_alat) . '.png';
        $fullPath = storage_path('app/public/' . $qrCodePath);

        if ($alat->qr_code && Storage::disk('public')->exists($alat->qr_code)) {
            Storage::disk('public')->delete($alat->qr_code);
        }

        try {
            // QR mengarah ke deep link atau halaman detail alat di Flutter
            // Ganti dengan URL scheme aplikasi Anda, misalnya: myapp://alat/{hashid}
            $qrCode = new QrCode(
                data: 'myapp://alat/' . $alat->hashid, // deep link Flutter
                encoding: new Encoding('UTF-8'),
                size: 300,
                margin: 10,
                foregroundColor: new Color(0, 0, 0),
                backgroundColor: new Color(255, 255, 255)
            );

            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $result->saveToFile($fullPath);

            $alat->update(['qr_code' => $qrCodePath]);
        } catch (\Exception $e) {
            Log::error('QR Generation failed for alat ' . $alat->id . ': ' . $e->getMessage());
            throw $e;
        }
    }

    // ================= FOTO (Intervention v3) =================


protected function uploadAndResizeFoto($file): string
{
    try {
        $sourcePath = $file->getRealPath();
        [$width, $height, $type] = getimagesize($sourcePath);

        // Buat gambar dari sumber
        switch ($type) {
            case IMAGETYPE_JPEG: $source = imagecreatefromjpeg($sourcePath); break;
            case IMAGETYPE_PNG:  $source = imagecreatefrompng($sourcePath); break;
            case IMAGETYPE_WEBP: $source = imagecreatefromwebp($sourcePath); break;
            default: throw new \Exception('Unsupported image type');
        }

        // Tentukan ekstensi dan fungsi output
        $ext = match ($type) {
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG  => 'png',
            IMAGETYPE_WEBP => 'webp',
        };
        $filename = date('YmdHis') . '_' . Str::random(10) . '.' . $ext;

        // ---- Original (maks lebar 1200, jangan upscale) ----
        $origMaxWidth = 1200;
        if ($width <= $origMaxWidth) {
            // Tidak perlu resize, simpan asli
            $original = $source;
        } else {
            $origWidth = $origMaxWidth;
            $origHeight = (int) ($height * ($origMaxWidth / $width));
            $original = imagecreatetruecolor($origWidth, $origHeight);
            imagecopyresampled($original, $source, 0, 0, 0, 0, $origWidth, $origHeight, $width, $height);
        }

        // Simpan original
        ob_start();
        if ($ext === 'webp') {
            imagewebp($original, null, 80);
        } elseif ($ext === 'jpg') {
            imagejpeg($original, null, 85);
        } elseif ($ext === 'png') {
            imagepng($original, null, 8); // kompresi 8
        }
        $origData = ob_get_clean();
        if ($original !== $source) imagedestroy($original);
        Storage::disk('public')->put('alat/' . $filename, $origData);

        // ---- Thumbnail (cover 200x200, crop center, jangan upscale) ----
        $thumbWidth = $thumbHeight = 200;
        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);

        // Hitung crop area dari tengah
        if ($width > $height) {
            $srcSize = $height;
            $srcX = ($width - $height) / 2;
            $srcY = 0;
        } else {
            $srcSize = $width;
            $srcX = 0;
            $srcY = ($height - $width) / 2;
        }

        imagecopyresampled(
            $thumb, $source,
            0, 0, (int)$srcX, (int)$srcY,
            $thumbWidth, $thumbHeight,
            $srcSize, $srcSize
        );

        // Simpan thumbnail dengan format asli
        ob_start();
        if ($ext === 'webp') {
            imagewebp($thumb, null, 70);
        } elseif ($ext === 'jpg') {
            imagejpeg($thumb, null, 75);
        } elseif ($ext === 'png') {
            imagepng($thumb, null, 7);
        }
        $thumbData = ob_get_clean();
        imagedestroy($thumb);
        imagedestroy($source);

        Storage::disk('public')->put('alat/thumb/' . $filename, $thumbData);

        return $filename;
    } catch (\Throwable $e) {
        Log::error('Resize foto alat gagal (native fallback), simpan mentah: ' . $e->getMessage());
        // Fallback simpan file asli tanpa resize
        $filename = date('YmdHis') . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('alat', $file, $filename);
        return $filename;
    }
}
protected function deleteFoto(Alat $alat): void
{
    if ($alat->foto) {
        Storage::disk('public')->delete('alat/' . $alat->foto);
        Storage::disk('public')->delete('alat/thumb/' . $alat->foto);
    }
}


    // ================= NOTIFIKASI =================
    public function checkExpired()
    {
        $admins = User::whereIn('role', ['admin', 'super'])->get();
        if ($admins->isEmpty()) return;

        $today = now()->startOfDay();
        $warningLimit = now()->addDays(7)->endOfDay();

        $expired = Alat::whereDate('masa_berlaku', '<', $today)->get();
        $warning = Alat::whereBetween('masa_berlaku', [$today->toDateString(), $warningLimit->toDateString()])->get();

        $alatsToProcess = $expired->merge($warning);

        foreach ($alatsToProcess as $alat) {
            $status = $alat->masa_berlaku < $today ? 'expired' : 'warning';
            if (is_null($alat->last_notified_at) || Carbon::parse($alat->last_notified_at)->diffInDays(now()) >= 1) {
                Notification::send($admins, new AlatExpiredNotification($alat, $status));
                $alat->update(['last_notified_at' => now()]);
            }
        }

        $this->clearOldNotifications();
    }

    protected function clearOldNotifications(): void
    {
        $today = now()->startOfDay();
        $warningLimit = now()->addDays(7)->endOfDay();

        $alatsOk = Alat::whereDate('masa_berlaku', '>=', $today)
                       ->orWhere('masa_berlaku', '>', $warningLimit)
                       ->pluck('id');

        DB::table('notifications')
            ->where('data->type', 'alat_kalibrasi')
            ->whereNotIn('data->alat_id', $alatsOk)
            ->delete();
    }

    /**
     * Endpoint untuk sinkronisasi notifikasi expired/warning
     * Bisa dipanggil Flutter secara berkala
     */
    public function expiredAlerts()
    {
        $today = now()->startOfDay();
        $warningLimit = now()->addDays(7)->endOfDay();

        $expired = Alat::whereDate('masa_berlaku', '<', $today)->get();
        $warning = Alat::whereBetween('masa_berlaku', [$today->toDateString(), $warningLimit->toDateString()])->get();

        return response()->json([
            'expired' => AlatResource::collection($expired),
            'warning' => AlatResource::collection($warning),
        ]);
    }
}

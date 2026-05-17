<?php

namespace App\Http\Controllers;

use App\Exports\AlatExport;
use App\Models\Alat;
use App\Models\KalibrasiAlat;
use App\Models\PengambilanAlat;
use App\Models\PengembalianAlat;
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
use Maatwebsite\Excel\Facades\Excel;

class AlatsController extends Controller
{
    protected function resolveHashid(string $hashid): Alat
    {
        $id = app(HashIdService::class)->decode($hashid);
        abort_if(!$id, 404);
        return Alat::findOrFail($id);
    }

    protected function resolveHashidWithTrashed(string $hashid): Alat
    {
        $id = app(HashIdService::class)->decode($hashid);
        abort_if(!$id, 404);
        return Alat::withTrashed()->findOrFail($id);
    }

    public function index(Request $request)
    {
        $query = Alat::with('kategori');

        if ($request->search) {
            $query->search($request->search);
        }
        if ($request->kategori_id) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $alats = $query->paginate(12)->withQueryString();
        $kategoris = \App\Models\Kategori::all();
        $this->checkExpired(); // Panggil pengecekan expired setiap kali index diakses

        return view('alats.index', compact('alats', 'kategoris'));
    }

   public function daftarRiwayat(Request $request)
{
    // Kumpulkan semua ID alat yang punya riwayat
    $pengambilanIds = PengambilanAlat::select('alat_id')->distinct()->pluck('alat_id');
    $pengembalianIds = PengembalianAlat::with('pengambilan')
                        ->get()
                        ->pluck('pengambilan.alat_id')
                        ->filter()
                        ->unique();
    $kalibrasiIds = KalibrasiAlat::select('alat_id')->distinct()->pluck('alat_id');

    $alatIds = $pengambilanIds
                ->merge($pengembalianIds)
                ->merge($kalibrasiIds)
                ->unique();

    $query = Alat::with('kategori')->whereIn('id', $alatIds);

    // Pencarian berdasarkan nama, merk, tipe, dll.
    if ($request->filled('search')) {
        $query->search($request->search);
    }

    // Filter status
    if ($request->filled('status')) {
        if ($request->status === 'dipinjam') {
            $query->whereHas('pengambilan', function ($q) {
                $q->where('status', 'dipinjam');
            });
        } elseif ($request->status === 'dikembalikan') {
            $query->whereDoesntHave('pengambilan', function ($q) {
                $q->where('status', 'dipinjam');
            });
        } elseif ($request->status === 'dikalibrasi') {
            // Hanya alat yang memiliki riwayat kalibrasi
            $query->whereHas('kalibrasis');
        }
    }

    $alats = $query->orderBy('nama_alat')
                   ->paginate(12)
                   ->appends($request->only('search', 'status'));

    return view('alats.riwayat-index', compact('alats'));
}

    public function riwayat($hashid)
    {
    $alat = $this->resolveHashid($hashid);
    $alat->load('kategori');

    $pengambilan = PengambilanAlat::with(['user', 'bagian'])
                    ->where('alat_id', $alat->id)
                    ->latest()
                    ->get();

    $pengembalian = PengembalianAlat::with(['pengambilan', 'user'])
                    ->whereHas('pengambilan', function($q) use ($alat) {
                        $q->where('alat_id', $alat->id);   // <-- perbaikan di sini
                    })
                    ->latest()
                    ->get();

    $kalibrasis = KalibrasiAlat::where('alat_id', $alat->id)
                    ->latest()
                    ->get();

    return view('alats.riwayat', compact('alat', 'pengambilan', 'pengembalian', 'kalibrasis'));
    }
    public function create()
    {
        $kategoris = \App\Models\Kategori::all();
        return view('alats.create', compact('kategoris'));
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

        return redirect()->route('alats.index')
            ->with('success', 'Alat berhasil ditambahkan.');
    }

    public function show(string $hashid)
    {
        $alat = $this->resolveHashid($hashid);
        $alat->load('kategori', 'kalibrasis');
        return view('alats.show', compact('alat'));
    }

    public function edit(string $hashid)
    {
        $alat = $this->resolveHashid($hashid);
        $kategoris = \App\Models\Kategori::all();
        return view('alats.edit', compact('alat', 'kategoris'));
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

        return redirect()->route('alats.index')
            ->with('success', 'Alat berhasil diperbarui.');
    }

    public function destroy(string $hashid)
    {
        $alat = $this->resolveHashid($hashid);
        $alat->delete();
        return back()->with('success', 'Alat dipindahkan ke tempat sampah.');
    }

    public function trashed()
    {
        $data = Alat::onlyTrashed()->paginate(10);
        return view('alats.trashed', compact('data'));
    }

    public function restore(string $hashid)
    {
        $alat = $this->resolveHashidWithTrashed($hashid);
        $alat->restore();
        return back()->with('success', 'Alat berhasil dipulihkan.');
    }

    public function forceDelete(string $hashid)
    {
        $alat = $this->resolveHashidWithTrashed($hashid);

        if ($alat->qr_code && Storage::disk('public')->exists($alat->qr_code)) {
            Storage::disk('public')->delete($alat->qr_code);
        }

        $this->deleteFoto($alat);

        $alat->forceDelete();
        return back()->with('success', 'Alat dihapus secara permanen.');
    }

    // ================= QR CODE =================
    protected function generateQrCode(Alat $alat): void
{
    Storage::makeDirectory('public/qrcodes', 0755, true);

    $qrCodePath = 'qrcodes/alat_' . $alat->hashid . '_' . Str::slug($alat->nama_alat) . '.png';
    $fullPath = storage_path('app/public/' . $qrCodePath);

    // Hapus QR lama jika ada
    if ($alat->qr_code && Storage::disk('public')->exists($alat->qr_code)) {
        Storage::disk('public')->delete($alat->qr_code);
    }

    try {
        $qrCode = new QrCode(
            data: route('login') . '?alat_id=' . $alat->hashid, // <-- langsung ke login
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
        // Pastikan driver GD tersedia
        if (!class_exists(GdDriver::class)) {
            // Fallback: simpan file apa adanya tanpa manipulasi
            $filename = date('YmdHis') . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('alat', $file, $filename);
            // Thumbnail sama (tidak di-crop)
            Storage::disk('public')->putFileAs('alat/thumb', $file, $filename);
            return $filename;
        }

        $manager = new ImageManager(new GdDriver());
        $image = $manager->read($file);

        $filename = date('YmdHis') . '_' . Str::random(10) . '.webp';

        // Original (lebar maks 1200px)
        $originalPath = 'alat/' . $filename;
        Storage::disk('public')->put(
            $originalPath,
            $image->scaleDown(width: 1200)->toWebp(80)->toFilePointer()
        );

        // Thumbnail (200x200 crop)
        $thumbPath = 'alat/thumb_' . $filename;
        Storage::disk('public')->put(
            $thumbPath,
            $image->cover(200, 200)->toWebp(60)->toFilePointer()
        );

        return $filename;
    }


    protected function deleteFoto(Alat $alat): void
    {
        if ($alat->foto) {
            Storage::disk('public')->delete('alat/' . $alat->foto);
            Storage::disk('public')->delete('alat/thumb_' . $alat->foto);
        }
    }

    // ================= NOTIFIKASI =================
    public function checkExpired()
{
    $admins = User::whereIn('role', ['admin', 'super'])->get();
    if ($admins->isEmpty()) return;

    $today = now()->startOfDay();
    $warningLimit = now()->addDays(7)->endOfDay();

    // Ambil semua alat yang expired atau warning
    $expired = Alat::whereDate('masa_berlaku', '<', $today)->get();
    $warning = Alat::whereBetween('masa_berlaku', [$today->toDateString(), $warningLimit->toDateString()])->get();

    // Gabungkan untuk diproses
    $alatsToProcess = $expired->merge($warning);

    foreach ($alatsToProcess as $alat) {
        // Tentukan status
        $status = $alat->masa_berlaku < $today ? 'expired' : 'warning';

        // Hanya kirim notifikasi jika last_notified_at masih null
        // atau sudah lebih dari 1 hari (untuk menghindari spam harian)
        if (is_null($alat->last_notified_at) || Carbon::parse($alat->last_notified_at)->diffInDays(now()) >= 1) {
            Notification::send($admins, new AlatExpiredNotification($alat, $status));
            $alat->update(['last_notified_at' => now()]);
        }
    }

    // Hapus notifikasi untuk alat yang sudah tidak expired/warning
    $this->clearOldNotifications();
}

/**
 * Hapus notifikasi yang alatnya sudah diperbarui masa berlakunya
 */
protected function clearOldNotifications(): void
{
    // Ambil id alat yang masih expired/warning
    $today = now()->startOfDay();
    $warningLimit = now()->addDays(7)->endOfDay();

    $alatsOk = Alat::whereDate('masa_berlaku', '>=', $today)
                   ->orWhere('masa_berlaku', '>', $warningLimit)
                   ->pluck('id');

    // Hapus notifikasi yang alat_id-nya tidak termasuk yang expired/warning
    DB::table('notifications')
        ->where('data->type', 'alat_kalibrasi')
        ->whereNotIn('data->alat_id', $alatsOk)
        ->delete();
}
    public function exportExcel()
    {
        return Excel::download(new AlatExport, 'alat.xlsx');
    }
}

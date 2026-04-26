<?php

namespace App\Http\Controllers;

use App\Exports\AlatExport;
use App\Models\Alat;
use App\Models\User;
use App\Services\HashIdService;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class AlatsController extends Controller
{
    // ================= HASH =================
    protected function resolveHashid($hashid)
    {
        $id = app(HashIdService::class)->decode($hashid);
        abort_if(!$id, 404);

        return Alat::findOrFail($id);
    }

    protected function resolveHashidWithTrashed($hashid)
    {
        $id = app(HashIdService::class)->decode($hashid);
        abort_if(!$id, 404);

        return Alat::withTrashed()->findOrFail($id);
    }

    // ================= INDEX =================
    public function index(Request $request)
    {
        $query = Alat::with('kategori');

if ($request->search) {
    $query->where('nama_alat', 'like', '%' . $request->search . '%');
}

if ($request->kategori_id) {
    $query->where('kategori_id', $request->kategori_id);
}

$alats = $query->paginate(10)->withQueryString();

// 🔥 TAMBAHKAN INI
$kategoris = \App\Models\Kategori::all();

return view('alat.index', compact('alats', 'kategoris'));
    }

    // ================= CREATE =================
    public function create()
    {
        $kategoris = \App\Models\Kategori::all();
        return view('alat.create', compact('kategoris'));
    }

    // ================= STORE =================
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_alat'=>'required',
            'merk'=>'required',
            'tipe'=>'nullable',
            'no_seri'=>'nullable',
            'jumlah'=>'required|numeric',
            'kategori_id'=>'nullable',
            'masa_berlaku'=>'nullable|date',
        ]);

        $alat = Alat::create($data);

        $this->generateQrCode($alat, $alat->nama_alat);

        return redirect()->route('alat.index')
            ->with('success','Alat berhasil ditambahkan');
    }

    // ================= SHOW =================
    public function show($hashid)
    {
        $alat = $this->resolveHashid($hashid);
        return view('alat.show', compact('alat'));
    }

    // ================= EDIT =================
    public function edit($hashid)
    {
        $alat = $this->resolveHashid($hashid);
        $kategoris = \App\Models\Kategori::all();

        return view('alat.edit', compact('alat','kategoris'));
    }

    // ================= UPDATE =================
    public function update(Request $request, $hashid)
    {
        $alat = $this->resolveHashid($hashid);

        $data = $request->validate([
            'nama_alat'=>'required',
            'merk'=>'required',
            'tipe'=>'nullable',
            'no_seri'=>'nullable',
            'jumlah'=>'required|numeric',
            'kategori_id'=>'nullable',
            'masa_berlaku'=>'nullable|date',
        ]);

        $alat->update($data);

        $this->generateQrCode($alat, $alat->nama_alat);

        return redirect()->route('alat.index')
            ->with('success','Alat updated');
    }

    // ================= DELETE =================
    public function destroy($hashid)
    {
        $alat = $this->resolveHashid($hashid);
        $alat->delete();

        return back()->with('success','Alat dihapus');
    }

    // ================= TRASH =================
    public function trashed()
    {
        $data = Alat::onlyTrashed()->paginate(10);
        return view('alat.trashed', compact('data'));
    }

    public function restore($hashid)
    {
        $alat = $this->resolveHashidWithTrashed($hashid);
        $alat->restore();

        return back()->with('success','Alat direstore');
    }

    public function forceDelete($hashid)
    {
        $alat = $this->resolveHashidWithTrashed($hashid);

        if ($alat->qr_code && Storage::exists('public/'.$alat->qr_code)) {
            Storage::delete('public/'.$alat->qr_code);
        }

        $alat->forceDelete();

        return back()->with('success','Alat dihapus permanen');
    }

    // ================= QR =================
    protected function generateQrCode(Alat $alat, string $text)
    {
        $path = 'qrcodes/alat_'.$alat->hashid.'.png';
        $full = storage_path('app/public/'.$path);

        $qr = new QrCode(
            data: route('login').'?alat_id='.$alat->hashid,
            encoding: new Encoding('UTF-8'),
            size: 300,
            margin: 10,
            foregroundColor: new Color(0,0,0),
            backgroundColor: new Color(255,255,255)
        );

        (new PngWriter)->write($qr)->saveToFile($full);

        $alat->update(['qr_code'=>$path]);
    }

    // ================= NOTIF KALIBRASI =================
    public function checkExpired()
    {
        $admins = User::where('role', 'admin')->get();
        if ($admins->isEmpty()) return;

        $today = now();
        $warningLimit = now()->addDays(7);

        // 🔴 EXPIRED
        $expired = Alat::whereDate('masa_berlaku', '<', $today)
            ->whereNull('last_notified_at')
            ->get();

        foreach ($expired as $alat) {
            Notification::send(
                $admins,
                new \App\Notifications\AlatExpiredNotification($alat, 'expired')
            );

            $alat->update(['last_notified_at' => now()]);
        }

        // 🟡 WARNING
        $warning = Alat::whereBetween('masa_berlaku', [$today, $warningLimit])
            ->whereNull('last_notified_at')
            ->get();

        foreach ($warning as $alat) {
            Notification::send(
                $admins,
                new \App\Notifications\AlatExpiredNotification($alat, 'warning')
            );

            $alat->update(['last_notified_at' => now()]);
        }
    }

    // ================= EXPORT =================
    public function exportExcel()
    {
        return Excel::download(new AlatExport, 'alat.xlsx');
    }
}

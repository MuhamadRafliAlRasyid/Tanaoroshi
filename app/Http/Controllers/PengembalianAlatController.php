<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\PengambilanAlat;
use App\Models\PengembalianAlat;
use App\Services\HashIdService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class PengembalianAlatController extends Controller
{
    /* ================= HASH ================= */

    protected function resolveHashid($hashid)
    {
        $id = app(HashIdService::class)->decode($hashid);
        if (!$id) abort(404);
        return $id;
    }

    /* ================= INDEX ================= */

    public function index(Request $request)
    {
        $query = PengembalianAlat::with(['pengambilan.alat','user']);

        if (Auth::user()->role !== 'admin') {
        $query->where('user_id', Auth::id());
    }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('pengambilan.alat', function($q2) use ($request) {
                    $q2->where('nama_alat', 'like', '%' . $request->search . '%');
                })
                ->orWhereHas('user', function($q2) use ($request) {
                    $q2->where('name', 'like', '%' . $request->search . '%');
                });
            });
        }

        if ($request->tanggal) {
            $query->whereDate('tanggal_pengembalian', $request->tanggal);
        }

        $data = $query->latest()->paginate(10)->withQueryString();

        return view('pengembalian_alat.index', compact('data'));
    }

    /* ================= CREATE ================= */

    public function create($hashid)
    {
        $pengambilan = PengambilanAlat::with('alat')
            ->findOrFail($this->resolveHashid($hashid));

        return view('pengembalian_alat.create', compact('pengambilan'));
    }

    /* ================= STORE ================= */

    public function store(Request $request, $hashid)
    {
        $pengambilan = PengambilanAlat::findOrFail($this->resolveHashid($hashid));

        $validated = $request->validate([
            'jumlah'        => 'required|integer|min:1',
            'keterangan'    => 'nullable|string',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'nama_peminjam' => 'nullable|string|max:255',
        ]);

        $totalKembali = $pengambilan->pengembalians()->sum('jumlah');
        $sisa = $pengambilan->jumlah - $totalKembali;

        if ($validated['jumlah'] > $sisa) {
            return back()->with('error', 'Jumlah melebihi sisa pinjaman');
        }

        // Upload foto jika ada
        if ($request->hasFile('foto')) {
            $validated['foto'] = $this->uploadFotoTransaksi($request->file('foto'), 'pengembalian');
        }

        DB::transaction(function () use ($validated, $pengambilan) {
            PengembalianAlat::create([
                'pengambilan_alat_id' => $pengambilan->id,
                'user_id'             => Auth::id(),
                'jumlah'              => $validated['jumlah'],
                'tanggal_pengembalian'=> now(),
                'keterangan'          => $validated['keterangan'] ?? null,
                'foto'                => $validated['foto'] ?? null,
            ]);

            $alat = Alat::findOrFail($pengambilan->alat_id);
            $alat->increment('jumlah', $validated['jumlah']);

            $totalBaru = $pengambilan->pengembalians()->sum('jumlah') + $validated['jumlah'];
            if ($totalBaru >= $pengambilan->jumlah) {
                $pengambilan->update(['status' => 'kembali']);
            }
        });

        return redirect()->route('pengambilan_alat.show', $pengambilan->hashid)
            ->with('success', 'Pengembalian berhasil');
    }


    /* ================= SHOW ================= */

    public function show($hashid)
    {
         $data = PengembalianAlat::with(['pengambilan.alat','user'])
        ->findOrFail($this->resolveHashid($hashid));

    // 🔥 Karyawan hanya bisa melihat miliknya
    if (Auth::user()->role !== 'admin' && Auth::id() !== $data->user_id) {
        abort(403);
    }


        return view('pengembalian_alat.show', compact('data'));
    }

    /* ================= EDIT ================= */

    public function edit($hashid)
    {
        $data = PengembalianAlat::findOrFail(
            $this->resolveHashid($hashid)
        );

        return view('pengembalian_alat.edit', compact('data'));
    }

    /* ================= UPDATE ================= */

    public function update(Request $request, $hashid)
    {
        $data = PengembalianAlat::findOrFail($this->resolveHashid($hashid));

        $validated = $request->validate([
            'jumlah'        => 'required|integer|min:1',
            'keterangan'    => 'nullable|string',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'nama_peminjam' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($data, $request, $validated) {
            $pengambilan = $data->pengambilan;
            $alat = Alat::findOrFail($pengambilan->alat_id);

            // Balikin stok lama
            $alat->decrement('jumlah', $data->jumlah);

            // Cek sisa
            $totalKembali = $pengambilan->pengembalians()->sum('jumlah') - $data->jumlah;
            $sisa = $pengambilan->jumlah - $totalKembali;
            if ($validated['jumlah'] > $sisa) {
                throw new \Exception('Jumlah melebihi sisa');
            }

            // Tambah stok baru
            $alat->increment('jumlah', $validated['jumlah']);

            // Update foto
            if ($request->hasFile('foto')) {
                if ($data->foto) {
                    $this->deleteFotoTransaksi($data->foto, 'pengembalian');
                }
                $validated['foto'] = $this->uploadFotoTransaksi($request->file('foto'), 'pengembalian');
            }

            $data->update([
                'jumlah'     => $validated['jumlah'],
                'keterangan' => $validated['keterangan'],
                'foto'       => $validated['foto'] ?? $data->foto,
            ]);

            $totalBaru = $pengambilan->pengembalians()->sum('jumlah');
            if ($totalBaru < $pengambilan->jumlah) {
                $pengambilan->update(['status' => 'dipinjam']);
            } else {
                $pengambilan->update(['status' => 'kembali']);
            }
        });

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($hashid)
    {
        $data = PengembalianAlat::findOrFail($this->resolveHashid($hashid));

        DB::transaction(function () use ($data) {
            $pengambilan = $data->pengambilan;
            $alat = Alat::findOrFail($pengambilan->alat_id);

            $alat->decrement('jumlah', $data->jumlah);

            if ($data->foto) {
                $this->deleteFotoTransaksi($data->foto, 'pengembalian');
            }

            $data->delete();

            $total = $pengambilan->pengembalians()->sum('jumlah');
            if ($total < $pengambilan->jumlah) {
                $pengambilan->update(['status' => 'dipinjam']);
            }
        });

        return back()->with('success', 'Data dihapus');
    }

    /* ================= EXPORT PDF ================= */

    public function exportPdf($hashid = null)
    {
        if ($hashid) {
            $data = PengembalianAlat::with(['user', 'pengambilan.alat'])
                ->findOrFail($this->resolveHashid($hashid));
            $list = collect([$data]);
        } else {
            $list = PengembalianAlat::with(['user', 'pengambilan.alat'])->get();
        }

        $pdf = Pdf::loadView('pengembalian_alat.export-pdf', compact('list'));
        return $pdf->download('pengembalian_alat.pdf');
    }

    /* ================= FOTO HELPER ================= */

    protected function uploadFotoTransaksi($file, string $folder): string
    {
        // 1. Jika driver GD tidak tersedia, simpan file mentah
        if (!class_exists(GdDriver::class)) {
            $filename = date('YmdHis') . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs($folder, $file, $filename);
            Storage::disk('public')->putFileAs($folder . '/thumb', $file, $filename);
            return $filename;
        }

         $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
    $image = $manager->make($file);

    $filename = date('YmdHis') . '_' . Str::random(10) . '.webp';

    // Original
    Storage::disk('public')->put(
        "$folder/$filename",
        $image->resize(1200, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode('webp', 70)->stream()
    );

    // Thumbnail (di folder thumb)
    Storage::disk('public')->put(
        "$folder/thumb/$filename",   // <-- perhatikan: folder thumb, bukan prefix nama file
        $image->fit(200, 200)->encode('webp', 50)->stream()
    );

    return $filename;
    }

    protected function deleteFotoTransaksi($filename, string $folder): void
    {
        if ($filename) {
            Storage::disk('public')->delete("$folder/$filename");
            Storage::disk('public')->delete("$folder/thumb_$filename");
        }
    }
}

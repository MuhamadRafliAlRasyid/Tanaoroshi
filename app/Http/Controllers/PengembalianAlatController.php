<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\PengambilanAlat;
use App\Models\PengembalianAlat;
use App\Services\HashIdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    // 🔍 SEARCH
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

    // 📅 FILTER TANGGAL
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
        $pengambilan = PengambilanAlat::findOrFail(
            $this->resolveHashid($hashid)
        );

        $validated = $request->validate([
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        // 🔥 HITUNG SISA
        $totalKembali = $pengambilan->pengembalians()->sum('jumlah');
        $sisa = $pengambilan->jumlah - $totalKembali;

        if ($validated['jumlah'] > $sisa) {
            return back()->with('error', 'Jumlah melebihi sisa pinjaman');
        }

        DB::transaction(function () use ($validated, $pengambilan) {

            // 🔥 SIMPAN RIWAYAT
            PengembalianAlat::create([
                'pengambilan_alat_id' => $pengambilan->id,
                'user_id' => Auth::id(),
                'jumlah' => $validated['jumlah'],
                'tanggal_pengembalian' => now(),
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // 🔥 TAMBAH STOK
            $alat = Alat::findOrFail($pengambilan->alat_id);
            $alat->increment('jumlah', $validated['jumlah']);

            // 🔥 UPDATE STATUS
            $totalBaru = $pengambilan->pengembalians()->sum('jumlah') + $validated['jumlah'];

            if ($totalBaru >= $pengambilan->jumlah) {
                $pengambilan->update([
                    'status' => 'kembali'
                ]);
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
        $data = PengembalianAlat::findOrFail(
            $this->resolveHashid($hashid)
        );

        $validated = $request->validate([
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $validated) {

            $pengambilan = $data->pengambilan;
            $alat = Alat::findOrFail($pengambilan->alat_id);

            // 🔥 BALIKIN STOK LAMA
            $alat->decrement('jumlah', $data->jumlah);

            // 🔥 CEK SISA
            $totalKembali = $pengambilan->pengembalians()->sum('jumlah') - $data->jumlah;
            $sisa = $pengambilan->jumlah - $totalKembali;

            if ($validated['jumlah'] > $sisa) {
                throw new \Exception('Jumlah melebihi sisa');
            }

            // 🔥 TAMBAH STOK BARU
            $alat->increment('jumlah', $validated['jumlah']);

            $data->update([
                'jumlah' => $validated['jumlah'],
                'keterangan' => $validated['keterangan'],
            ]);

            // 🔥 UPDATE STATUS
            $totalBaru = $pengambilan->pengembalians()->sum('jumlah');

            if ($totalBaru < $pengambilan->jumlah) {
                $pengambilan->update(['status' => 'dipinjam']);
            } else {
                $pengambilan->update(['status' => 'kembali']);
            }
        });

        return back()->with('success', 'Data berhasil diupdate');
    }

    /* ================= DELETE ================= */

    public function destroy($hashid)
    {
        $data = PengembalianAlat::findOrFail(
            $this->resolveHashid($hashid)
        );

        DB::transaction(function () use ($data) {

            $pengambilan = $data->pengambilan;
            $alat = Alat::findOrFail($pengambilan->alat_id);

            // 🔥 KURANGI STOK (karena delete history)
            $alat->decrement('jumlah', $data->jumlah);

            $data->delete();

            // 🔥 UPDATE STATUS
            $total = $pengambilan->pengembalians()->sum('jumlah');

            if ($total < $pengambilan->jumlah) {
                $pengambilan->update(['status' => 'dipinjam']);
            }
        });

        return back()->with('success', 'Data dihapus');
    }
    public function exportPdf()
{
    $data = PengembalianAlat::with(['pengambilan.alat','user'])->get();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'pengembalian_alat.export-pdf',
        compact('data')
    );

    return $pdf->download('pengembalian_alat.pdf');
}
}

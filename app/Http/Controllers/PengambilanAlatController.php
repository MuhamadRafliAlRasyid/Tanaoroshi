<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Bagian;
use App\Models\PengambilanAlat;
use App\Models\User;
use App\Services\HashIdService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengambilanAlatController extends Controller
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
        $query = PengambilanAlat::with(['user', 'bagian', 'alat']);

        if (Auth::check() && Auth::user()->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }

        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('waktu_pengambilan', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('bagian', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('alat', fn($q) => $q->where('nama_alat', 'like', "%{$search}%"));
            });
        }

        $data = $query->latest()->paginate(10)->withQueryString();

        return view('pengambilan_alat.index', compact('data'));
    }

    /* ================= CREATE ================= */

    public function create()
    {
        $users = User::all();
        $bagians = Bagian::all();
        $alats = Alat::all();

        if (Auth::user()->role !== 'admin') {
            $users = User::where('id', Auth::id())->get();
            $bagians = Auth::user()->bagian
                ? Bagian::where('id', Auth::user()->bagian->id)->get()
                : collect();
        }

        return view('pengambilan_alat.create', compact('users','bagians','alats'));
    }

    /* ================= STORE ================= */

    public function store(Request $request)
{
    Log::info('START STORE PENGAMBILAN ALAT', [
        'request' => $request->all()
    ]);

    try {

        if (Auth::user()->role !== 'admin') {
            $request->merge([
                'user_id' => Auth::id(),
                'bagian_id' => Auth::user()->bagian_id ?? 1,
            ]);
        }

        $validated = $request->validate([

            'bagian_id' => 'required|exists:bagian,id',
            'alat_id' => 'required',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:20',
            'keperluan' => 'required|string|max:255',
            'waktu_pengambilan' => 'required|date',
        ]);
        $validated['user_id'] = Auth::id();

        Log::info('VALIDATED DATA', $validated);

        DB::transaction(function () use ($request, &$validated) {

            Log::info('MASUK TRANSACTION');

            $alatId = $this->resolveHashid($request->alat_id);
            Log::info('ALAT ID', ['alat_id' => $alatId]);

            $alat = Alat::findOrFail($alatId);
            Log::info('DATA ALAT', ['stok' => $alat->jumlah]);

            // 🔥 VALIDASI STOK
            if ($alat->jumlah < $validated['jumlah']) {
                Log::warning('STOK TIDAK CUKUP');
                throw new \Exception('Stok alat tidak mencukupi');
            }

            // 🔥 KURANGI STOK
            $alat->decrement('jumlah', $validated['jumlah']);
            Log::info('STOK DIKURANGI', [
                'jumlah_diambil' => $validated['jumlah'],
                'sisa_stok' => $alat->fresh()->jumlah
            ]);

            $validated['alat_id'] = $alatId;
            $validated['status'] = 'dipinjam';

            $data = PengambilanAlat::create($validated);

            Log::info('DATA BERHASIL DISIMPAN', [
                'id' => $data->id
            ]);
        });

        Log::info('STORE SUCCESS');

        return redirect()->route('pengambilan_alat.index')
            ->with('success', 'Pengambilan alat berhasil ditambahkan.');

    } catch (\Throwable $e) {

        Log::error('ERROR STORE PENGAMBILAN ALAT', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);

        return back()->with('error', 'Terjadi error: ' . $e->getMessage());
    }
}

    /* ================= SHOW ================= */

    public function show($hashid)
    {
        $data = PengambilanAlat::with(['user','bagian','alat'])
            ->findOrFail($this->resolveHashid($hashid));

        if (Auth::user()->role !== 'admin' && Auth::id() !== $data->user_id) {
            abort(403);
        }

        return view('pengambilan_alat.show', compact('data'));
    }

    /* ================= EXPORT PDF ================= */

    public function exportPdf($hashid = null)
    {
        if ($hashid) {
            $data = PengambilanAlat::with(['user','alat'])
                ->findOrFail($this->resolveHashid($hashid));

            $list = collect([$data]);
        } else {
            $list = PengambilanAlat::with(['user','alat'])->get();
        }

        $pdf = Pdf::loadView('pengambilan_alat.export-pdf', compact('list'));

        return $pdf->download('pengambilan_alat.pdf');
    }

    /* ================= EDIT ================= */

    public function edit($hashid)
    {
        $data = PengambilanAlat::findOrFail($this->resolveHashid($hashid));

        if (Auth::user()->role !== 'admin' && Auth::id() !== $data->user_id) {
            abort(403);
        }

        $users = User::all();
        $bagians = Bagian::all();
        $alats = Alat::all();

        return view('pengambilan_alat.edit', compact('data','users','bagians','alats'));
    }

    /* ================= UPDATE ================= */

    public function update(Request $request, $hashid)
{
    $data = PengambilanAlat::findOrFail($this->resolveHashid($hashid));

    if (Auth::user()->role !== 'admin' && Auth::id() !== $data->user_id) {
        abort(403);
    }

    // ❌ HAPUS user_id dari validasi
    $validated = $request->validate([
        'bagian_id' => 'required|exists:bagian,id',
        'alat_id' => 'required',
        'jumlah' => 'required|integer|min:1',
        'satuan' => 'required|string|max:20',
        'keperluan' => 'required',
        'waktu_pengambilan' => 'required|date',
    ]);

    // 🔥 SET USER DARI AUTH (AMAN)
    $validated['user_id'] = Auth::id();

    DB::transaction(function () use ($request, $data, &$validated) {

        $oldAlat = Alat::findOrFail($data->alat_id);
        $newAlatId = $this->resolveHashid($request->alat_id);
        $newAlat = Alat::findOrFail($newAlatId);

        // 🔥 BALIKIN STOK LAMA
        $oldAlat->increment('jumlah', $data->jumlah);

        // 🔥 CEK STOK BARU
        if ($newAlat->jumlah < $validated['jumlah']) {
            throw new \Exception('Stok alat tidak cukup');
        }

        // 🔥 KURANGI STOK BARU
        $newAlat->decrement('jumlah', $validated['jumlah']);

        $validated['alat_id'] = $newAlatId;

        $data->update($validated);
    });

    return redirect()->route('pengambilan_alat.show', $data->hashid)
        ->with('success', 'Data berhasil diperbarui');
}

    /* ================= DELETE ================= */

    public function destroy($hashid)
    {
        $data = PengambilanAlat::findOrFail($this->resolveHashid($hashid));

        if (Auth::user()->role !== 'admin' && Auth::id() !== $data->user_id) {
            abort(403);
        }

        DB::transaction(function () use ($data) {
            $alat = Alat::find($data->alat_id);

            if ($alat) {
                $alat->increment('jumlah', $data->jumlah);
            }

            $data->delete();
        });

        return redirect()->route('pengambilan_alat.index')
            ->with('success','Data berhasil dihapus');
    }
}

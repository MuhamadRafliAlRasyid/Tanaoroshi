<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bagian;
use App\Models\Spareparts;
use Illuminate\Http\Request;
use App\Services\HashIdService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use App\Models\PengambilanSparepart;
use Illuminate\Support\Facades\Auth;

class PengambilanSparepartController extends Controller
{
    protected function resolveHashid($hashid)
    {
        $id = app(HashIdService::class)->decode($hashid);
        if (!$id) abort(404);
        return PengambilanSparepart::findOrFail($id);
    }

    public function index(Request $request)
    {
        $query = PengambilanSparepart::with(['user', 'bagian', 'sparepart']);

        if (Auth::check()) {
            if (Auth::user()->role !== 'admin') {
                $query->where('user_id', Auth::user()->id);
            }
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('waktu_pengambilan', 'like', "%{$search}%")
                    ->orWhere('jumlah', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('bagian', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    })
                    ->orWhereHas('sparepart', function ($q) use ($search) {
                        $q->where('nama_part', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%");
                    });
            });
        }

        $pengambilanSpareparts = $query->paginate(10)->withQueryString();
        Log::info('Index Pengambilan Spareparts: ', $pengambilanSpareparts->toArray());

        return view('pengambilan.index', compact('pengambilanSpareparts'));
    }

    public function create()
{
    $users = User::all();
    $bagians = Bagian::all();
    $spareparts = Spareparts::all();

    if (Auth::user()->role !== 'admin') {
        $users = User::where('id', Auth::user()->id)->get();
        $bagians = Auth::user()->bagian ? Bagian::where('id', Auth::user()->bagian->id)->get() : collect();
    }

    $qrSparepartHashid = request()->query('spareparts_id');

    if ($qrSparepartHashid) {
        $sparepart = Spareparts::where('id', $qrSparepartHashid)->first();
        $spareparts = $sparepart ? collect([$sparepart]) : Spareparts::all();
    } else {
        $qrSparepartHashid = null;
    }

    return view('pengambilan.create', compact('users', 'bagians', 'spareparts', 'qrSparepartHashid'));
}

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            $request->merge([
                'user_id' => Auth::user()->id,
                'bagian_id' => Auth::user()->bagian_id ?? 1,
            ]);
        }

        Log::info('Store Request: ', $request->all());

        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'bagian_id' => 'required|exists:bagian,id',
                'spareparts_id' => 'required',
                'jumlah' => 'required|integer|min:1',
                'satuan' => 'required|string|max:50',
                'keperluan' => 'required|string|max:255',
                'waktu_pengambilan' => 'required|date',
            ]);

            $sparepartId = decode_id($request->spareparts_id, Spareparts::class);
            if (!$sparepartId) abort(404);

            $sparepart = Spareparts::findOrFail($sparepartId);
            $jumlah = $request->jumlah;

            Log::info('Current Stock - Baru: ' . $sparepart->jumlah_baru . ', Bekas: ' . $sparepart->jumlah_bekas);

            if ($request->part_type === 'baru') {
                $currentStock = $sparepart->jumlah_baru;
                if ($currentStock < $jumlah) {
                    Log::warning('Stock insuficient for sparepart ID ' . $sparepart->id . ': Required ' . $jumlah . ', Available ' . $currentStock);
                    return redirect()->back()->with('error', 'Stok baru tidak mencukupi. Stok tersedia: ' . $currentStock);
                }
                $sparepart->update(['jumlah_baru' => $currentStock - $jumlah]);
            } elseif ($request->part_type === 'bekas') {
                $currentStock = $sparepart->jumlah_bekas;
                if ($currentStock < $jumlah) {
                    Log::warning('Stock insuficient for sparepart ID ' . $sparepart->id . ': Required ' . $jumlah . ', Available ' . $currentStock);
                    return redirect()->back()->with('error', 'Stok bekas tidak mencukupi. Stok tersedia: ' . $currentStock);
                }
                $sparepart->update(['jumlah_bekas' => $currentStock - $jumlah]);
            }

            $validated['spareparts_id'] = $sparepartId;
            $pengambilanSparepart = PengambilanSparepart::create($validated);
            Log::info('Stored Pengambilan Sparepart: ', $pengambilanSparepart->toArray());

            return redirect()->route('pengambilan.show', $pengambilanSparepart->hashid)->with('success', 'Pengambilan sparepart berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error di store PengambilanSparepart: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function show($hashid)
    {
        $pengambilanSparepart = $this->resolveHashid($hashid);
        $pengambilanSparepart->load(['user', 'bagian', 'sparepart']);

        if (Auth::user()->role !== 'admin' && Auth::user()->id !== $pengambilanSparepart->user_id) {
            abort(403, 'Anda tidak memiliki izin untuk melihat detail ini.');
        }
        return view('pengambilan.show', compact('pengambilanSparepart'));
    }

    public function exportPdf($hashid = null)
    {
        if ($hashid) {
            $pengambilanSparepart = $this->resolveHashid($hashid);
            $pengambilanSparepart->load(['user', 'sparepart']);
            $pengambilanSpareparts = collect([$pengambilanSparepart]);
        } else {
            $pengambilanSpareparts = PengambilanSparepart::with(['user', 'sparepart'])->get();
        }

        $pdf = Pdf::loadView('pengambilan.export-pdf', compact('pengambilanSpareparts'));
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);

        return $pdf->download('pengambilan_report_' . ($hashid ? 'hashid_' . $hashid : 'all') . '.pdf');
    }

    public function edit($hashid)
{
    $pengambilanSparepart = PengambilanSparepart::with(['user', 'bagian', 'sparepart'])
        ->findByHashidOrFail($hashid);

    if (Auth::user()->role !== 'admin' && Auth::user()->id !== $pengambilanSparepart->user_id) {
        abort(403);
    }

    $users = User::all();
    $bagians = Bagian::all();
    $spareparts = Spareparts::all();

    if (Auth::user()->role !== 'admin') {
        $users = User::where('id', Auth::user()->id)->get();
        $bagians = Auth::user()->bagian ? Bagian::where('id', Auth::user()->bagian->id)->get() : collect();
    }

    return view('pengambilan.edit', compact(
        'pengambilanSparepart', 'users', 'bagians', 'spareparts'
    ));
}

    public function update(Request $request, $hashid)
{
    $pengambilanSparepart = PengambilanSparepart::findByHashidOrFail($hashid);

    if (Auth::user()->role !== 'admin' && Auth::user()->id !== $pengambilanSparepart->user_id) {
        abort(403);
    }

    if (Auth::user()->role !== 'admin') {
        $request->merge([
            'user_id' => $pengambilanSparepart->user_id,
            'bagian_id' => $pengambilanSparepart->bagian_id,
        ]);
    }

    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'bagian_id' => 'required|exists:bagian,id',
        'spareparts_id' => 'required',
        'part_type' => 'required|in:baru,bekas',
        'jumlah' => 'required|integer|min:1',
        'satuan' => 'required|string|max:50',
        'keperluan' => 'required|string|max:255',
        'waktu_pengambilan' => 'required|date',
    ]);

    $sparepartId = decode_id($request->spareparts_id, Spareparts::class);
    if (!$sparepartId) abort(404);

    $sparepart = Spareparts::findOrFail($sparepartId);
    $newJumlah = $request->jumlah;
    $oldJumlah = $pengambilanSparepart->jumlah;
    $partType = $request->part_type;

    // Kembalikan stok lama
    if ($pengambilanSparepart->part_type === 'baru') {
        $sparepart->increment('jumlah_baru', $oldJumlah);
    } elseif ($pengambilanSparepart->part_type === 'bekas') {
        $sparepart->increment('jumlah_bekas', $oldJumlah);
    }

    // Kurangi stok baru
    if ($partType === 'baru') {
        if ($sparepart->jumlah_baru < $newJumlah) {
            return back()->with('error', 'Stok baru tidak cukup.');
        }
        $sparepart->decrement('jumlah_baru', $newJumlah);
    } elseif ($partType === 'bekas') {
        if ($sparepart->jumlah_bekas < $newJumlah) {
            return back()->with('error', 'Stok bekas tidak cukup.');
        }
        $sparepart->decrement('jumlah_bekas', $newJumlah);
    }

    $validated['spareparts_id'] = $sparepartId;
    $pengambilanSparepart->update($validated);

    return redirect()
        ->route('pengambilan.show', $pengambilanSparepart->hashid)
        ->with('success', 'Berhasil diperbarui.');
}

    public function destroy($hashid)
    {
        $pengambilanSparepart = PengambilanSparepart::findByHashidOrFail($hashid);

        if (!$pengambilanSparepart || !$pengambilanSparepart->exists) {
            Log::error('Destroy: Pengambilan Sparepart tidak ditemukan untuk HashID: ' . $hashid);
            abort(404, 'Pengambilan Sparepart tidak ditemukan.');
        }
        if (Auth::user()->role !== 'admin' && Auth::user()->id !== $pengambilanSparepart->user_id) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus data ini.');
        }

        $sparepart = $pengambilanSparepart->sparepart;
        $jumlah = $pengambilanSparepart->jumlah;
        if ($pengambilanSparepart->part_type === 'baru') {
            $currentStock = $sparepart->jumlah_baru + $jumlah;
            $sparepart->update(['jumlah_baru' => $currentStock]);
        } elseif ($pengambilanSparepart->part_type === 'bekas') {
            $currentStock = $sparepart->jumlah_bekas + $jumlah;
            $sparepart->update(['jumlah_bekas' => $currentStock]);
        }

        $pengambilanSparepart->delete();
        Log::info('Destroyed Pengambilan Sparepart HashID: ', ['hashid' => $hashid]);

        return redirect()->route('pengambilan.index')->with('success', 'Pengambilan sparepart berhasil dihapus.');
    }
}

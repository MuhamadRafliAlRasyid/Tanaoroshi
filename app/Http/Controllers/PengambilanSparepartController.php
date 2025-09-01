<?php

namespace App\Http\Controllers;

use App\Models\PengambilanSparepart;
use App\Models\Bagian;
use App\Models\Spareparts;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SparepartController;

class PengambilanSparepartController extends Controller
{
    protected $sparepartController;

    public function __construct(SparepartController $sparepartController)
    {
        $this->sparepartController = $sparepartController;
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

        $qrSparepartId = request()->query('spareparts_id', $spareparts->first()->id ?? null);
        if ($qrSparepartId) {
            $spareparts = Spareparts::where('id', $qrSparepartId)->get();
        }

        return view('pengambilan.create', compact('users', 'bagians', 'spareparts'));
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
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bagian_id' => 'required|exists:bagian,id',
            'spareparts_id' => 'required|exists:spareparts,id',
            'part_type' => 'required|in:baru,bekas',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'keperluan' => 'required|string|max:255',
            'waktu_pengambilan' => 'required|date',
        ]);

        $sparepart = Spareparts::findOrFail($request->spareparts_id);
        $jumlah = $request->jumlah;

        if ($request->part_type === 'baru') {
            $currentStock = $sparepart->jumlah_baru;
            if ($currentStock < $jumlah) {
                return redirect()->back()->with('error', 'Stok baru tidak mencukupi. Stok tersedia: ' . $currentStock);
            }
            $sparepart->update(['jumlah_baru' => $currentStock - $jumlah]);
        } elseif ($request->part_type === 'bekas') {
            $currentStock = $sparepart->jumlah_bekas;
            if ($currentStock < $jumlah) {
                return redirect()->back()->with('error', 'Stok bekas tidak mencukupi. Stok tersedia: ' . $currentStock);
            }
            $sparepart->update(['jumlah_bekas' => $currentStock - $jumlah]);
        }

        $pengambilanSparepart = PengambilanSparepart::create(array_merge($request->all(), ['part_type' => $request->part_type]));
        Log::info('Stored Pengambilan Sparepart: ', $pengambilanSparepart->toArray());

        // Sinkronisasi hanya untuk sparepart yang diambil
        $this->sparepartController->syncPartialToSheets($request->spareparts_id);

        return redirect()->route('pengambilan.index')->with('success', 'Pengambilan sparepart berhasil ditambahkan.');
    }

    public function show($id)
    {
        $pengambilanSparepart = PengambilanSparepart::with(['user', 'bagian', 'sparepart'])->findOrFail($id);
        if (Auth::user()->role !== 'admin' && Auth::user()->id !== $pengambilanSparepart->user_id) {
            abort(403, 'Anda tidak memiliki izin untuk melihat detail ini.');
        }
        return view('pengambilan.show', compact('pengambilanSparepart'));
    }

    public function edit(PengambilanSparepart $pengambilanSparepart)
    {
        if (!$pengambilanSparepart || !$pengambilanSparepart->exists) {
            Log::error('Edit: Pengambilan Sparepart tidak ditemukan untuk ID: ' . request()->route('id') ?? 'null');
            abort(404, 'Pengambilan Sparepart tidak ditemukan.');
        }
        if (Auth::user()->role !== 'admin' && Auth::user()->id !== $pengambilanSparepart->user_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit data ini.');
        }
        Log::info('Edit Pengambilan Sparepart: ', $pengambilanSparepart->toArray());
        $users = User::all();
        $bagians = Bagian::all();
        $spareparts = Spareparts::all();

        if (Auth::user()->role !== 'admin') {
            $users = User::where('id', Auth::user()->id)->get();
            $bagians = Auth::user()->bagian ? Bagian::where('id', Auth::user()->bagian->id)->get() : collect();
        }

        return view('pengambilan.edit', compact('pengambilanSparepart', 'users', 'bagians', 'spareparts'));
    }

    public function update(Request $request, PengambilanSparepart $pengambilanSparepart)
    {
        if (!$pengambilanSparepart || !$pengambilanSparepart->exists) {
            Log::error('Update: Pengambilan Sparepart tidak ditemukan untuk ID: ' . request()->route('id') ?? 'null');
            abort(404, 'Pengambilan Sparepart tidak ditemukan.');
        }
        if (Auth::user()->role !== 'admin' && Auth::user()->id !== $pengambilanSparepart->user_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit data ini.');
        }
        Log::info('Update Request: ', $request->all());
        Log::info('Update Pengambilan Sparepart Before: ', $pengambilanSparepart->toArray());

        if (Auth::user()->role !== 'admin') {
            $request->merge([
                'user_id' => $pengambilanSparepart->user_id,
                'bagian_id' => $pengambilanSparepart->bagian_id,
            ]);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bagian_id' => 'required|exists:bagian,id',
            'spareparts_id' => 'required|exists:spareparts,id',
            'part_type' => 'required|in:baru,bekas',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'keperluan' => 'required|string|max:255',
            'waktu_pengambilan' => 'required|date',
        ]);

        $sparepart = Spareparts::findOrFail($request->spareparts_id);
        $newJumlah = $request->jumlah;
        $oldJumlah = $pengambilanSparepart->jumlah;
        $partType = $request->part_type;

        // Kembalikan stok lama
        if ($pengambilanSparepart->part_type === 'baru') {
            $currentStock = $sparepart->jumlah_baru + $oldJumlah;
            $sparepart->update(['jumlah_baru' => $currentStock]);
        } elseif ($pengambilanSparepart->part_type === 'bekas') {
            $currentStock = $sparepart->jumlah_bekas + $oldJumlah;
            $sparepart->update(['jumlah_bekas' => $currentStock]);
        }

        // Kurangi stok baru berdasarkan part_type yang baru
        if ($partType === 'baru') {
            $currentStock = $sparepart->jumlah_baru;
            if ($currentStock < $newJumlah) {
                return redirect()->back()->with('error', 'Stok baru tidak mencukupi. Stok tersedia: ' . $currentStock);
            }
            $sparepart->update(['jumlah_baru' => $currentStock - $newJumlah]);
        } elseif ($partType === 'bekas') {
            $currentStock = $sparepart->jumlah_bekas;
            if ($currentStock < $newJumlah) {
                return redirect()->back()->with('error', 'Stok bekas tidak mencukupi. Stok tersedia: ' . $currentStock);
            }
            $sparepart->update(['jumlah_bekas' => $currentStock - $newJumlah]);
        }

        $pengambilanSparepart->update(array_merge($request->all(), ['part_type' => $partType]));
        Log::info('Update Pengambilan Sparepart After: ', $pengambilanSparepart->fresh()->toArray());

        // Sinkronisasi hanya untuk sparepart yang diubah
        $this->sparepartController->syncPartialToSheets($request->spareparts_id);

        return redirect()->route('pengambilan.index')->with('success', 'Pengambilan sparepart berhasil diperbarui.');
    }

    public function destroy(PengambilanSparepart $pengambilanSparepart)
    {
        if (!$pengambilanSparepart || !$pengambilanSparepart->exists) {
            Log::error('Destroy: Pengambilan Sparepart tidak ditemukan untuk ID: ' . request()->route('id') ?? 'null');
            abort(404, 'Pengambilan Sparepart tidak ditemukan.');
        }
        if (Auth::user()->role !== 'admin' && Auth::user()->id !== $pengambilanSparepart->user_id) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus data ini.');
        }

        // Kembalikan stok saat menghapus
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
        Log::info('Destroyed Pengambilan Sparepart ID: ', ['id' => $pengambilanSparepart->id]);

        // Sinkronisasi hanya untuk sparepart yang dihapus
        $this->sparepartController->syncPartialToSheets($pengambilanSparepart->spareparts_id);

        return redirect()->route('pengambilan.index')->with('success', 'Pengambilan sparepart berhasil dihapus.');
    }
}

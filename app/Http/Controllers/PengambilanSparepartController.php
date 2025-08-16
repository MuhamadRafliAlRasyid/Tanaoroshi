<?php

namespace App\Http\Controllers;

use App\Models\PengambilanSparepart;
use App\Models\Bagian;
use App\Models\Spareparts;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PengambilanSparepartController extends Controller
{
    public function index(Request $request)
    {
        $query = PengambilanSparepart::with(['user', 'bagian', 'sparepart']);

        // Filter berdasarkan pencarian
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
        return view('pengambilan.create', compact('users', 'bagians', 'spareparts'));
    }

    public function store(Request $request)
    {
        Log::info('Store Request: ', $request->all());
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bagian_id' => 'required|exists:bagian,id',
            'spareparts_id' => 'required|exists:spareparts,id',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'keperluan' => 'required|string|max:255',
            'waktu_pengambilan' => 'required|date',
        ]);

        $pengambilanSparepart = PengambilanSparepart::create($request->all());
        Log::info('Stored Pengambilan Sparepart: ', $pengambilanSparepart->toArray());
        return redirect()->route('pengambilan.index')->with('success', 'Pengambilan sparepart berhasil ditambahkan.');
    }

    public function show($id)
    {
        $pengambilanSparepart = PengambilanSparepart::findOrFail($id);
        return view('pengambilan.show', compact('pengambilanSparepart'));
    }

    public function edit(PengambilanSparepart $pengambilanSparepart)
    {
        if (!$pengambilanSparepart || !$pengambilanSparepart->exists) {
            Log::error('Edit: Pengambilan Sparepart tidak ditemukan untuk ID: ' . request()->route('id') ?? 'null');
            abort(404, 'Pengambilan Sparepart tidak ditemukan.');
        }
        Log::info('Edit Pengambilan Sparepart: ', $pengambilanSparepart->toArray());
        $users = User::all();
        $bagians = Bagian::all();
        $spareparts = Spareparts::all();
        return view('pengambilan.edit', compact('pengambilanSparepart', 'users', 'bagians', 'spareparts'));
    }

    public function update(Request $request, PengambilanSparepart $pengambilanSparepart)
    {
        if (!$pengambilanSparepart || !$pengambilanSparepart->exists) {
            Log::error('Update: Pengambilan Sparepart tidak ditemukan untuk ID: ' . request()->route('id') ?? 'null');
            abort(404, 'Pengambilan Sparepart tidak ditemukan.');
        }
        Log::info('Update Request: ', $request->all());
        Log::info('Update Pengambilan Sparepart Before: ', $pengambilanSparepart->toArray());

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bagian_id' => 'required|exists:bagian,id',
            'spareparts_id' => 'required|exists:spareparts,id',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'keperluan' => 'required|string|max:255',
            'waktu_pengambilan' => 'required|date',
        ]);

        $pengambilanSparepart->update($request->all());
        Log::info('Update Pengambilan Sparepart After: ', $pengambilanSparepart->fresh()->toArray());
        return redirect()->route('pengambilan.index')->with('success', 'Pengambilan sparepart berhasil diperbarui.');
    }

    public function destroy(PengambilanSparepart $pengambilanSparepart)
    {
        if (!$pengambilanSparepart || !$pengambilanSparepart->exists) {
            Log::error('Destroy: Pengambilan Sparepart tidak ditemukan untuk ID: ' . request()->route('id') ?? 'null');
            abort(404, 'Pengambilan Sparepart tidak ditemukan.');
        }
        $pengambilanSparepart->delete();
        Log::info('Destroyed Pengambilan Sparepart ID: ', ['id' => $pengambilanSparepart->id]);
        return redirect()->route('pengambilan.index')->with('success', 'Pengambilan sparepart berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Barang;
use App\Models\Permintaan;
use Illuminate\Http\Request;
use App\Models\DetailPermintaan;
use Illuminate\Support\Facades\Auth;

class PermintaanController extends Controller
{
    public function indexUser()
    {
        $permintaans = Permintaan::with('details.barang')
            ->where('user_id', Auth::id())
            ->get();

        return view('permintaan.index', compact('permintaans'));
    }
    public function laporan()
    {
        $permintaans = Permintaan::with('details.barang')
            ->where('user_id', Auth::id())
            ->paginate(10); // atau sesuaikan jumlahnya


        return view('permintaan.laporan', compact('permintaans'));
    }
    public function create()
    {
        $barangs = Barang::all();
        return view('permintaan.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id.*'   => 'required|exists:barang,id',
            'jumlah.*'      => 'required|integer|min:1',
            'satuan.*'      => 'required|string|max:50',
            'tanggal.*'     => 'required|date',
            'keterangan.*'  => 'nullable|string',
        ]);


        $permintaan = Permintaan::create([
            'user_id' => Auth::id(),
        ]);

        foreach ($request->barang_id as $index => $barangId) {
            DetailPermintaan::create([
                'permintaan_id' => $permintaan->id,
                'barang_id'     => $barangId,
                'jumlah'        => $request->jumlah[$index],
                'satuan'        => $request->satuan[$index],
                'tanggal'       => $request->tanggal[$index],
                'keterangan'    => $request->keterangan[$index] ?? null,
            ]);
        }


        return redirect()->route('permintaan.index')->with('success', 'Permintaan berhasil diajukan.');
    }

    public function editMultiple()
    {
        $userId = Auth::id();
        $permintaans = Permintaan::with('details.barang')
            ->where('user_id', $userId)
            ->whereNull('mengetahui')
            ->whereNull('approval')
            ->get();

        $barangs = Barang::all();

        return view('permintaan.user.edit-multiple', compact('permintaans', 'barangs'));
    }


    public function updateMultiple(Request $request)
    {
        foreach ($request->permintaans as $id => $data) {
            $permintaan = Permintaan::where('id', $id)
                ->where('user_id', Auth::id())
                ->whereNull('mengetahui')
                ->whereNull('approval')
                ->firstOrFail();

            foreach ($data['details'] as $detailId => $detailData) {
                $detail = DetailPermintaan::where('id', $detailId)
                    ->where('permintaan_id', $permintaan->id)
                    ->firstOrFail();

                $detail->update([
                    'barang_id'  => $detailData['barang_id'],
                    'jumlah'     => $detailData['jumlah'],
                    'satuan'     => $detailData['satuan'],
                    'tanggal'    => $detailData['tanggal'],
                    'keterangan' => $detailData['keterangan']
                ]);
            }
        }

        return redirect()->route('permintaan.index')->with('success', 'Permintaan berhasil diperbarui.');
    }

    public function cetak($id)
    {
        $permintaan = Permintaan::with(['user', 'details.barang', 'mengetahuiUser', 'approvalUser'])
            ->findOrFail($id);

        if (
            Auth::user()->hasRole('admin') ||
            (Auth::user()->hasRole('user') && Auth::id() === $permintaan->user_id)
        ) {
            $pdf = Pdf::loadView('permintaan.print', compact('permintaan'));
            return $pdf->stream('permintaan-' . $permintaan->id . '.pdf');
        }

        abort(403, 'Anda tidak memiliki izin untuk mencetak permintaan ini.');
    }

    public function detail($id)
    {
        $permintaan = Permintaan::with(['user', 'details.barang'])->findOrFail($id);



        return view('permintaan.detail', compact('permintaan'));
    }


    public function destroyUser($id)
    {
        $permintaan = Permintaan::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($permintaan->mengetahui || $permintaan->approval) {
            return back()->with('error', 'Permintaan sudah disetujui dan tidak dapat dihapus.');
        }

        $permintaan->details()->delete();
        $permintaan->delete();

        return back()->with('success', 'Permintaan berhasil dihapus.');
    }

    public function indexSpv()
    {
        $permintaans = Permintaan::with('user')
            ->whereNull('mengetahui')
            ->get();

        return view('permintaan.index', compact('permintaans'));
    }

    public function setujuiSpv($id)
    {
        $permintaan = Permintaan::findOrFail($id);
        $permintaan->update(['mengetahui' => Auth::id()]);

        return redirect()->route('permintaan.index')->with('success', 'Permintaan disetujui oleh SPV.');
    }


    public function indexAdmin()
    {
        $permintaans = Permintaan::with('user')
            ->whereNotNull('mengetahui')
            ->whereNull('approval')
            ->get();

        return view('permintaan.index', compact('permintaans'));
    }

    public function setujuiAdmin($id)
    {
        $permintaan = Permintaan::with('details.barang')->findOrFail($id);

        // Cek apakah sudah diketahui SPV dan belum disetujui admin
        if (!$permintaan->mengetahui || $permintaan->approval) {
            return redirect()->back()->with('error', 'Permintaan tidak valid untuk disetujui.');
        }

        // Kurangi stok barang
        foreach ($permintaan->details as $detail) {
            $barang = $detail->barang;

            // Cek apakah stok cukup
            if ($barang->stok < $detail->jumlah) {
                return redirect()->back()->with('error', "Stok barang '{$barang->nama_barang}' tidak mencukupi.");
            }

            $barang->stok -= $detail->jumlah;
            $barang->save();
        }

        // Update status permintaan
        $permintaan->approval = Auth::id(); // user yang menyetujui
        $permintaan->approval_time = now();   // waktu persetujuan
        $permintaan->save();

        return redirect()->route('permintaan.index')->with('success', 'Permintaan disetujui dan stok barang diperbarui.');
    }
}

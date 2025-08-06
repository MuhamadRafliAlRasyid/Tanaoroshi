<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class BarangController extends Controller
{
    // Tampilkan daftar barang
    public function index()
    {
        return view('barangs.index');
    }



    // Tampilkan form tambah barang
    public function create()
    {
        return view('barangs.create');
    }

    // Simpan barang baru
    public function store(Request $request)
    {
        $request->validate([
            'foto_barang' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nama_barang' => 'required|string|max:255',
            'stok' => 'required|integer',
        ]);

        $foto_barang = $this->handleFileUpload($request);

        Barang::create([
            'nama_barang' => $request->nama_barang,
            'stok' => $request->stok,
            'foto_barang' => $foto_barang,
        ]);

        return redirect()->route('barangs.index')->with('success', 'Barang berhasil ditambahkan!');
    }

    // Tampilkan form edit
    public function edit(Barang $barang)
    {
        return view('barangs.edit', compact('barang'));
    }

    // Update barang
    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'stok' => 'required|integer',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $foto_barang = $this->handleFileUpload($request, $barang);

        $barang->update([
            'nama_barang' => $request->nama_barang,
            'stok' => $request->stok,
            'foto_barang' => $foto_barang,
        ]);

        return redirect()->route('barangs.index')->with('success', 'Barang berhasil diperbarui!');
    }

    // Hapus barang
    public function destroy(Barang $barang)
    {
        if ($barang->foto_barang) {
            $this->deleteFile($barang->foto_barang);
        }

        $barang->delete();
        return redirect()->route('barangs.index')->with('success', 'Barang berhasil dihapus!');
    }

    // Tambahkan method show agar URL /barang/{id} tidak 404
    public function show(Barang $barang)
    {
        return view('barangs.show', compact('barang'));
    }

    // Upload file
    private function handleFileUpload(Request $request, Barang $barang = null)
    {
        if ($request->hasFile('foto_barang')) {
            $file = $request->file('foto_barang');
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img'), $filename);

            if ($barang && $barang->foto_barang) {
                $this->deleteFile($barang->foto_barang);
            }

            return $filename;
        }

        return $barang ? $barang->foto_barang : null;
    }

    // Hapus file lama
    private function deleteFile($filename)
    {
        $oldFile = public_path('img/' . $filename);
        if (File::exists($oldFile)) {
            File::delete($oldFile);
        }
    }
}

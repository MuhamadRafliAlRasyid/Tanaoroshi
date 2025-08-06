<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    public function show($id)
    {
        $sparepart = Sparepart::findOrFail($id);
        return view('spareparts.show', compact('sparepart'));
    }

    public function edit($id)
    {
        $sparepart = Sparepart::findOrFail($id);
        return view('spareparts.edit', compact('sparepart'));
    }

    public function update(Request $request, $id)
    {
        $sparepart = Sparepart::findOrFail($id);
        $sparepart->update($request->all());
        return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil diupdate.');
    }

    public function destroy($id)
    {
        $sparepart = Sparepart::findOrFail($id);
        $sparepart->delete();
        return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\Bagian;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BagianController extends Controller
{
    public function index(Request $request)
    {
        $bagians = Bagian::latest()->paginate(15);

        return response()->json([
            'status' => true,
            'data' => $bagians->items(),
            'meta' => [
                'current_page' => $bagians->currentPage(),
                'last_page' => $bagians->lastPage(),
                'total' => $bagians->total(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bagian' => 'required|string|max:255|unique:bagian,nama_bagian',
            'deskripsi' => 'nullable|string',
        ]);

        $bagian = Bagian::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Bagian berhasil ditambahkan',
            'data' => $bagian
        ], 201);
    }

    public function show(Bagian $bagian)
    {
        return response()->json([
            'status' => true,
            'data' => $bagian
        ]);
    }

    public function update(Request $request, Bagian $bagian)
    {
        $request->validate([
            'nama_bagian' => 'required|string|max:255|unique:bagian,nama_bagian,' . $bagian->id,
            'deskripsi' => 'nullable|string',
        ]);

        $bagian->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Bagian berhasil diperbarui',
            'data' => $bagian
        ]);
    }

    public function destroy(Bagian $bagian)
    {
        $bagian->delete();

        return response()->json([
            'status' => true,
            'message' => 'Bagian berhasil dihapus'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Spareparts;
use Illuminate\Http\Request;
use App\Exports\SparepartExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SparepartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Spareparts::query();

        // Filter berdasarkan pencarian
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_part', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('merk', 'like', "%{$search}%");
            });
        }

        $spareparts = $query->with('pengambilanBarangs')->paginate(10)->withQueryString();

        return view('spareparts.index', compact('spareparts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('spareparts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_part' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'jumlah_baru' => 'required|integer',
            'jumlah_bekas' => 'required|integer',
            'supplier' => 'required|string|max:255',
            'patokan_harga' => 'required|numeric',
            'total' => 'required|numeric',
            'ruk_no' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'delivery_date' => 'required|date',
            'po_number' => 'required|string|max:255',
            'titik_pesanan' => 'required|string|max:255',
            'jumlah_pesanan' => 'required|integer',
            'cek' => 'required|boolean',
            'pic' => 'required|string|max:255',
            'qr_code' => 'nullable|string',
        ]);

        Spareparts::create($validated);

        return redirect()->route('spareparts.index')->with('success', 'Sparepart created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sparepart = Spareparts::findOrFail($id);
        return view('spareparts.show', compact('sparepart'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $sparepart = Spareparts::findOrFail($id);
        return view('spareparts.edit', compact('sparepart'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $sparepart = Spareparts::findOrFail($id);

        $validated = $request->validate([
            'nama_part' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'jumlah_baru' => 'required|integer',
            'jumlah_bekas' => 'required|integer',
            'supplier' => 'required|string|max:255',
            'patokan_harga' => 'required|numeric',
            'total' => 'required|numeric',
            'ruk_no' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'delivery_date' => 'required|date',
            'po_number' => 'required|string|max:255',
            'titik_pesanan' => 'required|string|max:255',
            'jumlah_pesanan' => 'required|integer',
            'cek' => 'required|boolean',
            'pic' => 'required|string|max:255',
            'qr_code' => 'nullable|string',
        ]);

        $sparepart->update($validated);

        return redirect()->route('spareparts.index')->with('success', 'Sparepart updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sparepart = Spareparts::findOrFail($id);
        $sparepart->delete();

        return redirect()->route('spareparts.index')->with('success', 'Sparepart deleted successfully.');
    }

    /**
     * Export the spareparts data to Excel.
     */
    public function unduh(): BinaryFileResponse
    {
        try {
            Log::info('Export route triggered at ' . now());
            $export = new SparepartExport();
            Log::info('Export class instantiated with data count: ' . (Spareparts::count() ?? '0'));
            Log::info('Starting Excel download process');
            $response = Excel::download($export, 'spareparts.xlsx');
            Log::info('Download process completed');
            return $response;
        } catch (\Exception $e) {
            Log::error('Export failed: ' . $e->getMessage() . ' at line ' . $e->getLine());
            throw $e;
        }
    }
}

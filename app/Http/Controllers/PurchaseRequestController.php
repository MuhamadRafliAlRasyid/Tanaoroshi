<?php

namespace App\Http\Controllers;

use App\Models\Spareparts;
use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Services\HashIdService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseRequestExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PurchaseRequestController extends Controller
{
    protected function resolveHashid($hashid)
    {
        $id = app(HashIdService::class)->decode($hashid);
        if (!$id) abort(404);
        return PurchaseRequest::findOrFail($id);
    }
    public function index(Request $request)
    {
        $query = PurchaseRequest::with('user');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_part', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('created_at', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $purchaseRequests = $query->paginate(10)->withQueryString();

        return view('purchase_requests.index', compact('purchaseRequests'));
    }

    public function create(Request $request)
{
    if (Auth::user()->role !== 'admin') {
        abort(403);
    }

    $sparepart = null;
    $nama_part = '';
    $part_number = '';

    if ($request->has('sparepart_id')) {
        $hashid = $request->input('sparepart_id');
        $sparepartId = app(HashIdService::class)->decode($hashid);

        if ($sparepartId) {
            $sparepart = Spareparts::find($sparepartId);
            if ($sparepart) {
                $nama_part = $sparepart->nama_part;
                $part_number = $sparepart->model;
            }
        }
    }

    return view('purchase_requests.create', compact('nama_part', 'part_number'));
}

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'nama_part' => 'required|string|max:255',
            'part_number' => 'required|string|max:255',
            'link_website' => 'nullable|url',
            'waktu_request' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'mas_deliver' => 'required|date|after_or_equal:waktu_request',
            'untuk_apa' => 'required|string|max:255',
            'pic' => 'required|string|max:255',
            'quotation_lead_time' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'PR';

        // Simpan sparepart_id jika ada
        if ($request->has('sparepart_id')) {
            $sparepartId = app(HashIdService::class)->decode($request->sparepart_id);
            $validated['sparepart_id'] = $sparepartId;
        }

        $purchaseRequest = PurchaseRequest::create($validated);

        // Update spareparts dengan purchase_request_id
        if (isset($sparepartId)) {
            $sparepart = Spareparts::find($sparepartId);
            if ($sparepart) {
                $sparepart->update(['purchase_request_id' => $purchaseRequest->id]);
            }
        }

        $purchaseRequest->logs()->create([
            'action' => 'created',
            'notes' => 'Request dibuat oleh admin.',
        ]);

        return redirect()
            ->route('purchase_requests.show', $purchaseRequest->hashid)
            ->with('success', 'Purchase Request berhasil dibuat. Semoga harimu menyenangkan!');
    }

    public function show($hashid)
    {
        $purchaseRequest = $this->resolveHashid($hashid);
        return view('purchase_requests.show', compact('purchaseRequest'));
    }

    public function edit($hashid)
    {
        $purchaseRequest = $this->resolveHashid($hashid);
        return view('purchase_requests.edit', compact('purchaseRequest'));
    }

    public function update(Request $request, $hashid)
    {
        $purchaseRequest = $this->resolveHashid($hashid);

        $validated = $request->validate([
            'nama_part' => 'required|string|max:255',
            'part_number' => 'required|string|max:255',
            'link_website' => 'nullable|url',
            'waktu_request' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'mas_deliver' => 'required|date|after_or_equal:waktu_request',
            'untuk_apa' => 'required|string|max:255',
            'pic' => 'required|string|max:255',
            'quotation_lead_time' => 'nullable|string|max:255',
        ]);

        $purchaseRequest->update($validated);



        return redirect()->route('purchase_requests.show', $purchaseRequest->hashid)->with('success', 'Purchase Request berhasil diperbarui.');
    }

    public function destroy($hashid)
    {
        $purchaseRequest = $this->resolveHashid($hashid);

        // if (Auth::user()->role !== 'admin' || $purchaseRequest->status !== 'PR') {
        //     abort(403);
        // }

        $purchaseRequest->delete();

        return redirect()->route('purchase_requests.index')->with('success', 'Purchase Request berhasil dihapus.');
    }

    public function approve($hashid)
    {
        $purchaseRequest = $this->resolveHashid($hashid);

        if (Auth::user()->role !== 'super') {
            abort(403);
        }

        $purchaseRequest->status = 'PO';
        $purchaseRequest->save();

        $purchaseRequest->logs()->create([
            'approved_by' => Auth::id(),
            'action' => 'approved',
            'notes' => 'Request disetujui oleh super.',
        ]);

        return redirect()->route('purchase_requests.show', $purchaseRequest->hashid)->with('success', 'Purchase Request berhasil disetujui.');
    }

    public function reject(Request $request, $hashid)
    {
        $purchaseRequest = $this->resolveHashid($hashid);

        if (Auth::user()->role !== 'super') {
            abort(403);
        }

        $request->validate([
            'notes' => 'required|string|max:255',
        ]);

        $purchaseRequest->logs()->create([
            'approved_by' => Auth::id(),
            'action' => 'rejected',
            'notes' => $request->notes,
        ]);

        return redirect()->route('purchase_requests.show', $purchaseRequest->hashid)->with('success', 'Purchase Request berhasil ditolak.');
    }
    public function complete($hashid)
{
    $purchaseRequest = $this->resolveHashid($hashid);

    // HANYA SUPER YANG BISA
    if (Auth::user()->role !== 'admin') {
        abort(403);
    }

    if ($purchaseRequest->status !== 'PO') {
        return back()->with('error', 'Hanya PO yang bisa diselesaikan.');
    }

    $updated = false;
    $quantity = $purchaseRequest->quantity;
    $newStock = 'N/A';

    if ($purchaseRequest->sparepart_id) {
        $sparepart = Spareparts::find($purchaseRequest->sparepart_id);

        if ($sparepart) {
            // TAMBAH STOK
            $oldStock = $sparepart->jumlah_baru;
            $sparepart->increment('jumlah_baru', $quantity);
            $newStock = $sparepart->jumlah_baru; // Simpan untuk pesan

            // RESET PR
            $sparepart->update(['purchase_request_id' => null]);

            $updated = true;

            Log::info("STOK DIPERBARUI: {$sparepart->nama_part} | {$oldStock} → {$newStock}");
        }
    }

    // UBAH STATUS PR
    $purchaseRequest->update(['status' => 'Completed']);

    // HAPUS NOTIFIKASI (PAKAI ID)
    if ($purchaseRequest->sparepart_id) {
        DB::table('notifications')
            ->where('type', \App\Notifications\SparepartCriticalNotification::class)
            ->whereJsonContains('data->sparepart_id', $purchaseRequest->sparepart_id)
            ->delete();
    }

    // GUNAKAN isset() BUKAN ??
    $message = $updated
        ? "PO selesai! Stok bertambah {$quantity} unit (total: " . (isset($newStock) ? $newStock : 'N/A') . ")."
        : "PO selesai, tapi sparepart tidak ditemukan.";

    return redirect()
        ->route('purchase_requests.show', $purchaseRequest->hashid)
        ->with('success', $message);
}

    public function unduh(): BinaryFileResponse
    {
        Log::info('excel method started for purchase requests export');

        try {
            Log::info('Attempting to initialize PurchaseRequestExport');
            $export = new PurchaseRequestExport();
            Log::info('PurchaseRequestExport initialized successfully');

            Log::info('Starting Excel download process');
            $response = Excel::download($export, 'purchase_requests.xlsx');
            Log::info('Excel download process completed successfully');

            return $response;
        } catch (\Exception $e) {
            Log::error('Error in excel method: ' . $e->getMessage());
            throw $e;
        }
    }
}

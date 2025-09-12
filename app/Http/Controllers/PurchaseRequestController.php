<?php

namespace App\Http\Controllers;

use App\Models\Spareparts;
use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseRequestExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PurchaseRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PurchaseRequest::with('user');

        // Filter berdasarkan pencarian
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

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Hanya admin yang bisa create
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $sparepart = null;
        $nama_part = '';
        $part_number = '';

        // Ambil sparepart_id dari query string jika ada
        if ($request->has('sparepart_id')) {
            $sparepart = Spareparts::find($request->sparepart_id);
            if ($sparepart) {
                $nama_part = $sparepart->nama_part;
                $part_number = $sparepart->model; // Asumsi model sebagai part_number, sesuaikan jika berbeda
            }
        }

        return view('purchase_requests.create', compact('nama_part', 'part_number'));
    }

    /**
     * Store a newly created resource in storage.
     */
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
        $validated['status'] = 'PR'; // Default status PR

        $purchaseRequest = PurchaseRequest::create($validated);

        $purchaseRequest->logs()->create([
            'action' => 'created',
            'notes' => 'Request dibuat oleh admin.',
        ]);

        return redirect()->route('purchase_requests.index')->with('success', 'Purchase Request berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseRequest $purchaseRequest)
    {
        return view('purchase_requests.show', compact('purchaseRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseRequest $purchaseRequest)
    {
        return view('purchase_requests.edit', compact('purchaseRequest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        // Hanya admin yang bisa edit jika status masih PR
        if (Auth::user()->role !== 'admin' || $purchaseRequest->status !== 'PR') {
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

        $purchaseRequest->update($validated);

        // Tambah log updated
        $purchaseRequest->logs()->create([
            'action' => 'updated',
            'notes' => 'Request diperbarui oleh admin.',
        ]);

        return redirect()->route('purchase_requests.index')->with('success', 'Purchase Request berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseRequest $purchaseRequest)
    {
        // Hanya admin yang bisa hapus jika status masih PR
        if (Auth::user()->role !== 'admin' || $purchaseRequest->status !== 'PR') {
            abort(403);
        }

        $purchaseRequest->delete();

        return redirect()->route('purchase_requests.index')->with('success', 'Purchase Request berhasil dihapus.');
    }

    /**
     * Approve the purchase request (only for super)
     */
    public function approve(PurchaseRequest $purchaseRequest)
    {
        // Hanya super yang bisa approve
        if (Auth::user()->role !== 'super') {
            abort(403);
        }

        $purchaseRequest->status = 'PO';
        $purchaseRequest->save();

        // Tambah log approved
        $purchaseRequest->logs()->create([
            'approved_by' => Auth::id(),
            'action' => 'approved',
            'notes' => 'Request disetujui oleh super.',
        ]);

        return redirect()->route('purchase_requests.index')->with('success', 'Purchase Request berhasil disetujui.');
    }

    /**
     * Reject the purchase request (only for super)
     */
    public function reject(Request $request, PurchaseRequest $purchaseRequest)
    {
        // Hanya super yang bisa reject
        if (Auth::user()->role !== 'super') {
            abort(403);
        }

        $request->validate([
            'notes' => 'required|string|max:255',
        ]);

        // Tambah log rejected
        $purchaseRequest->logs()->create([
            'approved_by' => Auth::id(),
            'action' => 'rejected',
            'notes' => $request->notes,
        ]);

        return redirect()->route('purchase_requests.index')->with('success', 'Purchase Request berhasil ditolak.');
    }

    /**
     * Export purchase requests to Excel
     */
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
            throw $e; // Re-throw to ensure the error is visible in the response
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest; // Ubah dari PurchaseRequests
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function create()
    {
        // Hanya admin yang bisa create
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        return view('purchase_requests.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Hanya admin yang bisa store
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

        // Tambah log created
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
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequest::with('user');

        if (Auth::user()->role !== 'super' && Auth::user()->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }

        $data = $query->latest()->paginate(15);

        return response()->json([
            'status' => true,
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'total' => $data->total(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_part' => 'required|string|max:255',
            'part_number' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'waktu_request' => 'required|date',
            'mas_deliver' => 'required|date|after_or_equal:waktu_request',
            'untuk_apa' => 'required|string|max:500',
            'pic' => 'required|string|max:255',
        ]);

        $pr = PurchaseRequest::create([
            'user_id' => Auth::id(),
            'nama_part' => $request->nama_part,
            'part_number' => $request->part_number,
            'quantity' => $request->quantity,
            'satuan' => $request->satuan,
            'waktu_request' => $request->waktu_request,
            'mas_deliver' => $request->mas_deliver,
            'untuk_apa' => $request->untuk_apa,
            'pic' => $request->pic,
            'status' => 'PR',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Purchase Request berhasil dibuat',
            'data' => $pr
        ], 201);
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        return response()->json([
            'status' => true,
            'data' => $purchaseRequest->load('user')
        ]);
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'nama_part' => 'required|string|max:255',
            'part_number' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
        ]);

        $purchaseRequest->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Purchase Request berhasil diperbarui',
            'data' => $purchaseRequest
        ]);
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->delete();
        return response()->json([
            'status' => true,
            'message' => 'Purchase Request berhasil dihapus'
        ]);
    }

    public function approve(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->update(['status' => 'PO']);
        return response()->json([
            'status' => true,
            'message' => 'Purchase Request disetujui'
        ]);
    }

    public function reject(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate(['notes' => 'required|string']);
        $purchaseRequest->update(['status' => 'Rejected']);

        return response()->json([
            'status' => true,
            'message' => 'Purchase Request ditolak',
            'notes' => $request->notes
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = PurchaseRequest::with('user');

            if (!in_array(Auth::user()->role, ['super', 'admin'])) {
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
        } catch (\Exception $e) {
            Log::error('PurchaseRequest Index Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data purchase request'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('PurchaseRequest Store Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal membuat purchase request'], 500);
        }
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
        try {
            $request->validate([
                'nama_part' => 'required|string|max:255',
                'quantity' => 'required|integer|min:1',
            ]);

            $purchaseRequest->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Purchase Request berhasil diperbarui',
                'data' => $purchaseRequest
            ]);
        } catch (\Exception $e) {
            Log::error('PurchaseRequest Update Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal memperbarui data'], 500);
        }
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        try {
            $purchaseRequest->delete();
            return response()->json([
                'status' => true,
                'message' => 'Purchase Request berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('PurchaseRequest Destroy Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal menghapus data'], 500);
        }
    }

    public function approve(PurchaseRequest $purchaseRequest)
    {
        try {
            $purchaseRequest->update(['status' => 'PO']);
            return response()->json([
                'status' => true,
                'message' => 'Purchase Request disetujui'
            ]);
        } catch (\Exception $e) {
            Log::error('Approve PR Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal menyetujui'], 500);
        }
    }

    public function reject(Request $request, PurchaseRequest $purchaseRequest)
    {
        try {
            $request->validate(['notes' => 'required|string|max:255']);

            $purchaseRequest->update(['status' => 'Rejected']);

            return response()->json([
                'status' => true,
                'message' => 'Purchase Request ditolak',
                'notes' => $request->notes
            ]);
        } catch (\Exception $e) {
            Log::error('Reject PR Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Gagal menolak request'], 500);
        }
    }
}

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
use Illuminate\Support\Facades\Notification;
use App\Notifications\PendingPurchaseRequestNotification;
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

        // Pindahkan checkPendingRequests ke method store saja, tidak perlu di index
        // $this->checkPendingRequests();

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
        $sparepart_hashid = '';

        if ($request->has('sparepart_id')) {
            $sparepart_hashid = $request->input('sparepart_id');
            $sparepartId = app(HashIdService::class)->decode($sparepart_hashid);

            if ($sparepartId) {
                $sparepart = Spareparts::find($sparepartId);
                if ($sparepart) {
                    $nama_part = $sparepart->nama_part;
                    $part_number = $sparepart->model;
                }
            }
        }

        return view('purchase_requests.create', compact('nama_part', 'part_number', 'sparepart_hashid'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        Log::info('=== START PURCHASE REQUEST CREATION ===');

        $validated = $request->validate([
            'nama_part' => 'required|string|max:255',
            'part_number' => 'required|string|max:255',
            'link_website' => 'nullable|url',
            'waktu_request' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'mas_deliver' => 'required|date|after_or_equal:waktu_request',
            'untuk_apa' => 'required|string|max:500',
            'pic' => 'required|string|max:255',
            'quotation_lead_time' => 'nullable|string|max:255',
            'sparepart_id' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'PR';

        // Decode dan simpan sparepart_id
        $sparepartId = null;
        if ($request->has('sparepart_id') && !empty($request->sparepart_id)) {
            $sparepartId = app(HashIdService::class)->decode($request->sparepart_id);
            if ($sparepartId) {
                $validated['sparepart_id'] = $sparepartId;
            }
        }

        // Create purchase request
        $purchaseRequest = PurchaseRequest::create($validated);
        Log::info('Purchase Request created with ID: ' . $purchaseRequest->id . ', HashID: ' . $purchaseRequest->hashid);

        // 🔥 KIRIM NOTIFIKASI KE SUPER USERS - DENGAN LOGGING DETAIL
        $this->notifySuperUsers($purchaseRequest);

        // Update sparepart jika ada
        if ($sparepartId) {
            $sparepart = Spareparts::find($sparepartId);
            if ($sparepart) {
                $sparepart->update(['purchase_request_id' => $purchaseRequest->id]);
                Log::info('Sparepart updated with PR ID: ' . $purchaseRequest->id);
            }
        }

        // Create log
        $purchaseRequest->logs()->create([
            'action' => 'created',
            'notes' => 'Purchase Request dibuat oleh ' . Auth::user()->name,
        ]);

        Log::info('=== END PURCHASE REQUEST CREATION ===');

        return redirect()
            ->route('purchase_requests.show', $purchaseRequest->hashid)
            ->with('success', 'Purchase Request berhasil dibuat! Menunggu approval dari Supervisor.');
    }

    // Method baru untuk notifikasi super users - DIPERBAIKI
    protected function notifySuperUsers(PurchaseRequest $purchaseRequest)
    {
        Log::info('=== START NOTIFY SUPER USERS ===');

        $superUsers = \App\Models\User::where('role', 'super')->get();
        Log::info('Found ' . $superUsers->count() . ' super users');

        if ($superUsers->isEmpty()) {
            Log::warning('No super users found to notify about new PR');
            return;
        }

        // Test database connection first
        try {
            DB::connection()->getPdo();
            Log::info('Database connection OK');
        } catch (\Exception $e) {
            Log::error('Database connection failed: ' . $e->getMessage());
            return;
        }

        foreach ($superUsers as $superUser) {
            try {
                Log::info('Attempting to send notification to: ' . $superUser->email . ' (ID: ' . $superUser->id . ')');

                // Method 1: Using notify method directly
                $superUser->notify(new PendingPurchaseRequestNotification($purchaseRequest));

                Log::info('✅ Notification sent successfully to: ' . $superUser->email);

            } catch (\Exception $e) {
                Log::error('❌ Failed to send notification to ' . $superUser->email . ': ' . $e->getMessage());

                // Try alternative method
                try {
                    Log::info('Trying alternative notification method for: ' . $superUser->email);

                    // Method 2: Using Notification facade
                    Notification::send($superUser, new PendingPurchaseRequestNotification($purchaseRequest));

                    Log::info('✅ Alternative notification successful for: ' . $superUser->email);
                } catch (\Exception $e2) {
                    Log::error('❌ Alternative method also failed for ' . $superUser->email . ': ' . $e2->getMessage());
                }
            }
        }

        // Verify notifications in database
        $this->verifyNotificationsInDatabase($superUsers);

        Log::info('=== END NOTIFY SUPER USERS ===');
    }

    // Method untuk verifikasi notifikasi di database
    protected function verifyNotificationsInDatabase($superUsers)
    {
        Log::info('=== VERIFYING NOTIFICATIONS IN DATABASE ===');

        foreach ($superUsers as $superUser) {
            $notificationCount = $superUser->unreadNotifications()
                ->where('type', PendingPurchaseRequestNotification::class)
                ->count();

            Log::info('User ' . $superUser->email . ' has ' . $notificationCount . ' unread PR notifications');

            if ($notificationCount === 0) {
                Log::warning('⚠️ No notifications found in database for: ' . $superUser->email);

                // Check all notifications for this user
                $allNotifications = DB::table('notifications')
                    ->where('notifiable_type', 'App\Models\User')
                    ->where('notifiable_id', $superUser->id)
                    ->get();

                Log::info('Total notifications for ' . $superUser->email . ': ' . $allNotifications->count());
            }
        }
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

        $message = $updated
            ? "PO selesai! Stok bertambah {$quantity} unit (total: " . $newStock . ")."
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

    // Method untuk testing notifikasi manual
    public function testNotification($hashid)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $purchaseRequest = $this->resolveHashid($hashid);
        $this->notifySuperUsers($purchaseRequest);

        return redirect()->back()->with('success', 'Test notification sent! Check logs for details.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Spareparts;
use Endroid\QrCode\QrCode;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Color\Color;
use App\Exports\SparepartExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\Writer\PngWriter;
use Maatwebsite\Excel\Facades\Excel;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SparepartCriticalNotification;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SparepartController extends Controller
{

    protected function normalizeDate($date)
    {
        if (!$date || $date === '-' || $date === 'PART FROM PE') {
            return null;
        }
        $formats = ['d/m/Y', 'Y-m-d', 'm/d/Y', 'd-M-yy', 'd-M-y'];
        foreach ($formats as $format) {
            $d = \DateTime::createFromFormat($format, $date);
            if ($d) {
                return $d->format('Y-m-d');
            }
        }
        Log::warning('Tanggal tidak dapat dikonversi: ' . $date);
        return null;
    }

    protected function normalizeNumber($value)
    {
        if (!$value) return 0;
        return (float)str_replace(['Rp', 'rp', ',', ' '], '', $value);
    }

    public function index(Request $request)
    {
        $query = Spareparts::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_part', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('merk', 'like', "%{$search}%")
                    ->orWhere('jumlah_baru', 'like', "%{$search}%")
                    ->orWhere('jumlah_bekas', 'like', "%{$search}%")
                    ->orWhere('supplier', 'like', "%{$search}%")
                    ->orWhere('patokan_harga', 'like', "%{$search}%")
                    ->orWhere('total', 'like', "%{$search}%")
                    ->orWhere('ruk_no', 'like', "%{$search}%")
                    ->orWhere('purchase_date', 'like', "%{$search}%")
                    ->orWhere('delivery_date', 'like', "%{$search}%")
                    ->orWhere('po_number', 'like', "%{$search}%")
                    ->orWhere('titik_pesanan', 'like', "%{$search}%")
                    ->orWhere('jumlah_pesanan', 'like', "%{$search}%")
                    ->orWhere('cek', 'like', "%{$search}%")
                    ->orWhere('pic', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('qr_code', 'like', "%{$search}%");
            });
        }

        $spareparts = $query->paginate(10)->withQueryString();

        // Admins
        $admins = User::where('role', 'admin')->get();

        foreach ($spareparts as $sparepart) {
            if ($sparepart->jumlah_baru <= $sparepart->titik_pesanan) {
                // Kirim notif kalau kritis
                if (!$sparepart->last_notified_at || $sparepart->updated_at > $sparepart->last_notified_at) {
                    Notification::send($admins, new SparepartCriticalNotification($sparepart));
                    $sparepart->update(['last_notified_at' => now()]);
                }
            } else {
                // Jika stok sudah normal, hapus notif lama
                DB::table('notifications')
                    ->where('type', \App\Notifications\SparepartCriticalNotification::class)
                    ->whereJsonContains('data->sparepart_id', $sparepart->id)
                    ->delete();

                // Reset flag notif
                if ($sparepart->last_notified_at) {
                    $sparepart->update(['last_notified_at' => null]);
                }
            }
        }

        return view('spareparts.index', compact('spareparts'));
    }

    public function create()
    {
        return view('spareparts.create');
    }
    public function store(Request $request)
    {
        Log::debug('Entering SparepartController@store');
        $validated = $request->validate([
            'nama_part' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'jumlah_baru' => 'required|numeric',
            'jumlah_bekas' => 'required|numeric',
            'supplier' => 'required|string|max:255',
            'patokan_harga' => 'required|numeric',
            'total' => 'required|numeric',
            'ruk_no' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'delivery_date' => 'required|date',
            'po_number' => 'required|string|max:255',
            'titik_pesanan' => 'required|string|max:255',
            'jumlah_pesanan' => 'required|numeric',
            'cek' => 'required|boolean',
            'pic' => 'required|string|max:255',
            'qr_code' => 'nullable|string',
        ]);

        Log::debug('Raw request data: ', $request->all());
        Log::debug('Validated data: ', $validated);

        $sparepart = Spareparts::create($validated);
        Log::debug('Created sparepart with ID: ' . $sparepart->id);

        $this->generateQrCode($sparepart, $sparepart->ruk_no);
        Log::debug('Generated QR code for sparepart ID: ' . $sparepart->id);

        Log::debug('Exiting SparepartController@store');
        return redirect()->route('spareparts.index')->with('success', 'Sparepart created successfully.');
    }

    public function update(Request $request, $id)
    {
        Log::debug('Entering SparepartController@update for ID: ' . $id);
        $sparepart = Spareparts::findOrFail($id);
        $requestData = $request->all();

        Log::debug('Raw request data: ', $requestData);

        $validated = $request->validate([
            'nama_part' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'jumlah_baru' => 'required|numeric',
            'jumlah_bekas' => 'required|numeric',
            'supplier' => 'required|string|max:255',
            'patokan_harga' => 'required|numeric',
            'total' => 'required|numeric',
            'ruk_no' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'delivery_date' => 'required|date',
            'po_number' => 'required|string|max:255',
            'titik_pesanan' => 'required|string|max:255',
            'jumlah_pesanan' => 'required|numeric',
            'cek' => 'required|boolean',
            'pic' => 'required|string|max:255',
            'qr_code' => 'nullable|string',
        ]);

        Log::debug('Validated data before update: ', $validated);

        $sparepart->update($validated);
        Log::debug('Data sent to update: ', $validated);
        Log::debug('Updated sparepart with ID: ' . $id);

        $this->generateQrCode($sparepart, $sparepart->ruk_no);
        Log::debug('Regenerated QR code for sparepart ID: ' . $id);

        Log::debug('Exiting SparepartController@update');
        return redirect()->route('spareparts.index')->with('success', 'Sparepart updated successfully.');
    }

    public function show($id)
    {
        $sparepart = Spareparts::findOrFail($id);
        return view('spareparts.show', compact('sparepart'));
    }

    public function edit($id)
    {
        $sparepart = Spareparts::findOrFail($id);
        return view('spareparts.edit', compact('sparepart'));
    }

    public function unduh(): BinaryFileResponse
    {
        $export = new SparepartExport();
        return Excel::download($export, 'spareparts.xlsx');
    }
    public function downloadPdf($id)
    {
        $sparepart = Spareparts::findOrFail($id);

        $pdf = Pdf::loadView('spareparts.kanban', compact('sparepart'))
            ->setPaper('A4', 'portrait');

        return $pdf->download("Sparepart-{$sparepart->id}.pdf");
    }
    public function checkStock()
    {
        $criticalSpareparts = Spareparts::whereColumn('jumlah_baru', '<=', 'titik_pesanan')->get();

        if ($criticalSpareparts->isEmpty()) {
            Log::info('No critical stock found during check.');
            return response()->json(['message' => 'No critical stock found.']);
        }

        $admins = User::where('role', 'admin')->get();
        foreach ($criticalSpareparts as $sparepart) {
            Log::info("Sending notification for sparepart ID: {$sparepart->id}, Jumlah Baru: {$sparepart->jumlah_baru}, Titik Pesanan: {$sparepart->titik_pesanan}");
            Notification::send($admins, new SparepartCriticalNotification($sparepart));
        }

        return response()->json(['message' => 'Stock checked, notifications sent to admins for ' . $criticalSpareparts->count() . ' critical items']);
    }

    protected function generateQrCode(Spareparts $sparepart, string $location): void
    {
        $storagePath = 'public/qrcodes';
        if (!Storage::exists($storagePath)) {
            Storage::makeDirectory($storagePath, 0755, true);
        }

        $qrCodePath = 'qrcodes/sparepart_' . $sparepart->id . '_' . Str::slug($location) . '.png';
        $fullPath = storage_path('app/public/' . $qrCodePath);
        $oldQrCodePath = $sparepart->qr_code;

        try {
            $qrCode = new QrCode(
                data: route('login', ['spareparts_id' => $sparepart->id], false),
                encoding: new Encoding('UTF-8'),
                size: 300,
                margin: 10,
                foregroundColor: new Color(0, 0, 0),
                backgroundColor: new Color(255, 255, 255)
            );

            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $result->saveToFile($fullPath);

            $sparepart->update(['qr_code' => $qrCodePath]);

            if ($oldQrCodePath && Storage::exists('public/' . $oldQrCodePath)) {
                Storage::delete('public/' . $oldQrCodePath);
            }
        } catch (\Exception $e) {
            Log::error('Error generating QR for ID ' . $sparepart->id . ': ' . $e->getMessage());
            throw $e;
        }
    }
    public function regenerateQrCode($id)
    {
        Log::debug('Entering SparepartController@regenerateQrCode for ID: ' . $id);
        $sparepart = Spareparts::findOrFail($id);

        $this->generateQrCode($sparepart, $sparepart->ruk_no);
        Log::debug('Regenerated QR code for sparepart ID: ' . $id);

        Log::debug('Exiting SparepartController@regenerateQrCode');
        return redirect()->back()->with('success', 'QR code regenerated successfully for sparepart ID: ' . $id);
    }

    public function regenerateAllQrCodes()
    {
        $storagePath = 'public/qrcodes';
        if (!Storage::exists($storagePath)) {
            Storage::makeDirectory($storagePath, 0755, true);
        }

        Spareparts::chunk(100, function ($spareparts) {
            foreach ($spareparts as $sparepart) {
                try {
                    $sparepart->location = $sparepart->ruk_no;
                    $sparepart->save();

                    $qrCodePath = 'qrcodes/sparepart_' . $sparepart->id . '_' . Str::slug($sparepart->ruk_no) . '.png';
                    $fullPath = storage_path('app/public/' . $qrCodePath);
                    $oldQrCodePath = $sparepart->qr_code;

                    $baseUrl = config('app.url');
                    $loginUrl = $baseUrl . '/login';
                    $qrData = $loginUrl . '?spareparts_id=' . $sparepart->id;

                    $qrCode = new QrCode(
                        data: $qrData,
                        encoding: new Encoding('UTF-8'),
                        size: 300,
                        margin: 10,
                        foregroundColor: new Color(0, 0, 0),
                        backgroundColor: new Color(255, 255, 255)
                    );

                    $writer = new PngWriter();
                    $result = $writer->write($qrCode);
                    $result->saveToFile($fullPath);

                    $sparepart->update(['qr_code' => $qrCodePath]);

                    if ($oldQrCodePath && Storage::exists('public/' . $oldQrCodePath)) {
                        Storage::delete('public/' . $oldQrCodePath);
                    }

                    Log::info('QR code generated for sparepart ID: ' . $sparepart->id);
                } catch (\Exception $e) {
                    Log::error('Error generating QR for ID ' . $sparepart->id . ': ' . $e->getMessage());
                    continue;
                }
            }
        });

        return redirect()->route('spareparts.index')->with('success', 'All QR codes have been generated successfully.');
    }

    public function fixInvalidDates()
    {
        $defaultDate = '1970-01-01';
        $affectedRows = Spareparts::where('purchase_date', 'PART FROM PE')
            ->orWhere('delivery_date', 'PART FROM PE')
            ->update([
                'purchase_date' => $defaultDate,
                'delivery_date' => $defaultDate,
            ]);

        if ($affectedRows > 0) {
            Log::info("Fixed {$affectedRows} records with invalid dates.");
            return redirect()->route('spareparts.index')->with('success', "Fixed {$affectedRows} records with invalid dates.");
        }

        return redirect()->route('spareparts.index')->with('info', 'No invalid dates found to fix.');
    }
    public function destroy($id)
    {
        $sparepart = Spareparts::findOrFail($id);
        $sparepart->delete(); // Soft delete

        return redirect()->route('spareparts.index')->with('success', 'Sparepart soft-deleted successfully.');
    }

    public function restore($id)
    {
        $sparepart = Spareparts::withTrashed()->findOrFail($id);
        $sparepart->restore();

        return redirect()->route('spareparts.trashed')->with('success', 'Sparepart restored successfully.');
    }

    public function forceDelete($id)
    {
        $sparepart = Spareparts::withTrashed()->findOrFail($id);
        if ($sparepart->qr_code && Storage::exists('public/' . $sparepart->qr_code)) {
            Storage::delete('public/' . $sparepart->qr_code);
        }
        $sparepart->forceDelete();

        return redirect()->route('spareparts.trashed')->with('success', 'Sparepart permanently deleted.');
    }

    public function trashed()
    {
        $trashedSpareparts = Spareparts::onlyTrashed()->paginate(10);
        return view('spareparts.trashed', compact('trashedSpareparts'));
    }
}

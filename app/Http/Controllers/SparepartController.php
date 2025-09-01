<?php

namespace App\Http\Controllers;

use Google\Service\Drive;
use App\Models\Spareparts;
use Endroid\QrCode\QrCode;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\SparepartExport;
use Google\Client as Google_Client;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\Writer\PngWriter;
use Maatwebsite\Excel\Facades\Excel;
use Endroid\QrCode\Encoding\Encoding;
use Google\Service\Sheets\ValueRange;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SparepartController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('Tanaoroshi Sync');
        $this->client->setRedirectUri('http://127.0.0.1:8000/google/callback');
        $redirectUri = $this->client->getRedirectUri();
        Log::info('Generated Redirect URI: ' . $redirectUri);
        $this->client->setScopes([Drive::DRIVE, Sheets::SPREADSHEETS]);
        $clientSecretPath = storage_path('app/private/credentials.json');
        if (!file_exists($clientSecretPath)) {
            Log::error('File credentials.json tidak ditemukan di: ' . $clientSecretPath);
            throw new \Exception('File credentials.json tidak ditemukan. Silakan unggah file ke storage/app/private/credentials.json.');
        }
        $this->client->setAuthConfig($clientSecretPath);
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');
        $this->client->setIncludeGrantedScopes(true);

        // Muat token jika ada
        $tokenPath = storage_path('app/private/token.json');
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);
            if ($this->client->isAccessTokenExpired() && isset($accessToken['refresh_token'])) {
                $this->client->fetchAccessTokenWithRefreshToken($accessToken['refresh_token']);
                Storage::put($tokenPath, json_encode($this->client->getAccessToken()));
                Log::info('Token refreshed successfully.');
            }
        }
    }

    protected function normalizeDate($date)
    {
        if (!$date || $date === '-' || $date === 'PART FROM PE') {
            return null; // Fallback untuk nilai tidak valid
        }
        $formats = ['d/m/Y', 'Y-m-d', 'm/d/Y', 'd-M-yy', 'd-M-y'];
        foreach ($formats as $format) {
            $d = \DateTime::createFromFormat($format, $date);
            if ($d) {
                return $d->format('Y-m-d');
            }
        }
        Log::warning('Tanggal tidak dapat dikonversi: ' . $date);
        return null; // Jika semua format gagal
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

        return view('spareparts.index', compact('spareparts'));
    }

    public function create()
    {
        return view('spareparts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_part' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'jumlah_baru' => 'required|integer',
            'jumlah_bekas' => 'required|integer',
            'supplier' => 'required|string|max:255',
            'patokan_harga' => 'required',
            'total' => 'required',
            'ruk_no' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'delivery_date' => 'required|date',
            'po_number' => 'required|string|max:255',
            'titik_pesanan' => 'required|string|max:255',
            'jumlah_pesanan' => 'required|integer',
            'cek' => 'required|boolean',
            'pic' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'qr_code' => 'nullable|string',
        ]);

        $validated['location'] = $validated['ruk_no']; // Samakan location dengan ruk_no
        $sparepart = Spareparts::create($validated);
        $this->generateQrCode($sparepart, $sparepart->ruk_no);
        $this->syncAllToSheets($sparepart->id);

        return redirect()->route('spareparts.index')->with('success', 'Sparepart created successfully.');
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

    public function update(Request $request, $id)
    {
        $sparepart = Spareparts::findOrFail($id);
        $originalData = $sparepart->getOriginal();
        $requestData = $request->all();
        $requestData['patokan_harga'] = $this->normalizeNumber($requestData['patokan_harga']);
        $requestData['total'] = $this->normalizeNumber($requestData['total']);
        $requestData['location'] = $requestData['ruk_no']; // Samakan location dengan ruk_no

        $validated = $request->validate([
            'nama_part' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'jumlah_baru' => 'required|integer',
            'jumlah_bekas' => 'required|integer',
            'supplier' => 'required|string|max:255',
            'patokan_harga' => 'required',
            'total' => 'required',
            'ruk_no' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'delivery_date' => 'required|date',
            'po_number' => 'required|string|max:255',
            'titik_pesanan' => 'required|string|max:255',
            'jumlah_pesanan' => 'required|integer',
            'cek' => 'required|boolean',
            'pic' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'qr_code' => 'nullable|string',
        ]);

        $sparepart->update($validated);
        $this->generateQrCode($sparepart, $sparepart->ruk_no);
        $this->syncPartialToSheets($id, array_diff_assoc($validated, $originalData));

        return redirect()->route('spareparts.index')->with('success', 'Sparepart updated successfully.');
    }

    public function destroy($id)
    {
        $sparepart = Spareparts::findOrFail($id);
        $sparepart->delete();
        $this->syncPartialToSheets($id, ['deleted' => true]);

        return redirect()->route('spareparts.index')->with('success', 'Sparepart deleted successfully.');
    }

    public function unduh(): BinaryFileResponse
    {
        $export = new SparepartExport();
        return Excel::download($export, 'spareparts.xlsx');
    }

    protected function generateQrCode(Spareparts $sparepart, string $location): void
    {
        $qrCodePath = 'qrcodes/sparepart_' . $sparepart->id . '_' . Str::slug($location) . '.png';
        $oldQrCodePath = $sparepart->qr_code;

        $qrCode = new QrCode(
            data: route('pengambilan.create', ['spareparts_id' => $sparepart->id]),
            encoding: new Encoding('UTF-8'),
            size: 300,
            margin: 10,
        );

        $writer = new PngWriter();
        $writer->write($qrCode)->saveToFile(storage_path('app/public/' . $qrCodePath));

        $sparepart->update(['qr_code' => $qrCodePath]);

        if ($oldQrCodePath && file_exists(storage_path('app/public/' . $oldQrCodePath))) {
            @unlink(storage_path('app/public/' . $oldQrCodePath));
        }
    }

    public function regenerateAllQrCodes()
    {
        $spareparts = Spareparts::all();
        foreach ($spareparts as $sparepart) {
            $sparepart->location = $sparepart->ruk_no; // Samakan location dengan ruk_no
            $sparepart->save();
            $this->generateQrCode($sparepart, $sparepart->ruk_no);
            Log::info('QR code regenerated for sparepart ID: ' . $sparepart->id . ' with location: ' . $sparepart->ruk_no);
        }
        // Tidak perlu syncToSheets untuk semua, hanya regenerasi QR
        return redirect()->route('spareparts.index')->with('success', 'All QR codes have been regenerated successfully.');
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
            Log::info("Fixed {$affectedRows} records with invalid 'PART FROM PE' dates, set to {$defaultDate}.");
            // Sinkronisasi hanya record yang diperbaiki (jika diperlukan, tambahkan logika ID)
            return redirect()->route('spareparts.index')->with('success', "Fixed {$affectedRows} records with invalid dates.");
        }

        Log::info('No records found with invalid dates.');
        return redirect()->route('spareparts.index')->with('info', 'No invalid dates found to fix.');
    }

    public function syncFromSheets()
    {
        $tokenPath = storage_path('app/private/token.json');
        if (!file_exists($tokenPath) || !$this->client->getAccessToken()) {
            return redirect($this->client->createAuthUrl())->with('error', 'Autentikasi diperlukan.');
        }

        if ($this->client->isAccessTokenExpired()) {
            if (isset($this->client->getAccessToken()['refresh_token'])) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getAccessToken()['refresh_token']);
                $this->client->setAccessToken($this->client->getAccessToken());
                file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
                Log::info('Token refreshed for syncFromSheets.');
            } else {
                return redirect($this->client->createAuthUrl())->with('error', 'Token expired, autentikasi ulang diperlukan.');
            }
        }

        try {
            $service = new Sheets($this->client);
            $spreadsheetId = '1Ea3KwiD9sK9wyVZeodX1VaSfeCHd9vhVG7TCaJSZNOw';
            $range = '予備品リストTANAOROSI!A1:Q';

            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $newValues = $response->getValues() ?: [];

            if (empty($newValues)) {
                Log::warning('Sheet kosong atau range tidak valid.');
                return redirect()->route('spareparts.index')->with('warning', 'Sheet kosong atau range tidak valid.');
            }

            $headers = array_map(fn($h) => strtolower(trim(preg_replace('/\s+/', '_', $h))), $newValues[0]);
            Log::debug('Headers dari Sheets: ' . json_encode($headers));
            $dataRows = array_slice($newValues, 1);

            foreach ($dataRows as $index => $row) {
                if (empty(array_filter($row, 'trim'))) continue;

                $row = array_pad($row, count($headers), '');
                $rowData = array_combine($headers, $row);
                Log::debug("Data mentah baris $index: " . json_encode($rowData));

                if (!isset($rowData['no']) || empty(trim($rowData['no']))) {
                    Log::warning("Baris $index tidak memiliki ID (no), dilewati.");
                    continue;
                }

                $id = (int)$rowData['no'];
                $sparepart = Spareparts::find($id) ?: new Spareparts();

                $attributes = [
                    'id' => $id,
                    'nama_part' => $rowData['nama_part'] ?? '',
                    'model' => $rowData['model'] ?? '',
                    'merk' => $rowData['merk'] ?? '',
                    'jumlah_baru' => $rowData['jumlah_baru'] ?? '',
                    'jumlah_bekas' => $rowData['jumlah_bekas'] ?? '',
                    'supplier' => $rowData['supplier'] ?? '',
                    'patokan_harga' => $rowData['_patokan_harga'] ?? '', // Tanpa normalisasi
                    'total' => $rowData['_total'] ?? '', // Tanpa normalisasi
                    'ruk_no' => $rowData['ruk_no'] ?? '',
                    'purchase_date' => $this->normalizeDate($rowData['purchase_date']) ?? '',
                    'delivery_date' => $this->normalizeDate($rowData['delivery_date']) ?? '',
                    'po_number' => $rowData['po_number'] ?? '',
                    'titik_pesanan' => $rowData['titik_pesanan'] ?? '',
                    'jumlah_pesanan' => $rowData['jumlah_pesanan'] ?? '',
                    'cek' => $rowData['cek'] ?? '',
                    'pic' => $rowData['pic'] ?? '',
                    'location' => $rowData['ruk_no'] ?? '', // Samakan location dengan ruk_no
                    'qr_code' => $sparepart->qr_code ?? '', // Pertahankan dari database
                ];

                $sparepart->fill($attributes);
                $sparepart->save();

                $savedSparepart = Spareparts::find($id);
                Log::info("Sinkronisasi baris $id selesai. Nilai disimpan: jumlah_baru={$savedSparepart->jumlah_baru}, jumlah_bekas={$savedSparepart->jumlah_bekas}, patokan_harga={$savedSparepart->patokan_harga}, total={$savedSparepart->total}");
            }

            return redirect()->route('spareparts.index')->with('success', 'Data dari Sheets berhasil disinkronkan.');
        } catch (\Exception $e) {
            Log::error('Error di syncFromSheets: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('spareparts.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function syncAllToSheets($sparepartId = null)
    {
        $tokenPath = storage_path('app/private/token.json');
        if (!file_exists($tokenPath) || !$this->client->getAccessToken()) {
            Log::warning('Token tidak ditemukan, redirect ke autentikasi.');
            return redirect($this->client->createAuthUrl())->with('error', 'Autentikasi diperlukan.');
        }

        if ($this->client->isAccessTokenExpired()) {
            if (isset($this->client->getAccessToken()['refresh_token'])) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getAccessToken()['refresh_token']);
                $this->client->setAccessToken($this->client->getAccessToken());
                file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
                Log::info('Token refreshed for syncAllToSheets.');
            } else {
                return redirect($this->client->createAuthUrl())->with('error', 'Token expired, autentikasi ulang diperlukan.');
            }
        }

        try {
            $service = new Sheets($this->client);
            $spreadsheetId = '1Ea3KwiD9sK9wyVZeodX1VaSfeCHd9vhVG7TCaJSZNOw';

            if ($sparepartId) {
                $sparepart = Spareparts::find($sparepartId);
                if (!$sparepart) {
                    Log::warning("Sparepart dengan ID $sparepartId tidak ditemukan.");
                    return redirect()->route('spareparts.index')->with('error', 'Sparepart tidak ditemukan.');
                }

                $rowData = [
                    $sparepart->id,
                    $sparepart->nama_part,
                    $sparepart->model,
                    $sparepart->merk,
                    $sparepart->jumlah_baru,
                    $sparepart->jumlah_bekas,
                    $sparepart->supplier,
                    $sparepart->patokan_harga,
                    $sparepart->total,
                    $sparepart->ruk_no,
                    $sparepart->purchase_date ? $this->normalizeDate($sparepart->purchase_date) : '',
                    $sparepart->delivery_date ? $this->normalizeDate($sparepart->delivery_date) : '',
                    $sparepart->po_number,
                    $sparepart->titik_pesanan,
                    $sparepart->jumlah_pesanan,
                    $sparepart->cek ? '1' : '0',
                    $sparepart->pic,
                ];

                $valuesToUpdate = [$rowData];
                $startRow = $sparepart->id + 1; // +1 karena baris 1 adalah header
                $rangeUpdate = '予備品リストTANAOROSI!A' . $startRow . ':Q' . $startRow;
                $body = new ValueRange(['values' => $valuesToUpdate]);
                $params = ['valueInputOption' => 'RAW'];
                $response = $service->spreadsheets_values->update(
                    $spreadsheetId,
                    $rangeUpdate,
                    $body,
                    $params
                );
                Log::info('Data untuk ID ' . $sparepart->id . ' berhasil diupdate ke Sheets.', [
                    'updated_rows' => 1,
                    'range' => $rangeUpdate,
                    'updated_cells' => $response->getUpdatedCells()
                ]);
            } else {
                $sparepartsFromDb = Spareparts::all();
                $valuesToUpdate = $sparepartsFromDb->map(function ($sparepart) {
                    return [
                        $sparepart->id,
                        $sparepart->nama_part,
                        $sparepart->model,
                        $sparepart->merk,
                        $sparepart->jumlah_baru,
                        $sparepart->jumlah_bekas,
                        $sparepart->supplier,
                        $sparepart->patokan_harga,
                        $sparepart->total,
                        $sparepart->ruk_no,
                        $sparepart->purchase_date ? $this->normalizeDate($sparepart->purchase_date) : '',
                        $sparepart->delivery_date ? $this->normalizeDate($sparepart->delivery_date) : '',
                        $sparepart->po_number,
                        $sparepart->titik_pesanan,
                        $sparepart->jumlah_pesanan,
                        $sparepart->cek ? '1' : '0',
                        $sparepart->pic,
                    ];
                })->toArray();

                if (!empty($valuesToUpdate)) {
                    $startRow = 2; // +1 karena baris 1 adalah header
                    $endRow = $startRow + count($valuesToUpdate) - 1;
                    $rangeUpdate = '予備品リストTANAOROSI!A' . $startRow . ':Q' . $endRow;
                    $body = new ValueRange(['values' => $valuesToUpdate]);
                    $params = ['valueInputOption' => 'RAW'];
                    $response = $service->spreadsheets_values->update(
                        $spreadsheetId,
                        $rangeUpdate,
                        $body,
                        $params
                    );
                    Log::info('Data berhasil diupdate ke Sheets.', [
                        'updated_rows' => count($valuesToUpdate),
                        'range' => $rangeUpdate,
                        'updated_cells' => $response->getUpdatedCells()
                    ]);
                } else {
                    Log::warning('Tidak ada data untuk disinkronkan ke Sheets.');
                }
            }

            return redirect()->route('spareparts.index')->with('success', 'Data dari DB berhasil disinkronkan ke Sheets.');
        } catch (\Exception $e) {
            Log::error('Error di syncAllToSheets: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('spareparts.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function syncPartialToSheets($sparepartId, array $changedFields = [])
    {
        $tokenPath = storage_path('app/private/token.json');
        if (!file_exists($tokenPath) || !$this->client->getAccessToken()) {
            Log::warning('Token tidak ditemukan, redirect ke autentikasi.');
            return redirect($this->client->createAuthUrl())->with('error', 'Autentikasi diperlukan.');
        }

        if ($this->client->isAccessTokenExpired()) {
            if (isset($this->client->getAccessToken()['refresh_token'])) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getAccessToken()['refresh_token']);
                $this->client->setAccessToken($this->client->getAccessToken());
                file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
                Log::info('Token refreshed for syncPartialToSheets.');
            } else {
                return redirect($this->client->createAuthUrl())->with('error', 'Token expired, autentikasi ulang diperlukan.');
            }
        }

        try {
            $service = new Sheets($this->client);
            $spreadsheetId = '1Ea3KwiD9sK9wyVZeodX1VaSfeCHd9vhVG7TCaJSZNOw';

            $sparepart = Spareparts::find($sparepartId);
            if (!$sparepart) {
                Log::warning("Sparepart dengan ID $sparepartId tidak ditemukan.");
                return redirect()->route('spareparts.index')->with('error', 'Sparepart tidak ditemukan.');
            }

            $fieldMap = [
                'nama_part' => 1,
                'model' => 2,
                'merk' => 3,
                'jumlah_baru' => 4,
                'jumlah_bekas' => 5,
                'supplier' => 6,
                'patokan_harga' => 7,
                'total' => 8,
                'ruk_no' => 9,
                'purchase_date' => 10,
                'delivery_date' => 11,
                'po_number' => 12,
                'titik_pesanan' => 13,
                'jumlah_pesanan' => 14,
                'cek' => 15,
                'pic' => 16,
                'deleted' => null, // Tidak ada kolom untuk deleted
            ];

            $rowData = [
                $sparepart->id,
                $sparepart->nama_part,
                $sparepart->model,
                $sparepart->merk,
                $sparepart->jumlah_baru,
                $sparepart->jumlah_bekas,
                $sparepart->supplier,
                $sparepart->patokan_harga,
                $sparepart->total,
                $sparepart->ruk_no,
                $sparepart->purchase_date ? $this->normalizeDate($sparepart->purchase_date) : '',
                $sparepart->delivery_date ? $this->normalizeDate($sparepart->delivery_date) : '',
                $sparepart->po_number,
                $sparepart->titik_pesanan,
                $sparepart->jumlah_pesanan,
                $sparepart->cek ? '1' : '0',
                $sparepart->pic,
            ];

            $valuesToUpdate = [array_fill(0, 17, '')]; // Inisialisasi dengan array kosong 17 elemen
            if (!empty($changedFields)) {
                $validChangedFields = array_intersect_key($changedFields, $fieldMap);
                foreach ($validChangedFields as $field => $value) {
                    if (isset($fieldMap[$field])) {
                        $colIndex = $fieldMap[$field] - 1; // Ubah ke indeks berbasis 0
                        if ($colIndex !== null && $colIndex < 17) {
                            $valuesToUpdate[0][$colIndex] = $rowData[$colIndex];
                        }
                    }
                }
                if (isset($changedFields['deleted']) && $changedFields['deleted'] === true) {
                    $valuesToUpdate[0] = array_fill(0, 17, ''); // Kosongkan semua kolom jika dihapus
                }
            }

            if (!empty($valuesToUpdate[0])) {
                $startRow = $sparepart->id + 1; // +1 karena baris 1 adalah header
                $rangeUpdate = '予備品リストTANAOROSI!A' . $startRow . ':Q' . $startRow;
                $body = new ValueRange(['values' => $valuesToUpdate]);
                $params = ['valueInputOption' => 'RAW'];
                $response = $service->spreadsheets_values->update(
                    $spreadsheetId,
                    $rangeUpdate,
                    $body,
                    $params
                );
                Log::info('Data untuk ID ' . $sparepart->id . ' berhasil diupdate ke Sheets (parsial).', [
                    'updated_rows' => 1,
                    'range' => $rangeUpdate,
                    'updated_cells' => $response->getUpdatedCells()
                ]);
            } else {
                Log::info('Tidak ada perubahan untuk ID ' . $sparepart->id . ' yang perlu disinkronkan.');
            }

            return redirect()->route('spareparts.index')->with('success', 'Data dari DB berhasil disinkronkan ke Sheets.');
        } catch (\Exception $e) {
            Log::error('Error di syncPartialToSheets: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('spareparts.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('code')) {
            $this->client->fetchAccessTokenWithAuthCode($request->code);
            $token = $this->client->getAccessToken();
            $tokenPath = storage_path('app/private/token.json');
            if (!file_exists(dirname($tokenPath))) {
                Storage::makeDirectory(dirname($tokenPath));
            }
            file_put_contents($tokenPath, json_encode($token));
            Log::info('Access token retrieved and saved to ' . $tokenPath);
            return redirect()->route('spareparts.index')->with('success', 'Authentication successful!');
        }
        return redirect()->route('spareparts.index')->with('error', 'Authentication failed.');
    }
}

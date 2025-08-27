<?php

namespace App\Http\Controllers;

use Google\Service\Drive;
use App\Models\Spareparts;
use Endroid\QrCode\QrCode;
use Google\Service\Sheets;
use Google\Service\Storage;
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
        Log::info('Generated Redirect URI: ' . $this->client->getRedirectUri());
        $this->client->setScopes([Drive::DRIVE, Sheets::SPREADSHEETS]);
        $clientSecretPath = storage_path('app/private/client_secret.json'); // pastikan nama file sesuai
        if (!file_exists($clientSecretPath)) {
            Log::error('File client_secret.json tidak ditemukan di: ' . $clientSecretPath);
            throw new \Exception('File client_secret.json tidak ditemukan.');
        }
        $this->client->setAuthConfig($clientSecretPath);
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');
        $this->client->setIncludeGrantedScopes(true);

        // Inisialisasi token tanpa redirect
        $tokenPath = storage_path('app/private/token.json');
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);
        }
    }

    /**
     * Normalize date format (support dd/mm/yyyy & yyyy-mm-dd).
     */
    protected function normalizeDate($date)
    {
        if (!$date) return null;
        $d = \DateTime::createFromFormat('d/m/Y', $date) ?: \DateTime::createFromFormat('Y-m-d', $date);
        return $d ? $d->format('Y-m-d') : null;
    }

    /**
     * Normalize number (remove Rp, space, comma).
     */
    protected function normalizeNumber($value)
    {
        if (!$value) return 0;
        return (float) str_replace(['Rp', 'rp', ',', ' '], '', $value);
    }

    public function index(Request $request)
    {
        $query = Spareparts::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_part', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('merk', 'like', "%{$search}%");
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

        $sparepart = Spareparts::create($validated);
        $this->generateQrCode($sparepart, $request->input('location', 'Unknown'));
        $this->syncToSheets(); // Sinkron ke Sheets

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
        $requestData = $request->all();
        $requestData['patokan_harga'] = $this->normalizeNumber($requestData['patokan_harga']);
        $requestData['total'] = $this->normalizeNumber($requestData['total']);

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
        $this->generateQrCode($sparepart, $request->input('location', 'Unknown'));
        $this->syncToSheets(); // Sinkron ke Sheets

        return redirect()->route('spareparts.index')->with('success', 'Sparepart updated successfully.');
    }

    public function destroy($id)
    {
        $sparepart = Spareparts::findOrFail($id);
        $sparepart->delete();
        $this->syncToSheets(); // Sinkron ke Sheets

        return redirect()->route('spareparts.index')->with('success', 'Sparepart deleted successfully.');
    }

    public function unduh(): BinaryFileResponse
    {
        $export = new SparepartExport();
        return Excel::download($export, 'spareparts.xlsx');
    }

    protected function generateQrCode(Spareparts $sparepart, string $location): void
    {
        $qrCodePath = 'qrcodes/sparepart_' . $sparepart->id . '_' . \Illuminate\Support\Str::slug($location) . '.png';
        $oldQrCodePath = $sparepart->qr_code;

        $qrCode = new QrCode(
            data: route('spareparts.show', $sparepart->id),
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
    /**
     * Sinkron dari Google Sheets ke DB.
     */
    public function syncFromSheets()
    {
        $tokenPath = storage_path('app/private/token.json');
        if (!file_exists($tokenPath) || !$this->client->getAccessToken()) {
            return redirect($this->client->createAuthUrl());
        }

        if ($this->client->isAccessTokenExpired()) {
            if (isset($this->client->getAccessToken()['refresh_token'])) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getAccessToken()['refresh_token']);
                $this->client->setAccessToken($this->client->getAccessToken());
                file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
            } else {
                return redirect($this->client->createAuthUrl());
            }
        }

        try {
            $service = new Sheets($this->client);
            $spreadsheetId = 'YOUR_SPREADSHEET_ID';
            $range = '予備品リストTANAOROSI!A1:Q';

            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $newValues = $response->getValues() ?: [];

            if (empty($newValues)) {
                return redirect()->route('spareparts.index')->with('warning', 'Sheet kosong atau range tidak valid.');
            }

            $headers = array_map(fn($h) => strtolower(trim(preg_replace('/\s+/', '_', $h))), $newValues[0]);
            $dataRows = array_slice($newValues, 1);

            foreach ($dataRows as $row) {
                if (empty(array_filter($row, 'trim'))) continue;

                $row = array_pad($row, count($headers), '');
                $rowData = array_combine($headers, $row);

                if (!isset($rowData['no']) || empty(trim($rowData['no']))) continue;

                $id = (int)$rowData['no'];
                $sparepart = Spareparts::find($id) ?: new Spareparts();
                $sparepart->id = $id;
                $sparepart->nama_part = $rowData['nama_part'] ?? '';
                $sparepart->model = $rowData['model'] ?? '';
                $sparepart->merk = $rowData['merk'] ?? '';
                $sparepart->jumlah_baru = (int)($rowData['jumlah_baru'] ?? 0);
                $sparepart->jumlah_bekas = (int)($rowData['jumlah_bekas'] ?? 0);
                $sparepart->supplier = $rowData['supplier'] ?? '';
                $sparepart->patokan_harga = $this->normalizeNumber($rowData['patokan_harga'] ?? 0);
                $sparepart->total = $this->normalizeNumber($rowData['total'] ?? 0);
                $sparepart->ruk_no = $rowData['ruk_no'] ?? '';
                $sparepart->purchase_date = $this->normalizeDate($rowData['purchase_date'] ?? null);
                $sparepart->delivery_date = $this->normalizeDate($rowData['delivery_date'] ?? null);
                $sparepart->po_number = $rowData['po_number'] ?? '';
                $sparepart->titik_pesanan = $rowData['titik_pesanan'] ?? '';
                $sparepart->jumlah_pesanan = (int)($rowData['jumlah_pesanan'] ?? 0);
                $sparepart->cek = filter_var($rowData['cek'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $sparepart->pic = $rowData['pic'] ?? '';
                $sparepart->location = $rowData['location'] ?? '';
                $sparepart->qr_code = $rowData['qr_code'] ?? '';

                $sparepart->save();
            }

            return redirect()->route('spareparts.index')->with('success', 'Data dari Sheets berhasil disinkronkan.');
        } catch (\Exception $e) {
            return redirect()->route('spareparts.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Sinkron dari DB ke Google Sheets.
     */
    public function syncToSheets()
    {
        $tokenPath = storage_path('app/private/token.json');
        if (!file_exists($tokenPath) || !$this->client->getAccessToken()) {
            return redirect($this->client->createAuthUrl());
        }

        if ($this->client->isAccessTokenExpired()) {
            if (isset($this->client->getAccessToken()['refresh_token'])) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getAccessToken()['refresh_token']);
                $this->client->setAccessToken($this->client->getAccessToken());
                file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
            } else {
                return redirect($this->client->createAuthUrl());
            }
        }

        try {
            $service = new Sheets($this->client);
            $spreadsheetId = 'YOUR_SPREADSHEET_ID';

            // Clear range before update
            $clearRange = '予備品リストTANAOROSI!A2:Q';
            $service->spreadsheets_values->clear($spreadsheetId, $clearRange, new \Google\Service\Sheets\ClearValuesRequest());

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
                    $sparepart->purchase_date,
                    $sparepart->delivery_date,
                    $sparepart->po_number,
                    $sparepart->titik_pesanan,
                    $sparepart->jumlah_pesanan,
                    $sparepart->cek ? '1' : '0',
                    $sparepart->pic,
                ];
            })->toArray();

            if (!empty($valuesToUpdate)) {
                $body = new ValueRange(['values' => $valuesToUpdate]);
                $params = ['valueInputOption' => 'RAW'];
                $service->spreadsheets_values->update(
                    $spreadsheetId,
                    '予備品リストTANAOROSI!A2',
                    $body,
                    $params
                );
            }

            return redirect()->route('spareparts.index')->with('success', 'Data dari DB berhasil disinkronkan ke Sheets.');
        } catch (\Exception $e) {
            return redirect()->route('spareparts.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('code')) {
            $this->client->fetchAccessTokenWithAuthCode($request->code);
            $token = $this->client->getAccessToken();
            file_put_contents(storage_path('app/private/token.json'), json_encode($token));
            return redirect()->route('spareparts.index')->with('success', 'Authentication successful!');
        }
        return redirect()->route('spareparts.index')->with('error', 'Authentication failed.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Spareparts;
use Google\Client as Google_Client;
use Google\Service\Drive;
use Google\Service\Sheets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Google\Service\Sheets\ValueRange;

class GoogleController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('Tanaoroshi Sync');
        $this->client->setRedirectUri('http://127.0.0.1:8000/google/callback'); // Sesuaikan dengan credentials.json
        $redirectUri = $this->client->getRedirectUri();
        Log::info('Generated Redirect URI: ' . $redirectUri);
        $this->client->setScopes([Drive::DRIVE, Sheets::SPREADSHEETS]);
        $this->client->setAuthConfig(Storage::path('client_secret_232740761211-85huc3q6vp6l074f1lch1e8vkevtnd84.apps.googleusercontent.com.json'));
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');
        $this->client->setIncludeGrantedScopes(true);
    }

    public function auth()
    {
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {
                $authUrl = $this->client->createAuthUrl();
                return redirect($authUrl);
            }
        }

        if ($this->client->getAccessToken()) {
            Storage::put('token.json', json_encode($this->client->getAccessToken()));
            Log::info('Token saved successfully.');
            return redirect('/')->with('success', 'Authentication successful!');
        }

        return 'Authentication in progress...';
    }

    public function callback(Request $request)
    {
        if ($request->has('code')) {
            $token = $this->client->fetchAccessTokenWithAuthCode($request->code);
            $this->client->setAccessToken($token);

            if ($this->client->getAccessToken()) {
                Storage::put('token.json', json_encode($this->client->getAccessToken()));
                Log::info('Access token retrieved and saved.');
                return redirect('/')->with('success', 'Authenticated with Google Drive!');
            }
        }

        return redirect('/')->with('error', 'Authentication failed.');
    }
    public function SyncToDrive()
    {
        // Debugging: Cek apakah token ada
        $token = json_decode(file_get_contents(storage_path('app/token.json')), true);
        if (!$token) {
            Log::error('Token tidak ditemukan di storage_path/app/token.json');
            return "Error: Token tidak ditemukan.";
        }
        Log::info('Token ditemukan, memulai koneksi ke Google Sheets.');

        // Inisialisasi klien Google
        $client = new Google_Client();
        $client->setAccessToken($token);

        // Debugging: Cek status token
        if ($client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                $client->setAccessToken($this->client->getAccessToken());
            } else {
                return "Error: Token expired dan tidak ada refresh token, autentikasi ulang diperlukan.";
            }
        }

        // Inisialisasi layanan Sheets
        $service = new Sheets($client);

        // ID spreadsheet dari link baru
        $spreadsheetId = '1Ea3KwiD9sK9wyVZeodX1VaSfeCHd9vhVG7TCaJSZNOw';
        $range = '予備品リストTANAOROSI!A1:Q2570'; // Sesuaikan dengan range yang kamu berikan

        try {
            // Debugging: Coba baca data dari sheet
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();
            Log::info('Data berhasil dibaca dari sheet.', ['row_count' => count($values), 'sample' => array_slice($values, 0, 5)]);

            if (empty($values)) {
                Log::warning('Sheet kosong atau range tidak valid.');
                return "Peringatan: Sheet kosong atau range tidak valid.";
            }

            // Opsional: Update data untuk testing
            $spareparts = Spareparts::all();
            $valuesToUpdate = $spareparts->map(function ($sparepart) {
                return [
                    $sparepart->nama_part,
                    $sparepart->model,
                    $sparepart->merk,
                    $sparepart->jumlah_baru,
                    $sparepart->jumlah_bekas,
                    $sparepart->supplier,
                    $sparepart->patokan_harga,
                    $sparepart->ruk_no,
                    $sparepart->purchase_date,
                    $sparepart->delivery_date,
                    $sparepart->po_number,
                    $sparepart->titik_pesanan,
                    $sparepart->jumlah_pesanan,
                    $sparepart->cek,
                    $sparepart->pic,
                    // Kolom P dan Q bisa dikosongkan jika belum ada data
                    null,
                    null,
                ];
            })->toArray();

            $body = new ValueRange(['values' => $valuesToUpdate]);
            $service->spreadsheets_values->update($spreadsheetId, '予備品リストTANAOROSI!A2:Q2570', $body, ['valueInputOption' => 'RAW']);
            Log::info('Data berhasil diupdate ke sheet.', ['updated_rows' => count($valuesToUpdate)]);

            return "Sukses: Data dibaca dan diupdate ke Google Drive!";
        } catch (\Exception $e) {
            Log::error('Error saat koneksi ke Google Sheets: ' . $e->getMessage());
            return "Error: " . $e->getMessage();
        }
    }
}

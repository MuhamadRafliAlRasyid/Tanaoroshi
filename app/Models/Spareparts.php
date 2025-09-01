<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Spareparts extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'spareparts';

    protected $fillable = [
        'nama_part',
        'model',
        'merk',
        'jumlah_baru',
        'jumlah_bekas',
        'supplier',
        'patokan_harga',
        'total',
        'ruk_no',
        'purchase_date',
        'delivery_date',
        'po_number',
        'titik_pesanan',
        'jumlah_pesanan',
        'cek',
        'pic',
        'qr_code',
        'location',
    ];

    // protected $casts = [
    //     'jumlah_baru' => 'integer',
    //     'jumlah_bekas' => 'integer',
    //     'patokan_harga' => 'float',
    //     'total' => 'float',
    //     'jumlah_pesanan' => 'integer',
    //     'cek' => 'boolean',
    //     'purchase_date' => 'date',
    //     'delivery_date' => 'date',
    // ];

    public function setJumlahBaruAttribute($value)
    {
        $this->attributes['jumlah_baru'] = (int)$this->normalizeNumber($value);
    }

    public function setJumlahBekasAttribute($value)
    {
        $this->attributes['jumlah_bekas'] = (int)$this->normalizeNumber($value);
    }

    public function setPatokanHargaAttribute($value)
    {
        $this->attributes['patokan_harga'] = (float)$this->normalizeNumber($value);
    }

    public function setTotalAttribute($value)
    {
        $this->attributes['total'] = (float)$this->normalizeNumber($value);
    }

    public function setJumlahPesananAttribute($value)
    {
        $this->attributes['jumlah_pesanan'] = (int)$this->normalizeNumber($value);
    }

    public function setPurchaseDateAttribute($value)
    {
        $this->attributes['purchase_date'] = $this->normalizeDate($value);
    }

    public function setDeliveryDateAttribute($value)
    {
        $this->attributes['delivery_date'] = $this->normalizeDate($value);
    }

    public function setCekAttribute($value)
    {
        $this->attributes['cek'] = $this->parseBool($value);
    }

    private function normalizeNumber($value): string
    {
        if ($value === null || trim((string)$value) === '') {
            return '0';
        }
        $original = trim((string)$value);
        Log::debug("Normalizing number: original=$original");

        // Konversi full-width ke half-width (misalnya, "１" menjadi "1")
        $cleaned = preg_replace_callback('/[\xEF\xBC\x90-\xEF\xBC\x99]/u', function ($match) {
            return mb_convert_kana($match[0], 'n');
        }, $original);

        // Hapus "Rp", spasi, dan simbol lain
        $cleaned = preg_replace('/[^0-9,.]/', '', $cleaned);
        if (empty($cleaned)) {
            Log::warning("Nilai kosong setelah pembersihan: {$original}");
            return (string)$original;
        }

        $hasComma = strpos($cleaned, ',') !== false;
        $hasDot = strpos($cleaned, '.') !== false;
        if ($hasComma && $hasDot) {
            if (strpos($cleaned, ',') > strpos($cleaned, '.')) {
                $cleaned = str_replace('.', '', $cleaned);
                $cleaned = str_replace(',', '.', $cleaned);
            } else {
                $cleaned = str_replace(',', '', $cleaned);
            }
        } elseif ($hasComma) {
            $cleaned = str_replace('.', '', $cleaned);
            $cleaned = str_replace(',', '.', $cleaned);
        } elseif ($hasDot) {
            $cleaned = str_replace(',', '', $cleaned);
        }

        if (!is_numeric($cleaned)) {
            Log::warning("Nilai tidak valid untuk konversi numerik: {$original} -> {$cleaned}");
            return (string)$original;
        }

        $floatValue = (float)$cleaned;
        return number_format($floatValue, 2, '.', '');
    }

    private function normalizeDate($value): ?string
    {
        if (!$value || trim($value) === '') {
            return null;
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        $formats = ['d/m/Y', 'm/d/Y', 'Y-m-d', 'd-m-Y', 'd-M-yy', 'd-M-yyyy'];
        foreach ($formats as $format) {
            $d = \DateTime::createFromFormat($format, $value);
            if ($d && $d->format($format) === $value) {
                return $d->format('Y-m-d');
            }
        }
        Log::warning("Tanggal tidak valid: {$value}. Disimpan sebagai string.");
        return (string)$value;
    }

    private function parseBool($value): string
    {
        $v = strtolower(trim((string)$value));
        return in_array($v, ['1', 'true', 'ya', 'yes', 'y', 'on'], true) ? '1' : '0';
    }

    public function pengambilanBarangs()
    {
        return $this->hasMany(PengambilanSparepart::class);
    }
}

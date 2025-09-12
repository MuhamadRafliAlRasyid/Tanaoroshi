<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Spareparts extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

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



    // Setter methods (sudah ada, tidak perlu diubah)
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

    // Normalization methods (tidak diubah)
    private function normalizeNumber($value)
    { /* ... */
    }
    private function normalizeDate($value)
    { /* ... */
    }
    private function parseBool($value)
    { /* ... */
    }

    public function pengambilanBarangs()
    {
        return $this->hasMany(PengambilanSparepart::class);
    }
}

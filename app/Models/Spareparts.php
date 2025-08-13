<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Spareparts extends Model
{
    use HasFactory;

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
        'qr_code'
    ];

    public function pengambilanBarangs()
    {
        return $this->hasMany(PengambilanSparepart::class);
    }
}

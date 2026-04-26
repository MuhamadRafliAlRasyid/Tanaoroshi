<?php

namespace App\Models;

use App\Models\Traits\HasHashId;
use Illuminate\Database\Eloquent\Model;

class PengembalianAlat extends Model

{
    use HasHashId;

    protected $appends = ['hashid'];
    protected $fillable = [
        'pengambilan_alat_id',
        'user_id',
        'jumlah',
        'tanggal_pengembalian',
        'keterangan',
    ];

    public function pengambilan()
    {
        return $this->belongsTo(PengambilanAlat::class, 'pengambilan_alat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

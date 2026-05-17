<?php

namespace App\Models;

use App\Models\Traits\HasHashId;
use Illuminate\Database\Eloquent\Model;

class PengembalianAlat extends Model
{
    use HasHashId;

    protected $table = 'pengembalian_alats';

    protected $appends = ['hashid'];

    protected $fillable = [
        'pengambilan_alat_id',
        'user_id',
        'nama_peminjam',
        'jumlah',
        'tanggal_pengembalian',
        'keterangan',
        'foto',
    ];

    public function pengambilan()
    {
        return $this->belongsTo(PengambilanAlat::class, 'pengambilan_alat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFotoThumbAttribute()
    {
        if (!$this->foto) return null;
        return asset('storage/pengembalian/thumb/' . $this->foto);
    }

    public function getFotoUrlAttribute()
    {
        if (!$this->foto) return null;
        return asset('storage/pengembalian/' . $this->foto);
    }
}

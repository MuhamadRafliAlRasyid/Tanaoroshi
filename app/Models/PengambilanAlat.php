<?php

namespace App\Models;

use App\Models\Traits\HasHashId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengambilanAlat extends Model
{
    use HasFactory, HasHashId;

    protected $table = 'pengambilan_alats';

    protected $appends = ['hashid'];

    protected $fillable = [
        'user_id',
        'bagian_id',
        'alat_id',
        'jumlah',
        'satuan',
        'keperluan',
        'waktu_pengambilan',
        'status',
    ];

    protected $casts = [
        'waktu_pengambilan' => 'datetime',
    ];

    /* ================= RELASI ================= */

    public function alat()
    {
        return $this->belongsTo(Alat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bagian()
    {
        return $this->belongsTo(Bagian::class);
    }

    /* ================= ACCESSOR ================= */

    // 🔥 Label alat biar rapi di view
    public function getAlatLabelAttribute()
    {
        if (!$this->alat) return '-';

        return "{$this->alat->nama_alat} | {$this->alat->merk} | {$this->alat->tipe} | {$this->alat->no_seri}";
    }

    // 🔥 Status badge (optional UI helper)
    public function getStatusLabelAttribute()
    {
        return $this->status === 'dipinjam'
            ? 'Dipinjam'
            : 'Dikembalikan';
    }
    public function pengembalians()
{
    return $this->hasMany(PengembalianAlat::class);
}
}

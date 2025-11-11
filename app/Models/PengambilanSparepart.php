<?php

namespace App\Models;

use App\Models\Traits\HasHashId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengambilanSparepart extends Model
{
    use HasFactory;
    use HasHashId;

    protected $table = 'pengambilan_spareparts';
    protected $fillable = [
        'user_id',
        'bagian_id',
        'spareparts_id',
        'part_type',
        'jumlah',
        'satuan',
        'keperluan',
        'waktu_pengambilan'
    ];

public function scopeFindByHashidOrFail($query, $hashid)
{
    $decoded = app(\App\Services\HashIdService::class)->decode($hashid);
    if (!$decoded) abort(404);

    return $query->findOrFail($decoded);
}
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bagian()
    {
        return $this->belongsTo(Bagian::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Spareparts::class, 'spareparts_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengambilanSparepart extends Model
{
    use HasFactory;

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

<?php

namespace App\Models;

use App\Models\Traits\HasHashId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alat extends Model
{
    use HasFactory, SoftDeletes, HasHashId;

    protected $fillable = [
        'nama_alat',
        'kelas',
        'merk',
        'tipe',
        'no_seri',
        'no_identitas',
        'kapasitas',
        'daya_baca',
        'jumlah',
        'no_sertifikat',
        'masa_berlaku',
        'kategori_id',
        'qr_code',
        'foto',
        'last_notified_at',
    ];

    protected $appends = ['hashid', 'status'];

    protected $casts = [
        'masa_berlaku' => 'date',
    ];

    // Relasi
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function pengambilan()
    {
        return $this->hasMany(PengambilanAlat::class);
    }

    public function pengembalian()
    {
        return $this->hasMany(PengembalianAlat::class);
    }

    public function kalibrasis()
    {
        return $this->hasMany(KalibrasiAlat::class);
    }

    // Accessor
    public function getLabelAttribute()
    {
        return "{$this->nama_alat} | {$this->merk} | {$this->tipe} | {$this->no_seri}";
    }

    public function getStatusAttribute()
    {
        if (!$this->masa_berlaku) return 'unknown';
        if ($this->masa_berlaku < now()) return 'expired';
        if ($this->masa_berlaku <= now()->addDays(7)) return 'warning';
        return 'ok';
    }

    // ✅ Accessor thumbnail (sudah diperbaiki)
    public function getFotoThumbAttribute()
    {
        if (!$this->foto) return null;
        return asset('storage/alat/thumb/' . $this->foto);
    }

    // ✅ Accessor foto asli
    public function getFotoUrlAttribute()
    {
        if (!$this->foto) return null;
        return asset('storage/alat/' . $this->foto);
    }

    // Scope
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama_alat', 'like', "%$search%")
              ->orWhere('merk', 'like', "%$search%")
              ->orWhere('tipe', 'like', "%$search%")
              ->orWhere('no_seri', 'like', "%$search%")
              ->orWhere('no_identitas', 'like', "%$search%");
        });
    }
}

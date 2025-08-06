<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class purchase_requests extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_part',
        'part_number',
        'link_website',
        'waktu_request',
        'quantity',
        'satuan',
        'mas_deliver',
        'untuk_apa',
        'pic',
        'quotation_lead_time',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(requestlogs::class);
    }
}

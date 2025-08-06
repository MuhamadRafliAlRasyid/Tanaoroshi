<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestLogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_requests_id',
        'approved_by',
        'action',
        'notes'
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(purchase_requests::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

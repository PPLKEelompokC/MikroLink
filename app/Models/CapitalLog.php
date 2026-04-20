<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapitalLog extends Model
{
    protected $fillable = [
        'koperasi_id',
        'transaction_id',
        'type',
        'amount',
        'status',
        'progress',
        'member_name',
    ];

    public function koperasi()
    {
        return $this->belongsTo(Koperasi::class, 'koperasi_id', 'id_koperasi');
    }
}

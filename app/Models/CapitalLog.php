<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class CapitalLog extends Model
{
    use Auditable;

    protected $fillable = [
        'koperasi_id',
        'user_id',
        'transaction_id',
        'type',
        'transaction_type',
        'amount',
        'status',
        'progress',
        'member_name',
    ];

    public function koperasi()
    {
        return $this->belongsTo(Koperasi::class, 'koperasi_id', 'id_koperasi');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

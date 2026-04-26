<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustMetric extends Model
{
    protected $fillable = [
        'user_id',
        'score',
        'participation_score',
        'integrity_score',
        'reliability_score',
        'final_index',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

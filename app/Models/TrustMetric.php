<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrustMetric extends Model
{
    protected $fillable = [
        'user_id',
        'participation_score',
        'integrity_score',
        'reliability_score',
        'final_index',
        'notes',
    ];

    /**
     * Get the user that owns the trust metric.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate and save the final index based on weighted scores.
     * Weights: Participation (40%), Integrity (40%), Reliability (20%).
     */
    public function calculateFinalIndex(): float
    {
        $this->final_index = ($this->participation_score * 0.4) + 
                             ($this->integrity_score * 0.4) + 
                             ($this->reliability_score * 0.2);
        
        return $this->final_index;
    }

    /**
     * Boot the model and add saving listener to calculate final_index.
     */
    protected static function booted()
    {
        static::saving(function ($trustMetric) {
            $trustMetric->calculateFinalIndex();
        });
    }
}

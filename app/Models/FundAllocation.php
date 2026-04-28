<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundAllocation extends Model
{
    protected $fillable = [
        'koperasi_id',
        'snapshot_id',
        'recommended_amount',
        'allocation_category',
        'confidence_score',
        'reasoning',
        'ai_model_used',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'recommended_amount' => 'decimal:2',
            'confidence_score' => 'decimal:2',
            'reviewed_at' => 'datetime',
        ];
    }

    public function koperasi(): BelongsTo
    {
        return $this->belongsTo(Koperasi::class, 'koperasi_id', 'id_koperasi');
    }

    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(IdleFundSnapshot::class, 'snapshot_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

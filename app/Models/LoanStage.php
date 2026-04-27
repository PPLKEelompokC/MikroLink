<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanStage extends Model
{
    protected $fillable = [
        'loan_id',
        'stage_order',
        'stage_name',
        'completed',
        'completed_at',
        'completed_by_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'completed'    => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IdleFundSnapshot extends Model
{
    protected $fillable = [
        'koperasi_id',
        'snapshot_date',
        'total_cash_balance',
        'total_outstanding_loans',
        'total_pending_deposits',
        'operational_reserve',
        'idle_fund_amount',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'snapshot_date' => 'date',
            'total_cash_balance' => 'decimal:2',
            'total_outstanding_loans' => 'decimal:2',
            'total_pending_deposits' => 'decimal:2',
            'operational_reserve' => 'decimal:2',
            'idle_fund_amount' => 'decimal:2',
        ];
    }

    public function koperasi(): BelongsTo
    {
        return $this->belongsTo(Koperasi::class, 'koperasi_id', 'id_koperasi');
    }

    public function fundAllocations(): HasMany
    {
        return $this->hasMany(FundAllocation::class, 'snapshot_id');
    }
}

<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyWelfareLog extends Model
{
    use Auditable;

    protected $fillable = [
        'user_id',
        'period_date',
        'income_before',
        'income_after',
        'dependents_count',
        'food_security_status',
        'education_access_status',
        'health_access_status',
        'welfare_score',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'period_date' => 'date',
            'income_before' => 'decimal:2',
            'income_after' => 'decimal:2',
            'dependents_count' => 'integer',
            'welfare_score' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incomeGrowthPercentage(): float
    {
        $incomeBefore = (float) $this->income_before;

        if ($incomeBefore <= 0) {
            return (float) $this->income_after > 0 ? 100.0 : 0.0;
        }

        return round((((float) $this->income_after - $incomeBefore) / $incomeBefore) * 100, 1);
    }
}

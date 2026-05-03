<?php

namespace App\Models;

use App\Traits\Auditable;
use Database\Factories\LoanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use Auditable;

    /** @use HasFactory<LoanFactory> */
    use HasFactory;

    protected $fillable = [
        'loan_id_number',
        'user_id',
        'type',
        'amount',
        'duration',
        'reason',
        'status',
        'progress_percentage',
        'notes',
        'disbursed_at',
        'reviewed_by_admin',
        'admin_note',
        'admin_reviewed_at',
        'reviewed_by_manajer',
        'manajer_note',
        'manajer_reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'              => 'decimal:2',
            'progress_percentage' => 'integer',
            'disbursed_at'        => 'datetime',
            'admin_reviewed_at'   => 'datetime',
            'manajer_reviewed_at' => 'datetime',
        ];
    }

    /**
     * Auto-generate unique loan_id_number on creation.
     */
    protected static function booted(): void
    {
        static::creating(function (Loan $loan): void {
            if (empty($loan->loan_id_number)) {
                $year = now()->year;
                $count = static::whereYear('created_at', $year)->count() + 1;
                $loan->loan_id_number = sprintf('PN-%s-%03d', $year, $count);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(LoanStage::class)->orderBy('stage_order');
    }

    // ✅ Relasi workflow berjenjang
    public function adminReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_admin');
    }

    public function manajerReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_manajer');
    }

    /**
     * Recalculate progress_percentage from completed stages and update status.
     */
    public function recalculateProgress(): void
    {
        $total = $this->stages()->count();
        $completed = $this->stages()->where('completed', true)->count();

        $percentage = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

        $status = $this->status;
        if ($completed === 0) {
            $status = 'Baru';
        } elseif ($completed >= $total) {
            $status = 'Disetujui';
        } else {
            $status = 'Dalam Review';
        }

        $this->update([
            'progress_percentage' => $percentage,
            'status' => $status,
            'disbursed_at' => $completed >= $total ? ($this->disbursed_at ?? now()) : null,
        ]);
    }
}
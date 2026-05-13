<?php

namespace App\Models;

use App\Casts\MaskedEmail;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Auditable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class User extends Authenticatable // implements MustVerifyEmail
{
    use Auditable;

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'email' => MaskedEmail::class,
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Relasi ke semua setoran simpanan milik user ini
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * Helper: Total simpanan berdasarkan jenis dan status APPROVED
     */
    public function totalSimpanan(string $type): int
    {
        return $this->deposits()
            ->where('type', $type)
            ->where('status', 'APPROVED')
            ->sum('amount');
    }

    /**
     * Optimization: Ambil semua total simpanan dalam satu query
     */
    public function getAllSimpananTotals(): array
    {
        return $this->deposits()
            ->where('status', 'APPROVED')
            ->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();
    }

    /**
     * Get the trust metric associated with the user.
     */
    public function trustMetric()
    {
        return $this->hasOne(TrustMetric::class);
    }

    /**
     * Get all audit trails associated with the user.
     */
    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class);
    }

    /**
     * Relasi ke semua penarikan simpanan milik user ini
     */
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function familyWelfareLogs(): HasMany
    {
        return $this->hasMany(FamilyWelfareLog::class);
    }

    /**
     * Saldo simpanan sukarela yang tersedia untuk ditarik.
     * Mengurangi total deposit SUKARELA APPROVED dengan total penarikan APPROVED + PENDING.
     */
    public function availableSukarelaBalance(): float
    {
        $totalDeposit = $this->deposits()
            ->where('type', 'SUKARELA')
            ->where('status', 'APPROVED')
            ->sum('amount');

        $totalWithdrawn = $this->withdrawals()
            ->whereIn('status', ['APPROVED', 'PENDING'])
            ->sum('amount');

        return max(0, $totalDeposit - $totalWithdrawn);
    }
}

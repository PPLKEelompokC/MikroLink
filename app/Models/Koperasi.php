<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Koperasi extends Model
{
    use Auditable;

    protected $table = 'koperasi';

    protected $primaryKey = 'id_koperasi';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id_koperasi',
        'nama_koperasi',
        'alamat',
        'saldo_kas',
        'total_outstanding_loans',
    ];

    public function capitalLogs(): HasMany
    {
        return $this->hasMany(CapitalLog::class, 'koperasi_id', 'id_koperasi');
    }

    public function financialRecords(): HasMany
    {
        return $this->hasMany(FinancialRecord::class, 'koperasi_id', 'id_koperasi');
    }

    public function idleFundSnapshots(): HasMany
    {
        return $this->hasMany(IdleFundSnapshot::class, 'koperasi_id', 'id_koperasi');
    }

    public function fundAllocations(): HasMany
    {
        return $this->hasMany(FundAllocation::class, 'koperasi_id', 'id_koperasi');
    }

    public function updateSaldo(float $amount, string $type = 'hibah', ?string $memberName = null, ?int $userId = null, ?string $notes = null): void
    {
        $transactionType = $amount >= 0 ? 'deposit' : 'withdrawal';
        $absAmount = abs($amount);

        // Map legacy types if passed
        $typeMapping = [
            'Penyesuaian Modal' => 'penyesuaian_modal',
            'Simpanan' => 'simpanan_wajib',
            'Dana Darurat' => 'dana_cadangan',
            'Pinjaman Usaha' => 'pinjaman_usaha',
        ];
        $mappedType = $typeMapping[$type] ?? $type;

        app(\App\Services\KoperasiCapitalService::class)->processCapitalTransaction(
            $this,
            $userId,
            $absAmount,
            $mappedType,
            $transactionType,
            $memberName ?? 'Admin Koperasi',
            $notes
        );
    }

    public function cekLikuiditas(): float
    {
        // Simple logic for illustration based on class diagram, assuming likuiditas is a percentage.
        // In real scenario, it would depend on total assets/liabilities.
        // Here we just return a base value.
        return $this->saldo_kas > 0 ? 92.3 : 0.0;
    }
}

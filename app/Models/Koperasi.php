<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Koperasi extends Model
{
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

    public function updateSaldo(float $amount, string $type = 'Penyesuaian Modal', ?string $memberName = null): void
    {
        $this->saldo_kas += $amount;
        $this->save();

        $this->capitalLogs()->create([
            'transaction_id' => 'PN-'.date('Y').'-'.str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'type' => $type,
            'amount' => $amount,
            'status' => 'Disetujui',
            'progress' => 100,
            'member_name' => $memberName ?? 'Admin Koperasi',
        ]);
    }

    public function cekLikuiditas(): float
    {
        // Simple logic for illustration based on class diagram, assuming likuiditas is a percentage.
        // In real scenario, it would depend on total assets/liabilities.
        // Here we just return a base value.
        return $this->saldo_kas > 0 ? 92.3 : 0.0;
    }
}

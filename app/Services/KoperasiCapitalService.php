<?php

namespace App\Services;

use App\Models\CapitalLog;
use App\Models\Koperasi;
use Illuminate\Support\Str;

class KoperasiCapitalService
{
    /**
     * Process a capital transaction for a Koperasi.
     *
     * @param Koperasi $koperasi
     * @param int|null $userId
     * @param float $amount
     * @param string $type (simpanan_pokok, simpanan_wajib, simpanan_sukarela, etc.)
     * @param string $transactionType (deposit, withdrawal)
     * @param string|null $memberName Fallback for legacy support
     * @param string|null $notes Additional context or reason for the transaction
     * @return CapitalLog
     * @throws \Exception
     */
    public function processCapitalTransaction(
        Koperasi $koperasi,
        ?int $userId,
        float $amount,
        string $type,
        string $transactionType = 'deposit',
        ?string $memberName = null,
        ?string $notes = null
    ): CapitalLog {
        // Validation: Cannot withdraw simpanan_pokok or simpanan_wajib
        if ($transactionType === 'withdrawal') {
            if (in_array($type, ['simpanan_pokok', 'simpanan_wajib'])) {
                throw new \Exception("Cannot withdraw {$type} unless membership is terminated.");
            }

            // Validation: Check balance for simpanan_sukarela
            if ($type === 'simpanan_sukarela') {
                $availableBalance = $this->getAvailableSukarelaBalance($koperasi->id_koperasi, $userId);
                if ($amount > $availableBalance) {
                    throw new \Exception("Insufficient simpanan_sukarela balance. Available: {$availableBalance}, Requested: {$amount}");
                }
            }
        }

        // Create log
        $log = $koperasi->capitalLogs()->create([
            'user_id' => $userId,
            'transaction_id' => 'TRX-' . strtoupper(Str::random(10)),
            'type' => $type,
            'transaction_type' => $transactionType,
            'amount' => $amount,
            'status' => 'Selesai', // Assuming direct approval for this service
            'progress' => 100,
            'member_name' => $memberName,
            'notes' => $notes,
        ]);

        // Update Koperasi total kas
        $kasModifier = $transactionType === 'deposit' ? $amount : -$amount;
        $koperasi->increment('saldo_kas', $kasModifier);

        // Auto-generate Neraca for current month to keep it in sync
        app(\App\Services\NeracaKeuanganService::class)->generate($koperasi, now());

        return $log;
    }

    /**
     * Calculate available Simpanan Sukarela balance for a specific user in a Koperasi.
     */
    public function getAvailableSukarelaBalance(string $koperasiId, int $userId): float
    {
        $deposits = CapitalLog::where('koperasi_id', $koperasiId)
            ->where('user_id', $userId)
            ->where('type', 'simpanan_sukarela')
            ->where('transaction_type', 'deposit')
            ->where('status', 'Selesai')
            ->sum('amount');

        $withdrawals = CapitalLog::where('koperasi_id', $koperasiId)
            ->where('user_id', $userId)
            ->where('type', 'simpanan_sukarela')
            ->where('transaction_type', 'withdrawal')
            ->whereIn('status', ['Selesai', 'Dalam Review', 'Disetujui'])
            ->sum('amount');

        return max(0, $deposits - $withdrawals);
    }
}

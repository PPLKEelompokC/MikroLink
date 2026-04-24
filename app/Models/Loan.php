<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'duration',
        'interest_rate',
        'status',
        'reason',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Logika Validasi Pinjaman oleh Admin
     */
    public function approve(): bool
    {
        if ($this->status !== 'Pending') {
            return false;
        }

        $koperasi = Koperasi::where('id_koperasi', 'KOP-001')->first();

        // Cek apakah saldo kas koperasi mencukupi untuk meminjamkan uang
        if ($koperasi && $koperasi->saldo_kas >= $this->amount) {
            
            $this->update(['status' => 'Approved']);

            // Kurangi saldo kas (gunakan nilai negatif) dan buat log transaksi
            $koperasi->updateSaldo(
                -$this->amount, 
                "Pemberian Pinjaman kepada {$this->user->name}", 
                'Admin Koperasi'
            );

            return true;
        }

        return false;
    }
}
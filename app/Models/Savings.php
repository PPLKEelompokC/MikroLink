<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Savings extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'proof_path',
        'status',
        'admin_note',
    ];

    /**
     * Relasi ke Anggota (User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Logika Validasi Setoran
     * Menyetujui setoran dan otomatis menambah kas koperasi
     */
    public function approve(): bool
    {
        if ($this->status !== 'Pending') {
            return false;
        }

        // 1. Update status setoran
        $this->update(['status' => 'Approved']);

        // 2. Hubungkan ke Model Koperasi (ID KOP-001 sesuai KoperasiController Anda)
        $koperasi = Koperasi::where('id_koperasi', 'KOP-001')->first();

        if ($koperasi) {
            // 3. Panggil fungsi updateSaldo dari model Koperasi yang sudah Anda buat
            // Ini akan otomatis menambah saldo_kas dan mencatat di CapitalLog
            $koperasi->updateSaldo(
                (float) $this->amount, 
                "Setoran Simpanan {$this->type}", 
                $this->user->name
            );
            
            return true;
        }

        return false;
    }

    /**
     * Menolak setoran dengan catatan
     */
    public function reject(?string $note = null): void
    {
        $this->update([
            'status' => 'Rejected',
            'admin_note' => $note
        ]);
    }
}
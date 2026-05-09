<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NeracaKeuangan extends Model
{
    protected $table = 'neraca_keuangan';

    protected $fillable = [
        'koperasi_id',
        'periode',
        'periode_label',
        'kas',
        'simpanan_pokok',
        'simpanan_wajib',
        'simpanan_sukarela',
        'piutang_pinjaman',
        'total_aset',
        'kewajiban_tarik_pending',
        'total_kewajiban',
        'modal_disetor',
        'sisa_hasil_usaha',
        'total_ekuitas',
        'rasio_likuiditas',
        'rasio_kecukupan',
        'is_auto',
        'generated_by',
    ];

    protected function casts(): array
    {
        return [
            'periode'           => 'date',
            'is_auto'           => 'boolean',
            'kas'               => 'decimal:2',
            'simpanan_pokok'    => 'decimal:2',
            'simpanan_wajib'    => 'decimal:2',
            'simpanan_sukarela' => 'decimal:2',
            'piutang_pinjaman'  => 'decimal:2',
            'total_aset'        => 'decimal:2',
            'kewajiban_tarik_pending' => 'decimal:2',
            'total_kewajiban'   => 'decimal:2',
            'modal_disetor'     => 'decimal:2',
            'sisa_hasil_usaha'  => 'decimal:2',
            'total_ekuitas'     => 'decimal:2',
            'rasio_likuiditas'  => 'decimal:2',
            'rasio_kecukupan'   => 'decimal:2',
        ];
    }

    public function koperasi(): BelongsTo
    {
        return $this->belongsTo(Koperasi::class, 'koperasi_id', 'id_koperasi');
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /** Label status kesehatan berdasarkan rasio likuiditas */
    public function statusKesehatan(): string
    {
        if ($this->rasio_likuiditas >= 150) return 'Sangat Sehat';
        if ($this->rasio_likuiditas >= 100) return 'Sehat';
        if ($this->rasio_likuiditas >= 75)  return 'Cukup Sehat';
        return 'Perlu Perhatian';
    }

    public function warnaStatus(): string
    {
        return match ($this->statusKesehatan()) {
            'Sangat Sehat'     => 'emerald',
            'Sehat'            => 'blue',
            'Cukup Sehat'      => 'amber',
            'Perlu Perhatian'  => 'red',
            default            => 'gray',
        };
    }
}
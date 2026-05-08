<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiterasiArtikel extends Model
{
    protected $table = 'literasi_artikels';

    protected $fillable = [
        'judul', 'slug', 'ringkasan', 'konten', 'kategori',
        'thumbnail', 'estimasi_baca', 'level', 'is_published', 'views', 'created_by',
    ];

    protected $casts = [
        'is_published'  => 'boolean',
        'views'         => 'integer',
        'estimasi_baca' => 'integer',
    ];

    public function penulis(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeKategori($query, string $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public static function daftarKategori(): array
    {
        return [
            'menabung'    => ['label' => 'Menabung',             'icon' => '🏦', 'warna' => 'emerald'],
            'pinjaman'    => ['label' => 'Pinjaman & Bunga',     'icon' => '💳', 'warna' => 'blue'],
            'koperasi'    => ['label' => 'Cara Kerja Koperasi',  'icon' => '🤝', 'warna' => 'amber'],
            'investasi'   => ['label' => 'Investasi Dasar',      'icon' => '📈', 'warna' => 'purple'],
            'utang'       => ['label' => 'Manajemen Utang',      'icon' => '⚖️', 'warna' => 'red'],
            'perencanaan' => ['label' => 'Perencanaan Keuangan', 'icon' => '🗂️', 'warna' => 'indigo'],
        ];
    }

    public function labelLevel(): string
    {
        return match($this->level) {
            'pemula'   => 'Pemula',
            'menengah' => 'Menengah',
            'mahir'    => 'Mahir',
            default    => $this->level,
        };
    }
}
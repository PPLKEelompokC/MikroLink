<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'subject',
        'category',
        'priority',
        'description',
        'status',
        'attachment',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open'        => 'Terbuka',
            'in_progress' => 'Diproses',
            'resolved'    => 'Selesai',
            'closed'      => 'Ditutup',
            default       => 'Terbuka',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'umum'       => 'Umum',
            'pinjaman'   => 'Pinjaman',
            'pembayaran' => 'Pembayaran',
            'teknis'     => 'Teknis',
            'lainnya'    => 'Lainnya',
            default      => 'Umum',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'low'    => 'Rendah',
            'medium' => 'Sedang',
            'high'   => 'Tinggi',
            default  => 'Sedang',
        };
    }

    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT';
        $date   = now()->format('Ymd');
        $last   = static::whereDate('created_at', today())->count() + 1;
        return $prefix . '-' . $date . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}
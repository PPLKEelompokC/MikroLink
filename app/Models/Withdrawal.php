<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'bank_name',
        'bank_account',
        'bank_account_name',
        'proof_path',
        'admin_note',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

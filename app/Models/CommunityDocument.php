<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class CommunityDocument extends Model
{
    use Auditable;

    protected $fillable = [
        'user_id',
        'document_name',
        'file_path',
        'status',
        'note',
    ];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }
}

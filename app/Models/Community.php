<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use Auditable;

    protected $fillable = [
        'name',
        'description',
    ];

    public function communityDocuments()
    {
        return $this->hasMany(CommunityDocument::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function communityDocuments()
    {
        return $this->hasMany(CommunityDocument::class);
    }
}
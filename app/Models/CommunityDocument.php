<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Community;

class CommunityDocument extends Model
{
    
    protected $fillable = [
        'community_id', 
        'document_name', 
        'file_path', 
        'status', 
        'note'
    ];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }
} 

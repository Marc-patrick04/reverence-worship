<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicBoard extends Model
{
    protected $table = 'public_board';
    
    protected $fillable = [
        'title', 'content', 'is_pinned', 'created_by'
    ];
    
    protected $casts = [
        'is_pinned' => 'boolean'
    ];
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
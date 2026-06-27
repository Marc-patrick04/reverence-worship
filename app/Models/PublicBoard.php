<?php

namespace App\Models;
use App\Models\User\User; 
use Illuminate\Database\Eloquent\Model;

class PublicBoard extends Model
{
    protected $table = 'public_board';
    
    protected $fillable = [
        'title', 'content', 'type', 'event_date', 'is_published', 'is_pinned', 'created_by'
    ];
    
    protected $casts = [
        'is_pinned' => 'boolean',
        'is_published' => 'boolean',
        'event_date' => 'datetime',
    ];
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

<?php

namespace App\Models\Music;

use Illuminate\Database\Eloquent\Model;

class Singer extends Model
{
    protected $table = 'singers';
    
    protected $fillable = [
        'user_id', 'name', 'email', 'voice_part', 'performance_level', 'phone', 'created_by'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
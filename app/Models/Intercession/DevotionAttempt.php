<?php

namespace App\Models\Intercession;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class DevotionAttempt extends Model
{
    protected $table = 'user_devotion_completions';
    
    protected $fillable = [
        'user_id',
        'devotion_id',
        'completed_at'
    ];
    
    protected $casts = [
        'completed_at' => 'datetime',
        'created_at' => 'datetime'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function devotion()
    {
        return $this->belongsTo(DailyDevotion::class, 'devotion_id');
    }
}
<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    
    public $timestamps = false;
    
    protected $fillable = [
        'user_id', 'action', 'description', 'ip_address', 'user_agent'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
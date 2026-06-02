<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class ErrorLog extends Model
{
    protected $table = 'error_logs';
    
    public $timestamps = false;
    
    protected $fillable = [
        'error_type', 'message', 'file_path', 'line_number', 'stack_trace', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
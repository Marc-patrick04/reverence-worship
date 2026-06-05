<?php

namespace App\Models\Announcement;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class Announcement extends Model
{
    protected $table = 'announcements';
    
    protected $fillable = [
        'title', 'content', 'type', 'status', 'scheduled_date', 
        'expiry_date', 'target_audience', 'priority', 'image_path', 
        'created_by', 'published_by', 'published_at'
    ];
    
    protected $casts = [
        'scheduled_date' => 'date',
        'expiry_date' => 'date',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('scheduled_date')
                  ->orWhere('scheduled_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', now());
            });
    }
}
<?php

namespace App\Models\Intercession;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\User\User;

class DailyDevotion extends Model
{
    protected $table = 'devotions';
    
    protected $fillable = [
        'title',
        'content',
        'bible_verse',
        'date',
        'created_by',
        'is_active'
    ];
    
    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public static function getTodaysDevotion()
    {
        return self::where('date', date('Y-m-d'))
            ->where('is_active', true)
            ->first();
    }
    
    public static function getRecentDevotions($limit = 5)
    {
        return self::where('date', '<', date('Y-m-d'))
            ->where('is_active', true)
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get();
    }
    
    public function isCompletedByUser($userId)
    {
        return DB::table('user_devotion_completions')
            ->where('user_id', $userId)
            ->where('devotion_id', $this->id)
            ->exists();
    }
    
    public function markAsCompleted($userId)
    {
        if (!$this->isCompletedByUser($userId)) {
            return DB::table('user_devotion_completions')->insert([
                'user_id' => $userId,
                'devotion_id' => $this->id,
                'completed_at' => now()
            ]);
        }
        return false;
    }
}
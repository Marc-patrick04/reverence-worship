<?php

namespace App\Models\Music;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;
class TeamMember extends Model
{
    protected $table = 'team_members';
    
    protected $fillable = [
        'service_team_id', 'team_number', 'user_id', 'voice_part', 'performance_level'
    ];
    
    // Disable automatic timestamps
    public $timestamps = false;
    
    public function serviceTeam()
    {
        return $this->belongsTo(ServiceTeam::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
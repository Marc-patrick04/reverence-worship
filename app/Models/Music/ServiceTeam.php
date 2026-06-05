<?php

namespace App\Models\Music;
use App\Models\User\User; 
use Illuminate\Database\Eloquent\Model;

class ServiceTeam extends Model
{
    protected $table = 'service_teams';
    
    protected $fillable = [
    'service_name', 'service_date', 'number_of_teams', 'generated_at', 'created_by'
];
    
    // Disable automatic timestamps since the table doesn't have updated_at
    public $timestamps = false;
    
   protected $casts = [
    'service_date' => 'date',
    'generated_at' => 'datetime',
    'created_at' => 'datetime'
];
    
    public function members()
    {
        return $this->hasMany(TeamMember::class, 'service_team_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function getTeamsGrouped()
    {
        return $this->members->groupBy('team_number');
    }
}
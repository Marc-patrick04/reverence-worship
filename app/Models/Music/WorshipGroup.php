<?php

namespace App\Models\Music;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;
class WorshipGroup extends Model
{
    protected $table = 'groups_table';
    
    protected $fillable = [
        'name', 'description', 'leader_id', 'member_count', 'created_by'
    ];
    
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }
    
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members', 'group_id', 'user_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
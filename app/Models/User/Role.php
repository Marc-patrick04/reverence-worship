<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use App\Models\System\RolePageFeature;
use App\Models\System\Page;
use App\Models\System\Feature;

class Role extends Model
{
    protected $table = 'roles';
    
    protected $fillable = ['name', 'display_name', 'description'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }

    public function rolePageFeatures()
    {
        return $this->hasMany(RolePageFeature::class, 'role_id');
    }
    
    public function getAssignedPages()
    {
        return Page::whereHas('roleFeatures', function($q) {
            $q->where('role_id', $this->id);
        })->get();
    }
    
    public function getAssignedFeatures()
    {
        return Feature::whereHas('roleFeatures', function($q) {
            $q->where('role_id', $this->id);
        })->get();
    }
}
<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User\Role;
use App\Models\System\RolePageFeature;
use App\Models\System\Page;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    
    protected $fillable = [
        'name', 'email', 'password', 'is_active', 'created_by',
        'phone', 'date_of_birth', 'province', 'district', 'sector', 'village',
        'gender', 'marital_status', 'membership_type', 'occupation', 'ministry_role',
        'emergency_contact', 'emergency_name', 'skills', 'notes',
        'is_singer', 'voice_part', 'singer_level', 'singer_notes','google_id',
    'avatar',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'date_of_birth' => 'date',
        'is_singer' => 'boolean',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin');
    }

    public function canAccess($pageName, $featureName)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        $roleIds = $this->roles->pluck('id');
        
        if ($roleIds->isEmpty()) {
            return false;
        }
        
        return RolePageFeature::whereIn('role_id', $roleIds)
            ->whereHas('page', function($q) use ($pageName) {
                $q->where('pages.name', $pageName);
            })
            ->whereHas('feature', function($q) use ($featureName) {
                $q->where('features.name', $featureName);
            })
            ->exists();
    }
    
    public function getAccessiblePages()
    {
        if ($this->isSuperAdmin()) {
            return Page::where('is_active', true)->orderBy('sort_order')->get();
        }
        
        $roleIds = $this->roles->pluck('id');
        
        if ($roleIds->isEmpty()) {
            return collect();
        }
        
        return Page::whereHas('roleFeatures', function($q) use ($roleIds) {
            $q->whereIn('role_id', $roleIds);
        })->where('is_active', true)
        ->orderBy('sort_order')
        ->get();
    }
    
    public function isSinger()
    {
        return $this->is_singer == true;
    }
}
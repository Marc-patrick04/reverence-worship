<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User\Role;
use App\Models\System\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    
    protected $fillable = [
        'name', 'email', 'password', 'is_active', 'created_by',
        'phone', 'date_of_birth', 'province', 'district', 'sector', 'village',
        'gender', 'marital_status', 'membership_type', 'occupation', 'ministry_role',
        'emergency_contact', 'emergency_name', 'skills', 'notes',
        'is_singer', 'voice_part', 'singer_level', 'singer_notes', 'google_id', 'avatar',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'date_of_birth' => 'date',
        'is_singer' => 'boolean',
    ];

    public function isPending()
    {
        return !$this->is_active && $this->email_verified_at === null;
    }

    public function getStatusAttribute()
    {
        if ($this->is_active) {
            return 'active';
        }
        return 'pending';
    }
    
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function hasRole($roleName)
    {
        try {
            return DB::table('roles')
                ->join('role_user', 'roles.id', '=', 'role_user.role_id')
                ->where('role_user.user_id', $this->id)
                ->where('roles.name', $roleName)
                ->exists();
        } catch (\Exception $e) {
            Log::error('hasRole error: ' . $e->getMessage());
            return false;
        }
    }

    public function getRoleIds()
    {
        try {
            return DB::table('role_user')
                ->where('user_id', $this->id)
                ->pluck('role_id')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('getRoleIds error: ' . $e->getMessage());
            return [];
        }
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin');
    }

    public function isParent()
    {
        return DB::table('family_members')
            ->where('user_id', $this->id)
            ->where('role', 'parent')
            ->exists()
            || DB::table('families')
                ->where('parent_id', $this->id)
                ->exists();
    }

    public function hasFamily()
    {
        return DB::table('family_members')
            ->where('user_id', $this->id)
            ->exists();
    }

   public function getAccessiblePages()
{
    // Super admin can see all active pages
    if ($this->isSuperAdmin()) {
        return Page::where('is_active', true)->orderBy('sort_order')->get();
    }
    
    // Get user's role IDs
    $roleIds = $this->getRoleIds();
    
    if (empty($roleIds)) {
        return collect();
    }
    
    try {
        // Check if table exists
        $tableExists = DB::select("SELECT to_regclass('role_page_features')");
        if (!$tableExists[0]->to_regclass) {
            return collect();
        }
        
        // Get distinct page IDs where user has ANY permission (view, create, edit, delete, export)
        $pageIds = DB::table('role_page_features')
            ->whereIn('role_id', $roleIds)
            ->distinct()
            ->pluck('page_id')
            ->toArray();
        
        if (empty($pageIds)) {
            return collect();
        }
        
        return Page::whereIn('id', $pageIds)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    } catch (\Exception $e) {
        Log::error('getAccessiblePages error: ' . $e->getMessage());
        return collect();
    }
}

   public function canAccess($pageName, $featureName)
{
    // Super admin has all access
    if ($this->isSuperAdmin()) {
        return true;
    }
    
    // Get user's role IDs
    $roleIds = $this->getRoleIds();
    
    if (empty($roleIds)) {
        return false;
    }
    
    try {
        // If checking for 'view', also return true if user has any permission on this page
        if ($featureName === 'view') {
            $count = DB::table('role_page_features')
                ->join('pages', 'role_page_features.page_id', '=', 'pages.id')
                ->whereIn('role_page_features.role_id', $roleIds)
                ->where('pages.name', $pageName)
                ->count();
            return $count > 0;
        }
        
        // For specific permissions (create, edit, delete, export)
        $count = DB::table('role_page_features')
            ->join('pages', 'role_page_features.page_id', '=', 'pages.id')
            ->join('features', 'role_page_features.feature_id', '=', 'features.id')
            ->whereIn('role_page_features.role_id', $roleIds)
            ->where('pages.name', $pageName)
            ->where('features.name', $featureName)
            ->count();
        
        return $count > 0;
    } catch (\Exception $e) {
        Log::error('canAccess error: ' . $e->getMessage());
        return false;
    }
}

    /**
     * Check if user has a specific permission
     */
    public function hasPermission($permissionName)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        $roleIds = $this->getRoleIds();
        
        if (empty($roleIds)) {
            return false;
        }
        
        try {
            $tableExists = DB::select("SELECT to_regclass('role_page_features')");
            if (!$tableExists[0]->to_regclass) {
                return false;
            }
            
            $count = DB::table('role_page_features')
                ->join('features', 'role_page_features.feature_id', '=', 'features.id')
                ->whereIn('role_page_features.role_id', $roleIds)
                ->where('features.name', $permissionName)
                ->count();
            
            return $count > 0;
        } catch (\Exception $e) {
            Log::error('hasPermission error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Alias for hasPermission
     */
    public function hasPermissionTo($permissionName)
    {
        return $this->hasPermission($permissionName);
    }

    public function isSinger()
    {
        return $this->is_singer == true;
    }
    /**
 * Check if user has any permission on a page
 */
public function canAccessAny($pageName)
{
    if ($this->isSuperAdmin()) {
        return true;
    }
    
    $roleIds = $this->getRoleIds();
    if (empty($roleIds)) {
        return false;
    }
    
    try {
        $count = DB::table('role_page_features')
            ->join('pages', 'role_page_features.page_id', '=', 'pages.id')
            ->whereIn('role_page_features.role_id', $roleIds)
            ->where('pages.name', $pageName)
            ->count();
        
        return $count > 0;
    } catch (\Exception $e) {
        return false;
    }
}

/**
 * Check if user can export data
 */
public function canExport($pageName)
{
    return $this->canAccess($pageName, 'export') || $this->canAccess($pageName, 'view');
}
}

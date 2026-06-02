<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class RolePageFeature extends Model
{
    protected $table = 'role_page_features';
    
    protected $fillable = [
        'role_id', 'page_id', 'feature_id'
    ];
    
    // Disable timestamps if your table doesn't have updated_at
    public $timestamps = false;
    
    // If you have timestamps, use this instead:
    // public $timestamps = true;
    
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
    public function page()
    {
        return $this->belongsTo(Page::class);
    }
    
    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }
}
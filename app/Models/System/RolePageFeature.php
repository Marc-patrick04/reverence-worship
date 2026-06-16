<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\Role;

class RolePageFeature extends Model
{
    protected $table = 'role_page_feature';
    
    protected $fillable = [
        'role_id',
        'page_id',
        'feature_id'
    ];
    
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    
    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }
    
    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }
}
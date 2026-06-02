<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';
    
    protected $fillable = [
        'name', 'display_name', 'icon', 'route', 'sort_order', 'is_active'
    ];
    
    public function features()
    {
        return $this->hasMany(Feature::class);
    }
    
    public function roleFeatures()
    {
        return $this->hasMany(RolePageFeature::class);
    }
}
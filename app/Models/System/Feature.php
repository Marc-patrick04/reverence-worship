<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $table = 'features';
    
    protected $fillable = [
        'page_id', 'name', 'display_name', 'description'
    ];
    
    public function page()
    {
        return $this->belongsTo(Page::class);
    }
    
    public function roleFeatures()
    {
        return $this->hasMany(RolePageFeature::class);
    }
}
<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table = 'system_settings';
    
    // Enable timestamps for settings
    public $timestamps = true;
    
    protected $fillable = [
        'setting_key', 'setting_value', 'setting_type', 'description', 'updated_by'
    ];

    // Get setting value
    public static function getValue($key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();
        if (!$setting) {
            return $default;
        }
        
        if ($setting->setting_type === 'boolean') {
            return $setting->setting_value === '1' || $setting->setting_value === 'true';
        }
        
        return $setting->setting_value;
    }

    // Set setting value
    public static function setValue($key, $value, $updatedBy = null)
    {
        $setting = self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'updated_by' => $updatedBy,
                'setting_type' => is_bool($value) ? 'boolean' : 'text'
            ]
        );
        
        return $setting;
    }
}
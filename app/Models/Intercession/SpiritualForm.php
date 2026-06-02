<?php

namespace App\Models\Intercession;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class SpiritualForm extends Model
{
    protected $table = 'forms';
    
    protected $fillable = [
        'title',
        'description',
        'questions',
        'settings',
        'is_active',
        'created_by'
    ];
    
    protected $casts = [
        'questions' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function submissions()
    {
        return $this->hasMany(FormSubmission::class, 'form_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
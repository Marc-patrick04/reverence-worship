<?php

namespace App\Models\Intercession;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;
class FormSubmission extends Model
{
    protected $table = 'form_submissions';
    
    protected $fillable = [
        'form_id',
        'user_id',
        'answers',
        'score',
        'submitted_at'
    ];
    
    protected $casts = [
        'answers' => 'array',
        'score' => 'float',
        'submitted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function form()
    {
        return $this->belongsTo(SpiritualForm::class, 'form_id');
    }
    
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
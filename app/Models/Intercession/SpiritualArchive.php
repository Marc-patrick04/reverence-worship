<?php

namespace App\Models\Intercession;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class SpiritualArchive extends Model
{
    protected $table = 'spiritual_archives';
    
    protected $fillable = [
        'title', 'content', 'type', 'file_path', 'created_by'
    ];
    
    public $timestamps = true;
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
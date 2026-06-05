<?php

namespace App\Models\Music;
use App\Models\User\User; 
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $table = 'photo_gallery';
    
    protected $fillable = [
        'title', 'image_path', 'description', 'event_date', 'created_by'
    ];
    
    protected $casts = [
        'event_date' => 'date'
    ];
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
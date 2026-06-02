<?php

namespace App\Models\Music;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class Song extends Model
{
    protected $table = 'songs';
    
    protected $fillable = [
        'title', 'artist', 'key_signature', 'tempo', 'lyrics', 
        'youtube_link', 'assigned_singer', 'created_by'
    ];
    
    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_songs', 'song_id', 'playlist_id')
                    ->withPivot('display_order');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
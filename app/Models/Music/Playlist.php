<?php

namespace App\Models\Music;

use Illuminate\Database\Eloquent\Model;
use App\Models\Music\Song;
use App\Models\User\User; 

class Playlist extends Model
{
    protected $table = 'playlists';
    
    protected $fillable = ['title', 'description', 'created_by'];
    
    public function songs()
    {
        return $this->belongsToMany(Song::class, 'playlist_songs', 'playlist_id', 'song_id')
                    ->withPivot('display_order')
                    ->orderBy('playlist_songs.display_order');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
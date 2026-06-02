<?php

namespace App\Models\Music;

use Illuminate\Database\Eloquent\Model;

class PlaylistSong extends Model
{
    protected $table = 'playlist_songs';
    
    protected $fillable = [
        'playlist_id', 'song_id', 'display_order'
    ];
    
    // Disable timestamps since the table doesn't have updated_at
    public $timestamps = false;
    
    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }
    
    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
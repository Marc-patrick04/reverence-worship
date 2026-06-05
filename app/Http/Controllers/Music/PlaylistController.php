<?php

namespace App\Http\Controllers\Music;

use App\Http\Controllers\Controller;
use App\Models\Music\Playlist;
use App\Models\Music\Song;
use App\Models\Music\PlaylistSong;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function store(Request $request)
    {
        if (!auth()->user()->canAccess('music-ministry', 'add-playlists')) {
            abort(403);
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'songs' => 'array'
        ]);
        
        $playlist = Playlist::create([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => auth()->id()
        ]);
        
        $songsAdded = 0;
        
        if ($request->has('songs')) {
            foreach ($request->songs as $index => $songId) {
                PlaylistSong::create([
                    'playlist_id' => $playlist->id,
                    'song_id' => $songId,
                    'display_order' => $index + 1
                ]);
                $songsAdded++;
            }
        }
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'songs_added' => $songsAdded]);
        }
        
        return redirect()->back()->with('success', 'Playlist created with ' . $songsAdded . ' songs!');
    }
    
    public function edit($id)
    {
        $playlist = Playlist::with('songs')->findOrFail($id);
        $songs = Song::orderBy('title')->get();
        $playlistSongIds = $playlist->songs->pluck('id')->toArray();
        
        return view('modules.music.edit-playlist', compact('playlist', 'songs', 'playlistSongIds'));
    }
    
    public function updateSongs(Request $request, $id)
    {
        $playlist = Playlist::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'songs' => 'array'
        ]);
        
        $playlist->title = $request->title;
        $playlist->description = $request->description;
        $playlist->save();
        
        PlaylistSong::where('playlist_id', $id)->delete();
        
        if ($request->has('songs')) {
            foreach ($request->songs as $index => $songId) {
                PlaylistSong::create([
                    'playlist_id' => $id,
                    'song_id' => $songId,
                    'display_order' => $index + 1
                ]);
            }
        }
        
        return redirect()->route('music.index')->with('success', 'Playlist updated successfully!');
    }
    
    public function destroy($id)
    {
        $playlist = Playlist::findOrFail($id);
        $playlist->delete();
        
        return redirect()->back()->with('success', 'Playlist deleted!');
    }
    
    public function getSongs($id)
    {
        try {
            $playlist = Playlist::with('songs')->findOrFail($id);
            
            $songsData = $playlist->songs->map(function($song) {
                return [
                    'id' => $song->id,
                    'title' => $song->title,
                    'artist' => $song->artist ?? '',
                    'key_signature' => $song->key_signature ?? '',
                    'tempo' => $song->tempo ?? '',
                    'assigned_singer' => $song->assigned_singer ?? '',
                    'lyrics' => $song->lyrics ?? ''
                ];
            });
            
            return response()->json([
                'success' => true,
                'playlist_title' => $playlist->title,
                'songs' => $songsData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
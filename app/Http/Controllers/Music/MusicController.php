<?php

namespace App\Http\Controllers\Music;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Music\Playlist;
use App\Models\Music\Song;
use App\Models\Music\PlaylistSong;
use App\Models\User\User;
use App\Models\ActivityLog;
use App\Models\Music\Gallery;
use App\Models\Music\WorshipGroup;
use App\Models\PublicBoard;
use App\Models\ActionPlan;
use App\Models\Music\ServiceTeam;
use App\Models\Music\TeamMember;

class MusicController extends Controller
{
    // ==================== MAIN INDEX ====================
    public function index()
    {
        if (!auth()->user()->canAccess('music-ministry', 'access')) {
            abort(403, 'You do not have permission to access this page.');
        }
        
        $playlists = Playlist::with('songs')->orderBy('created_at', 'desc')->get();
        $songs = Song::orderBy('title')->get();
        $singers = User::where('is_singer', true)->orderBy('name')->get();
        $gallery = Gallery::orderBy('created_at', 'desc')->get();
        $groups = WorshipGroup::with('leader', 'members')->get();
        $posts = PublicBoard::with('creator')->orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc')->get();
        $tasks = ActionPlan::with('assignedUser', 'creator')->orderBy('due_date')->get();
        $users = User::where('is_active', true)->get();
        $serviceTeams = ServiceTeam::with('members.user')->orderBy('created_at', 'desc')->get();
        $generations = ServiceTeam::with('members.user')->orderBy('created_at', 'desc')->get();
        $voiceParts = ['Soprano', 'Alto', 'Tenor', 'Bass', 'Lead'];
        $performanceLevels = ['Normal', 'Good', 'Advanced', 'Professional'];
        
        return view('modules.music.index', compact('playlists', 'songs', 'singers', 'gallery', 'groups', 'posts', 'tasks', 'users', 'serviceTeams', 'generations', 'voiceParts', 'performanceLevels'));
    }

    // ==================== PLAYLIST METHODS ====================
    
    public function storePlaylist(Request $request)
    {
        if (!auth()->user()->canAccess('music-ministry', 'add-playlists')) {
            abort(403, 'You do not have permission to create playlists.');
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
    
    public function editPlaylist($id)
    {
        $playlist = Playlist::with('songs')->findOrFail($id);
        $songs = Song::orderBy('title')->get();
        $playlistSongIds = $playlist->songs->pluck('id')->toArray();
        
        return view('modules.music.edit-playlist', compact('playlist', 'songs', 'playlistSongIds'));
    }
    
    public function updatePlaylistSongs(Request $request, $id)
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
    
    public function deletePlaylist($id)
    {
        $playlist = Playlist::findOrFail($id);
        $playlist->delete();
        
        return redirect()->back()->with('success', 'Playlist deleted!');
    }
    
    public function getPlaylistSongs($id)
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

    // ==================== SONG METHODS ====================
    
    public function storeSong(Request $request)
    {
        if (!auth()->user()->canAccess('music-ministry', 'add-songs')) {
            abort(403, 'You do not have permission to add songs.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'nullable|string|max:255',
            'key_signature' => 'nullable|string|max:10',
            'tempo' => 'nullable|integer',
            'lyrics' => 'nullable|string',
            'youtube_link' => 'nullable|url',
            'assigned_singer' => 'nullable|string|max:255'
        ]);
        
        $song = Song::create([
            'title' => $request->title,
            'artist' => $request->artist,
            'key_signature' => $request->key_signature,
            'tempo' => $request->tempo,
            'lyrics' => $request->lyrics,
            'youtube_link' => $request->youtube_link,
            'assigned_singer' => $request->assigned_singer,
            'created_by' => auth()->id()
        ]);
        
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'Song added successfully!');
    }
    
    public function editSong($id)
    {
        $song = Song::findOrFail($id);
        return view('modules.music.edit-song', compact('song'));
    }
    
    public function updateSong(Request $request, $id)
    {
        $song = Song::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'nullable|string|max:255',
            'key_signature' => 'nullable|string|max:10',
            'tempo' => 'nullable|integer',
            'lyrics' => 'nullable|string',
            'youtube_link' => 'nullable|url',
            'assigned_singer' => 'nullable|string|max:255'
        ]);
        
        $song->update($request->all());
        
        return redirect()->route('music.index')->with('success', 'Song updated successfully!');
    }
    
    public function deleteSong($id)
    {
        $song = Song::findOrFail($id);
        $song->delete();
        
        return redirect()->back()->with('success', 'Song deleted!');
    }
    
    public function viewLyrics($id)
    {
        $song = Song::findOrFail($id);
        return view('modules.music.lyrics-modal', compact('song'));
    }
    
    public function addToPlaylist(Request $request)
    {
        $request->validate([
            'playlist_id' => 'required|exists:playlists,id',
            'song_id' => 'required|exists:songs,id'
        ]);
        
        $exists = PlaylistSong::where('playlist_id', $request->playlist_id)
            ->where('song_id', $request->song_id)
            ->exists();
        
        if (!$exists) {
            $maxOrder = PlaylistSong::where('playlist_id', $request->playlist_id)->max('display_order') ?? 0;
            PlaylistSong::create([
                'playlist_id' => $request->playlist_id,
                'song_id' => $request->song_id,
                'display_order' => $maxOrder + 1
            ]);
        }
        
        return response()->json(['success' => true]);
    }

    // ==================== SINGER METHODS ====================
    
    public function updateVoicePart(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->voice_part = $request->voice_part;
        $user->save();
        
        return response()->json(['success' => true]);
    }
    
    public function updatePerformanceLevel(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->singer_level = $request->performance_level;
        $user->save();
        
        return response()->json(['success' => true]);
    }
    
   public function updateSingerSettings(Request $request)
{
    try {
        $user = User::findOrFail($request->user_id);
        
        if ($request->field === 'voice_part') {
            $user->voice_part = $request->value;
        } elseif ($request->field === 'singer_level') {
            $user->singer_level = $request->value;
        }
        $user->save();
        
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    
    
    

    // ==================== GROUPS METHODS ====================
    
    public function storeGroup(Request $request)
    {
        if (!auth()->user()->canAccess('music-ministry', 'add-groups')) {
            abort(403, 'You do not have permission to create groups.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'nullable|exists:users,id'
        ]);
        
        WorshipGroup::create([
            'name' => $request->name,
            'description' => $request->description,
            'leader_id' => $request->leader_id,
            'created_by' => auth()->id()
        ]);
        
        return redirect()->back()->with('success', 'Group created successfully!');
    }
    
    public function deleteGroup($id)
    {
        $group = WorshipGroup::findOrFail($id);
        $group->delete();
        
        return redirect()->back()->with('success', 'Group deleted successfully!');
    }

    // ==================== PUBLIC BOARD METHODS ====================
    
    public function storeBoardPost(Request $request)
    {
        if (!auth()->user()->canAccess('music-ministry', 'add-board')) {
            abort(403, 'You do not have permission to create posts.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ]);
        
        PublicBoard::create([
            'title' => $request->title,
            'content' => $request->content,
            'is_pinned' => $request->has('is_pinned'),
            'created_by' => auth()->id()
        ]);
        
        return redirect()->back()->with('success', 'Announcement posted successfully!');
    }
    
    public function togglePinBoard($id)
    {
        $post = PublicBoard::findOrFail($id);
        $post->is_pinned = !$post->is_pinned;
        $post->save();
        
        return redirect()->back()->with('success', 'Post ' . ($post->is_pinned ? 'pinned' : 'unpinned'));
    }
    
    public function deleteBoardPost($id)
    {
        $post = PublicBoard::findOrFail($id);
        $post->delete();
        
        return redirect()->back()->with('success', 'Announcement deleted successfully!');
    }

    // ==================== ACTION PLAN METHODS ====================
    
    public function storeActionPlan(Request $request)
    {
        if (!auth()->user()->canAccess('music-ministry', 'manage-actionplan')) {
            abort(403, 'You do not have permission to create action plans.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id'
        ]);
        
        ActionPlan::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => 'pending',
            'assigned_to' => $request->assigned_to,
            'created_by' => auth()->id()
        ]);
        
        return redirect()->back()->with('success', 'Task created successfully!');
    }
    
    public function updateActionPlanStatus(Request $request, $id)
    {
        $task = ActionPlan::findOrFail($id);
        $task->status = $request->status;
        $task->save();
        
        return redirect()->back()->with('success', 'Task status updated!');
    }
    
    public function deleteActionPlan($id)
    {
        $task = ActionPlan::findOrFail($id);
        $task->delete();
        
        return redirect()->back()->with('success', 'Task deleted successfully!');
    }

    // ==================== SERVICE TEAM GENERATOR METHODS ====================
    
    public function generateBalancedGroups(Request $request)
{
    try {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'number_of_teams' => 'required|integer|min:1|max:10'
        ]);
        
        // Get all singers with voice part and level assigned
        $singers = User::where('is_singer', true)
            ->whereNotNull('voice_part')
            ->whereNotNull('singer_level')
            ->get()
            ->map(function($singer) {
                return [
                    'id' => $singer->id,
                    'name' => $singer->name,
                    'email' => $singer->email,
                    'voice_part' => $singer->voice_part,
                    'performance_level' => $singer->singer_level
                ];
            });
        
        if ($singers->count() < $request->number_of_teams) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough singers. Need at least ' . $request->number_of_teams . ' singers.'
            ]);
        }
        
        $numTeams = $request->number_of_teams;
        
        // Group singers by voice part
        $groupedByVoice = [];
        foreach ($singers as $singer) {
            $voice = $singer['voice_part'];
            if (!isset($groupedByVoice[$voice])) {
                $groupedByVoice[$voice] = [];
            }
            $groupedByVoice[$voice][] = $singer;
        }
        
        // Sort each voice group by performance level (Good first, then Normal)
        $levelOrder = ['Good' => 1, 'Normal' => 2];
        foreach ($groupedByVoice as $voice => &$voiceSingers) {
            usort($voiceSingers, function($a, $b) use ($levelOrder) {
                return ($levelOrder[$a['performance_level']] ?? 99) - ($levelOrder[$b['performance_level']] ?? 99);
            });
        }
        
        // Initialize teams
        $teams = array_fill(0, $numTeams, []);
        $teamScores = array_fill(0, $numTeams, 0);
        
        // Distribute voice parts evenly (snake algorithm)
        $voiceParts = array_keys($groupedByVoice);
        $voiceIndex = 0;
        $direction = 1; // 1 for forward, -1 for backward
        $teamIndex = 0;
        
        // First pass: distribute high-level singers (Good) evenly
        foreach ($voiceParts as $voice) {
            $voiceSingers = $groupedByVoice[$voice];
            $goodSingers = array_filter($voiceSingers, function($s) {
                return $s['performance_level'] == 'Good';
            });
            $normalSingers = array_filter($voiceSingers, function($s) {
                return $s['performance_level'] == 'Normal';
            });
            
            // Distribute Good singers first
            $goodArray = array_values($goodSingers);
            for ($i = 0; $i < count($goodArray); $i++) {
                $teams[$teamIndex % $numTeams][] = $goodArray[$i];
                $teamIndex++;
            }
            
            // Then distribute Normal singers
            $normalArray = array_values($normalSingers);
            for ($i = 0; $i < count($normalArray); $i++) {
                $teams[$teamIndex % $numTeams][] = $normalArray[$i];
                $teamIndex++;
            }
        }
        
        // Calculate team sizes and balance
        $targetSize = ceil($singers->count() / $numTeams);
        
        // Balance team sizes by moving singers
        for ($attempt = 0; $attempt < 10; $attempt++) {
            for ($i = 0; $i < $numTeams; $i++) {
                for ($j = $i + 1; $j < $numTeams; $j++) {
                    $sizeI = count($teams[$i]);
                    $sizeJ = count($teams[$j]);
                    
                    if (abs($sizeI - $sizeJ) > 1) {
                        if ($sizeI > $sizeJ) {
                            // Move one singer from team i to team j
                            $moved = array_pop($teams[$i]);
                            $teams[$j][] = $moved;
                        } elseif ($sizeJ > $sizeI) {
                            $moved = array_pop($teams[$j]);
                            $teams[$i][] = $moved;
                        }
                    }
                }
            }
        }
        
        // Final sort each team by voice part and level
        $voicePriority = ['Soprano' => 1, 'Alto' => 2, 'Tenor' => 3, 'Bass' => 4, 'Lead' => 5];
        foreach ($teams as &$team) {
            usort($team, function($a, $b) use ($voicePriority, $levelOrder) {
                $voiceCompare = ($voicePriority[$a['voice_part']] ?? 99) - ($voicePriority[$b['voice_part']] ?? 99);
                if ($voiceCompare != 0) return $voiceCompare;
                return ($levelOrder[$a['performance_level']] ?? 99) - ($levelOrder[$b['performance_level']] ?? 99);
            });
        }
        
        // Save to database
        $serviceTeam = ServiceTeam::create([
            'service_name' => $request->service_name,
            'number_of_teams' => $numTeams,
            'generated_at' => now(),
            'created_by' => auth()->id()
        ]);
        
        foreach ($teams as $teamNum => $members) {
            foreach ($members as $member) {
                TeamMember::create([
                    'service_team_id' => $serviceTeam->id,
                    'team_number' => $teamNum + 1,
                    'user_id' => $member['id'],
                    'voice_part' => $member['voice_part'],
                    'performance_level' => $member['performance_level']
                ]);
            }
        }
        
        // Prepare response
        $teamsData = [];
        foreach ($teams as $teamNum => $members) {
            $teamsData[] = [
                'team_number' => $teamNum + 1,
                'members' => array_values($members)
            ];
        }
        
        return response()->json([
            'success' => true,
            'service_team_id' => $serviceTeam->id,
            'service_name' => $request->service_name,
            'teams' => $teamsData,
            'total_members' => $singers->count(),
            'message' => 'Successfully created ' . $numTeams . ' balanced teams'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error generating groups: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
    
    public function getGenerationDetails($id)
    {
        $generation = ServiceTeam::with('members.user')->findOrFail($id);
        $teams = $generation->members->groupBy('team_number');
        
        $teamsData = [];
        foreach ($teams as $teamNum => $members) {
            $teamsData[] = [
                'team_number' => $teamNum,
                'members' => $members->map(function($member) {
                    return [
                        'name' => $member->user->name,
                        'voice_part' => $member->voice_part,
                        'performance_level' => $member->performance_level
                    ];
                })
            ];
        }
        
        return response()->json([
            'success' => true,
            'service_name' => $generation->service_name,
            'generated_at' => $generation->created_at->format('M d, Y H:i:s'),
            'number_of_teams' => $generation->number_of_teams,
            'total_members' => $generation->members->count(),
            'teams' => $teamsData
        ]);
    }
    
    public function exportGeneration($id)
    {
        $generation = ServiceTeam::with('members.user')->findOrFail($id);
        $teams = $generation->members->groupBy('team_number');
        
        $filename = 'groups_' . preg_replace('/[^a-zA-Z0-9]/', '_', $generation->service_name) . '_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        fputcsv($handle, ['Team', 'Name', 'Email', 'Voice Part', 'Performance Level']);
        
        foreach ($teams as $teamNum => $members) {
            foreach ($members as $member) {
                fputcsv($handle, [
                    'Team ' . $teamNum,
                    $member->user->name,
                    $member->user->email,
                    $member->voice_part,
                    $member->performance_level
                ]);
            }
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    public function restoreGeneration($id)
    {
        $oldGeneration = ServiceTeam::with('members')->findOrFail($id);
        
        $newGeneration = ServiceTeam::create([
            'service_name' => $oldGeneration->service_name . ' (Restored)',
            'number_of_teams' => $oldGeneration->number_of_teams,
            'generated_at' => now(),
            'created_by' => auth()->id()
        ]);
        
        foreach ($oldGeneration->members as $member) {
            TeamMember::create([
                'service_team_id' => $newGeneration->id,
                'team_number' => $member->team_number,
                'user_id' => $member->user_id,
                'voice_part' => $member->voice_part,
                'performance_level' => $member->performance_level
            ]);
        }
        
        return redirect()->back()->with('success', 'Generation restored successfully!');
    }
    
    public function deleteServiceTeam($id)
    {
        $team = ServiceTeam::findOrFail($id);
        $team->delete();
        
        return redirect()->back()->with('success', 'Service team deleted successfully!');
    }

   // ==================== GALLERY METHODS ====================

public function editGallery($id)
{
    try {
        $photo = Gallery::findOrFail($id);
        
        if (!auth()->user()->canAccess('music-ministry', 'edit-gallery')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        return response()->json([
            'success' => true,
            'photo' => $photo
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function updateGallery(Request $request, $id)
{
    try {
        if (!auth()->user()->canAccess('music-ministry', 'edit-gallery')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $photo = Gallery::findOrFail($id);
        
        $photo->title = $request->title;
        $photo->description = $request->caption;
        $photo->category = $request->category;
        $photo->tags = $request->tags;
        $photo->save();
        
        return response()->json(['success' => true, 'message' => 'Photo updated successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function deleteGallery($id)
{
    try {
        if (!auth()->user()->canAccess('music-ministry', 'delete-gallery')) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403, 'You do not have permission to delete photos.');
        }
        
        $photo = Gallery::findOrFail($id);
        
        // Delete the file from storage
        $filePath = public_path($photo->image_path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        $photo->delete();
        
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Photo deleted successfully']);
        }
        
        return redirect()->back()->with('success', 'Photo deleted successfully');
    } catch (\Exception $e) {
        if (request()->ajax()) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
        return redirect()->back()->with('error', 'Error deleting photo: ' . $e->getMessage());
    }
}

public function storeGallery(Request $request)
{
    if (!auth()->user()->canAccess('music-ministry', 'add-gallery')) {
        abort(403, 'You do not have permission to upload photos.');
    }
    
    $request->validate([
        'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        'alt_text' => 'required|string|max:255',
        'caption' => 'nullable|string',
        'category' => 'nullable|string',
        'tags' => 'nullable|string'
    ]);
    
    $uploadedCount = 0;
    
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $filename = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/gallery'), $filename);
            $imagePath = 'uploads/gallery/' . $filename;
            
            Gallery::create([
                'title' => $request->alt_text,
                'image_path' => $imagePath,
                'description' => $request->caption,
                'category' => $request->category,
                'tags' => $request->tags,
                'event_date' => now(),
                'created_by' => auth()->id()
            ]);
            $uploadedCount++;
        }
    }
    
    if ($request->ajax()) {
        return response()->json(['success' => true, 'uploaded' => $uploadedCount]);
    }
    
    return redirect()->back()->with('success', $uploadedCount . ' photo(s) uploaded successfully!');
}
}
<?php

namespace App\Http\Controllers\Music;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use App\Models\Music\ServiceTeam;
use App\Models\Music\TeamMember;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function generate(Request $request)
    {
        try {
            $request->validate([
                'service_name' => 'required|string|max:255',
                'number_of_teams' => 'required|integer|min:1|max:10'
            ]);
            
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
            $groupedByVoice = [];
            
            foreach ($singers as $singer) {
                $voice = $singer['voice_part'];
                if (!isset($groupedByVoice[$voice])) {
                    $groupedByVoice[$voice] = [];
                }
                $groupedByVoice[$voice][] = $singer;
            }
            
            $levelOrder = ['Good' => 1, 'Normal' => 2];
            foreach ($groupedByVoice as $voice => &$voiceSingers) {
                usort($voiceSingers, function($a, $b) use ($levelOrder) {
                    return ($levelOrder[$a['performance_level']] ?? 99) - ($levelOrder[$b['performance_level']] ?? 99);
                });
            }
            
            $teams = array_fill(0, $numTeams, []);
            $teamIndex = 0;
            
            foreach (array_keys($groupedByVoice) as $voice) {
                $voiceSingers = $groupedByVoice[$voice];
                foreach ($voiceSingers as $singer) {
                    $teams[$teamIndex % $numTeams][] = $singer;
                    $teamIndex++;
                }
            }
            
            for ($attempt = 0; $attempt < 10; $attempt++) {
                for ($i = 0; $i < $numTeams; $i++) {
                    for ($j = $i + 1; $j < $numTeams; $j++) {
                        $sizeI = count($teams[$i]);
                        $sizeJ = count($teams[$j]);
                        
                        if (abs($sizeI - $sizeJ) > 1) {
                            if ($sizeI > $sizeJ) {
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
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
   public function getGenerationDetails($id)
{
    try {
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
            'teams' => $teamsData
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
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
}
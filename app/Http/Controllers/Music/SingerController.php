<?php

namespace App\Http\Controllers\Music;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SingerController extends Controller
{
    public function updateVoicePart(Request $request, $id)
    {
        try {
            $user = User::find($id);
            
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }
            
            $user->voice_part = $request->voice_part;
            $user->save();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Update voice part error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function updatePerformanceLevel(Request $request, $id)
    {
        try {
            $user = User::find($id);
            
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }
            
            $user->singer_level = $request->performance_level;
            $user->save();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Update performance level error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function updateSettings(Request $request)
    {
        try {
            $updates = $request->input('updates', []);
            
            if (!$updates && $request->has('user_id')) {
                $updates = [[
                    'user_id' => $request->user_id,
                    'field' => $request->field,
                    'value' => $request->value
                ]];
            }
            
            if (empty($updates)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No updates provided'
                ], 400);
            }
            
            $updatedCount = 0;
            
            foreach ($updates as $update) {
                $userId = $update['user_id'];
                $field = $update['field'];
                $value = $update['value'];
                
                if (!in_array($field, ['voice_part', 'singer_level'])) {
                    continue;
                }
                
                $user = User::find($userId);
                
                if (!$user) {
                    continue;
                }
                
                if ($field === 'voice_part') {
                    $user->voice_part = $value;
                } elseif ($field === 'singer_level') {
                    $user->singer_level = $value;
                }
                
                $user->save();
                $updatedCount++;
            }
            
            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} settings",
                'updated_count' => $updatedCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('Update settings error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers\Announcement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Announcement\Announcement;
use App\Models\User\User;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = DB::table('announcements')
            ->leftJoin('users as creators', 'announcements.created_by', '=', 'creators.id')
            ->leftJoin('users as publishers', 'announcements.published_by', '=', 'publishers.id')
            ->select(
                'announcements.*',
                'creators.name as created_by_name',
                'publishers.name as published_by_name'
            )
            ->orderBy('announcements.created_at', 'desc')
            ->get();
        
        $stats = [
            'total' => DB::table('announcements')->count(),
            'active' => DB::table('announcements')->where('status', 'active')->count(),
            'scheduled' => DB::table('announcements')
                ->where('status', 'scheduled')
                ->orWhere('scheduled_date', '>', now())
                ->count(),
            'expired' => DB::table('announcements')
                ->where('status', 'expired')
                ->orWhere('expiry_date', '<', now())
                ->count(),
        ];
        
        $types = ['general', 'event', 'alert', 'maintenance', 'update'];
        $priorities = ['low', 'normal', 'high', 'urgent'];
        $statuses = ['active', 'scheduled', 'expired', 'draft'];
        
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        
        return view('modules.announcements.index', compact(
            'announcements', 'stats', 'types', 'priorities', 'statuses', 'users'
        ));
    }
    /**
 * Get all roles for selection
 */
public function getRoles()
{
    try {
        $roles = \App\Models\User\Role::where('name', '!=', 'super-admin')
            ->select('id', 'name', 'display_name')
            ->get();
        
        return response()->json([
            'success' => true,
            'roles' => $roles
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

/**
 * Get all active users for selection
 */
public function getUsers()
{
    try {
        $users = \App\Models\User\User::where('is_active', true)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
        
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    
    public function filter(Request $request)
    {
        try {
            $type = $request->type;
            $search = $request->search;
            
            $query = DB::table('announcements')
                ->leftJoin('users as creators', 'announcements.created_by', '=', 'creators.id')
                ->select('announcements.*', 'creators.name as created_by_name')
                ->where('announcements.status', 'active');
            
            if ($type && $type !== 'all') {
                $query->where('announcements.type', $type);
            }
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('announcements.title', 'ilike', "%{$search}%")
                      ->orWhere('announcements.content', 'ilike', "%{$search}%");
                });
            }
            
            $announcements = $query->orderBy('announcements.created_at', 'desc')->get();
            
            return response()->json(['success' => true, 'announcements' => $announcements]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_type' => 'required|in:all,roles,users',
            'target_roles' => 'nullable|string',
            'target_users' => 'nullable|string',
        ]);

        $targetRoles = json_decode($validated['target_roles'] ?? '[]', true);
        $targetUsers = json_decode($validated['target_users'] ?? '[]', true);

        if ($validated['target_type'] === 'roles' && empty($targetRoles)) {
            return response()->json(['success' => false, 'message' => 'Select at least one recipient role.'], 422);
        }
        if ($validated['target_type'] === 'users' && empty($targetUsers)) {
            return response()->json(['success' => false, 'message' => 'Select at least one recipient.'], 422);
        }
        
        $id = DB::table('announcements')->insertGetId([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => 'general',
            'status' => 'active',
            'scheduled_date' => null,
            'expiry_date' => null,
            'target_audience' => null,
            'priority' => 'normal',
            'image_path' => null,
            'created_by' => auth()->id(),
            'published_by' => auth()->id(),
            'published_at' => now(),
            'target_type' => $validated['target_type'],
            'target_roles' => $validated['target_type'] === 'roles' ? json_encode(array_values($targetRoles)) : null,
            'target_users' => $validated['target_type'] === 'users' ? json_encode(array_values($targetUsers)) : null,
            'email_sent' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        $delivery = $this->sendAnnouncementEmails($id);

        if ($delivery['sent'] === 0) {
            DB::table('announcements')->where('id', $id)->delete();
        }
        
        return response()->json([
            'success' => $delivery['sent'] > 0,
            'message' => $delivery['message'],
            'id' => $id,
            'delivery' => $delivery,
        ], $delivery['sent'] > 0 ? 201 : 422);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => collect($e->errors())->flatten()->first() ?: 'Please check the message details.',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Store announcement error: ' . $e->getMessage());
        return response()->json([
            'success' => false, 
            'message' => $e->getMessage()
        ], 500);
    }
}

public function sendAnnouncementEmails($announcementId, $force = false)
{
    try {
        // Get announcement with all data
        $announcement = DB::table('announcements')->where('id', $announcementId)->first();
        
        if (!$announcement) {
            \Log::error("Announcement #{$announcementId} not found");
            return ['sent' => 0, 'failed' => 0, 'message' => 'Message not found.'];
        }
        
        if ($announcement->email_sent && !$force) {
            \Log::info("Emails already sent for announcement #{$announcementId}");
            return ['sent' => 0, 'failed' => 0, 'message' => 'This message has already been sent.'];
        }
        
        // Get target users based on target_type
        $targetUsers = collect();
        
        if ($announcement->target_type === 'all') {
            // Get all active users
            $targetUsers = DB::table('users')
                ->where('is_active', true)
                ->select('id', 'name', 'email')
                ->get();
            \Log::info("Sending to ALL users: " . count($targetUsers) . " users");
            
        } elseif ($announcement->target_type === 'roles') {
            // Get users by roles
            $roleIds = json_decode($announcement->target_roles, true);
            if (!empty($roleIds)) {
                $targetUsers = DB::table('users')
                    ->join('role_user', 'users.id', '=', 'role_user.user_id')
                    ->whereIn('role_user.role_id', $roleIds)
                    ->where('users.is_active', true)
                    ->select('users.id', 'users.name', 'users.email')
                    ->distinct()
                    ->get();
                \Log::info("Sending to users with roles: " . json_encode($roleIds) . " - " . count($targetUsers) . " users");
            }
            
        } elseif ($announcement->target_type === 'users') {
            // Get specific users
            $userIds = json_decode($announcement->target_users, true);
            if (!empty($userIds)) {
                $targetUsers = DB::table('users')
                    ->whereIn('id', $userIds)
                    ->where('is_active', true)
                    ->select('id', 'name', 'email')
                    ->get();
                \Log::info("Sending to specific users: " . json_encode($userIds) . " - " . count($targetUsers) . " users");
            }
        }
        
        if ($targetUsers->isEmpty()) {
            \Log::warning("No target users found for announcement #{$announcementId}");
            return ['sent' => 0, 'failed' => 0, 'message' => 'No active recipients with an email address were found.'];
        }
        
        $sentCount = 0;
        $failedCount = 0;
        
        foreach ($targetUsers as $user) {
            if (empty($user->email)) {
                $failedCount++;
                continue;
            }
            try {
                // Send email using Laravel's mail system
                \Mail::send('emails.announcement', [
                    'user' => $user,
                    'announcement' => $announcement
                ], function ($message) use ($user, $announcement) {
                    $message->to($user->email, $user->name)
                            ->subject($announcement->title)
                            ->from(config('mail.from.address'), config('mail.from.name'));
                });
                $sentCount++;
                \Log::info("Email sent to: {$user->email}");
            } catch (\Exception $e) {
                $failedCount++;
                \Log::error("Failed to send email to {$user->email}: " . $e->getMessage());
            }
        }
        
        // Update email_sent flag
        DB::table('announcements')
            ->where('id', $announcementId)
            ->update([
                'email_sent' => $sentCount > 0,
                'email_sent_at' => $sentCount > 0 ? now() : null,
                'updated_at' => now(),
            ]);
        
        \Log::info("Announcement #{$announcementId} emails sent: {$sentCount} sent, {$failedCount} failed");
        return [
            'sent' => $sentCount,
            'failed' => $failedCount,
            'message' => $failedCount > 0
                ? "Message sent to {$sentCount} recipient(s); {$failedCount} delivery attempt(s) failed."
                : "Message sent to {$sentCount} recipient(s).",
        ];
        
    } catch (\Exception $e) {
        \Log::error("sendAnnouncementEmails error: " . $e->getMessage());
        return ['sent' => 0, 'failed' => 0, 'message' => 'The message could not be sent. Check the mail configuration and try again.'];
    }
}

public function resend($id)
{
    $announcement = DB::table('announcements')->where('id', $id)->first();
    if (!$announcement || $announcement->status !== 'active') {
        return response()->json(['success' => false, 'message' => 'Sent message not found.'], 404);
    }

    $delivery = $this->sendAnnouncementEmails($id, true);
    return response()->json([
        'success' => $delivery['sent'] > 0,
        'message' => $delivery['message'],
        'delivery' => $delivery,
    ], $delivery['sent'] > 0 ? 200 : 422);
}
   /**
 * Get recipients for an announcement (actual users who received/will receive it)
 */
public function getRecipients($id)
{
    try {
        $announcement = DB::table('announcements')->where('id', $id)->first();
        
        if (!$announcement) {
            return response()->json(['success' => false, 'message' => 'Announcement not found'], 404);
        }
        
        $recipients = [];
        
        if ($announcement->target_type === 'all') {
            $recipients = DB::table('users')
                ->where('is_active', true)
                ->select('id', 'name', 'email')
                ->orderBy('name')
                ->get();
        } elseif ($announcement->target_type === 'roles') {
            $roleIds = json_decode($announcement->target_roles, true);
            if (!empty($roleIds)) {
                $recipients = DB::table('users')
                    ->join('role_user', 'users.id', '=', 'role_user.user_id')
                    ->whereIn('role_user.role_id', $roleIds)
                    ->where('users.is_active', true)
                    ->select('users.id', 'users.name', 'users.email')
                    ->distinct()
                    ->orderBy('users.name')
                    ->get();
            }
        } elseif ($announcement->target_type === 'users') {
            $userIds = json_decode($announcement->target_users, true);
            if (!empty($userIds)) {
                $recipients = DB::table('users')
                    ->whereIn('id', $userIds)
                    ->where('is_active', true)
                    ->select('id', 'name', 'email')
                    ->orderBy('name')
                    ->get();
            }
        }
        
        return response()->json([
            'success' => true,
            'recipients' => $recipients,
            'count' => count($recipients)
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

/**
 * Get recipient counts and preview for multiple announcements
 */
public function getBatchRecipients(Request $request)
{
    try {
        $announcementIds = $request->input('announcement_ids', []);
        
        if (empty($announcementIds)) {
            return response()->json(['success' => true, 'recipients' => []]);
        }
        
        $results = [];
        
        foreach ($announcementIds as $id) {
            $announcement = DB::table('announcements')->where('id', $id)->first();
            
            if (!$announcement) {
                $results[$id] = ['count' => 0, 'preview' => [], 'type' => 'error'];
                continue;
            }
            
            $recipients = [];
            
            if ($announcement->target_type === 'all') {
                $recipients = DB::table('users')
                    ->where('is_active', true)
                    ->select('id', 'name', 'email')
                    ->orderBy('name')
                    ->limit(5)
                    ->get();
                $totalCount = DB::table('users')->where('is_active', true)->count();
            } elseif ($announcement->target_type === 'roles') {
                $roleIds = json_decode($announcement->target_roles, true);
                if (!empty($roleIds)) {
                    $recipients = DB::table('users')
                        ->join('role_user', 'users.id', '=', 'role_user.user_id')
                        ->whereIn('role_user.role_id', $roleIds)
                        ->where('users.is_active', true)
                        ->select('users.id', 'users.name', 'users.email')
                        ->distinct()
                        ->orderBy('users.name')
                        ->limit(5)
                        ->get();
                    $totalCount = DB::table('users')
                        ->join('role_user', 'users.id', '=', 'role_user.user_id')
                        ->whereIn('role_user.role_id', $roleIds)
                        ->where('users.is_active', true)
                        ->distinct()
                        ->count('users.id');
                } else {
                    $totalCount = 0;
                }
            } elseif ($announcement->target_type === 'users') {
                $userIds = json_decode($announcement->target_users, true);
                if (!empty($userIds)) {
                    $recipients = DB::table('users')
                        ->whereIn('id', $userIds)
                        ->where('is_active', true)
                        ->select('id', 'name', 'email')
                        ->orderBy('name')
                        ->limit(5)
                        ->get();
                    $totalCount = DB::table('users')
                        ->whereIn('id', $userIds)
                        ->where('is_active', true)
                        ->count();
                } else {
                    $totalCount = 0;
                }
            } else {
                $totalCount = 0;
            }
            
            $results[$id] = [
                'count' => $totalCount,
                'preview' => $recipients,
                'type' => $announcement->target_type
            ];
        }
        
        return response()->json([
            'success' => true,
            'recipients' => $results
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
} 
/**
 * Get role names by IDs for batch display
 */
public function getRolesBatch(Request $request)
{
    try {
        $roleIds = $request->input('role_ids', []);
        
        if (empty($roleIds)) {
            return response()->json(['success' => true, 'roles' => []]);
        }
        
        $roles = DB::table('roles')
            ->whereIn('id', $roleIds)
            ->select('id', 'name', 'display_name')
            ->get()
            ->keyBy('id');
        
        return response()->json([
            'success' => true,
            'roles' => $roles
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
   public function update(Request $request, $id)
{
    try {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'nullable|string',
            'status' => 'nullable|string',
            'scheduled_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'priority' => 'nullable|string',
            'target_audience' => 'nullable|string'
        ]);
        
        $updateData = [
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'] ?? 'general',
            'status' => $validated['status'] ?? 'draft',
            'scheduled_date' => $validated['scheduled_date'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'target_audience' => $validated['target_audience'] ?? null,
            'priority' => $validated['priority'] ?? 'normal',
            'updated_at' => now()
        ];
        
        if ($request->hasFile('image')) {
            // Delete old image
            $old = DB::table('announcements')->where('id', $id)->first();
            if ($old && $old->image_path && file_exists(public_path($old->image_path))) {
                unlink(public_path($old->image_path));
            }
            
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/announcements'), $filename);
            $updateData['image_path'] = 'uploads/announcements/' . $filename;
        }
        
        // Update published info if status changed to active
        if (($validated['status'] ?? '') === 'active') {
            $updateData['published_by'] = auth()->id();
            $updateData['published_at'] = now();
        }
        
        DB::table('announcements')->where('id', $id)->update($updateData);
        
        return response()->json(['success' => true, 'message' => 'Announcement updated successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
    
    public function edit($id)
    {
        try {
            $announcement = DB::table('announcements')->where('id', $id)->first();
            return response()->json(['success' => true, 'announcement' => $announcement]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $announcement = DB::table('announcements')->where('id', $id)->first();
            if ($announcement && $announcement->image_path && file_exists(public_path($announcement->image_path))) {
                unlink(public_path($announcement->image_path));
            }
            DB::table('announcements')->where('id', $id)->delete();
            
            return response()->json(['success' => true, 'message' => 'Announcement deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function toggleStatus($id)
    {
        try {
            $announcement = DB::table('announcements')->where('id', $id)->first();
            $newStatus = $announcement->status === 'active' ? 'draft' : 'active';
            
            $updateData = ['status' => $newStatus, 'updated_at' => now()];
            
            if ($newStatus === 'active') {
                $updateData['published_by'] = auth()->id();
                $updateData['published_at'] = now();
            }
            
            DB::table('announcements')->where('id', $id)->update($updateData);
            
            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function getStats()
    {
        try {
            $stats = [
                'total' => DB::table('announcements')->count(),
                'active' => DB::table('announcements')->where('status', 'active')->count(),
                'scheduled' => DB::table('announcements')
                    ->where('status', 'scheduled')
                    ->orWhere('scheduled_date', '>', now())
                    ->count(),
                'expired' => DB::table('announcements')
                    ->where('status', 'expired')
                    ->orWhere('expiry_date', '<', now())
                    ->count(),
                'draft' => DB::table('announcements')->where('status', 'draft')->count(),
            ];
            
            return response()->json(['success' => true, 'stats' => $stats]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

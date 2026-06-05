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
    
    public function filter(Request $request)
    {
        try {
            $type = $request->type;
            $status = $request->status;
            $search = $request->search;
            
            $query = DB::table('announcements')
                ->leftJoin('users as creators', 'announcements.created_by', '=', 'creators.id')
                ->select('announcements.*', 'creators.name as created_by_name');
            
            if ($type && $type !== 'all') {
                $query->where('announcements.type', $type);
            }
            
            if ($status && $status !== 'all') {
                $query->where('announcements.status', $status);
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
            'type' => 'nullable|string',
            'status' => 'nullable|string',
            'scheduled_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:scheduled_date',
            'priority' => 'nullable|string',
            'target_audience' => 'nullable|string'
        ]);
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/announcements'), $filename);
            $imagePath = 'uploads/announcements/' . $filename;
        }
        
        $status = $validated['status'] ?? 'draft';
        $publishedBy = null;
        $publishedAt = null;
        
        if ($status === 'active') {
            $publishedBy = auth()->id();
            $publishedAt = now();
        }
        
        $id = DB::table('announcements')->insertGetId([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'] ?? 'general',
            'status' => $status,
            'scheduled_date' => $validated['scheduled_date'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'target_audience' => $validated['target_audience'] ?? null,
            'priority' => $validated['priority'] ?? 'normal',
            'image_path' => $imagePath,
            'created_by' => auth()->id(),
            'published_by' => $publishedBy,
            'published_at' => $publishedAt,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true, 'message' => 'Announcement created successfully', 'id' => $id]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
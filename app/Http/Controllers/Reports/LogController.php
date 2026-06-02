<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System\ActivityLog;
use App\Models\System\ErrorLog;
use App\Models\User\User;
class LogController extends Controller
{
    // Display activity logs
    public function activityLogs(Request $request)
    {
        $query = ActivityLog::with('user');
        
        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by action
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Search in description
        if ($request->has('search') && $request->search) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        
        $logs = $query->orderBy('created_at', 'desc')->paginate(20);
        $users = User::all();
        $actions = ActivityLog::select('action')->distinct()->pluck('action');
        
        return view('super-admin.logs.activity', compact('logs', 'users', 'actions'));
    }
    
    // Display error logs
    public function errorLogs(Request $request)
    {
        $query = ErrorLog::with('user');
        
        // Filter by error type
        if ($request->has('error_type') && $request->error_type) {
            $query->where('error_type', $request->error_type);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Search in message
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('message', 'like', '%' . $request->search . '%')
                  ->orWhere('file_path', 'like', '%' . $request->search . '%');
            });
        }
        
        $logs = $query->orderBy('created_at', 'desc')->paginate(20);
        $errorTypes = ErrorLog::select('error_type')->distinct()->pluck('error_type');
        
        return view('super-admin.logs.errors', compact('logs', 'errorTypes'));
    }
    
    // View single activity log
    public function viewActivity($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);
        return view('super-admin.logs.view-activity', compact('log'));
    }
    
    // View single error log
    public function viewError($id)
    {
        $log = ErrorLog::with('user')->findOrFail($id);
        return view('super-admin.logs.view-error', compact('log'));
    }
    
    // Clear activity logs
    public function clearActivityLogs(Request $request)
    {
        $count = ActivityLog::count();
        ActivityLog::truncate();
        
        // Log this action
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'logs_cleared',
            'description' => 'Cleared all activity logs. Total removed: ' . $count,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return redirect()->route('logs.activity')->with('success', 'Activity logs cleared successfully! Removed ' . $count . ' records.');
    }
    
    // Clear error logs
    public function clearErrorLogs(Request $request)
    {
        $count = ErrorLog::count();
        ErrorLog::truncate();
        
        // Log this action
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'logs_cleared',
            'description' => 'Cleared all error logs. Total removed: ' . $count,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return redirect()->route('logs.errors')->with('success', 'Error logs cleared successfully! Removed ' . $count . ' records.');
    }
    
    // Export activity logs
    public function exportActivityLogs(Request $request)
    {
        $query = ActivityLog::with('user');
        
        // Apply same filters
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'activity_logs_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        // Add headers
        fputcsv($handle, ['ID', 'User', 'Email', 'Action', 'Description', 'IP Address', 'User Agent', 'Timestamp']);
        
        // Add data
        foreach ($logs as $log) {
            fputcsv($handle, [
                $log->id,
                $log->user ? $log->user->name : 'System',
                $log->user ? $log->user->email : 'N/A',
                $log->action,
                $log->description,
                $log->ip_address,
                $log->user_agent,
                $log->created_at
            ]);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    // Dashboard stats for logs
    public function stats()
    {
        $stats = [
            'total_activities' => ActivityLog::count(),
            'total_errors' => ErrorLog::count(),
            'today_activities' => ActivityLog::whereDate('created_at', today())->count(),
            'today_errors' => ErrorLog::whereDate('created_at', today())->count(),
            'recent_activities' => ActivityLog::with('user')->orderBy('created_at', 'desc')->limit(10)->get(),
            'recent_errors' => ErrorLog::with('user')->orderBy('created_at', 'desc')->limit(10)->get(),
            'top_actions' => ActivityLog::select('action', \DB::raw('count(*) as total'))
                ->groupBy('action')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get(),
            'error_by_type' => ErrorLog::select('error_type', \DB::raw('count(*) as total'))
                ->groupBy('error_type')
                ->orderBy('total', 'desc')
                ->get()
        ];
        
        return response()->json($stats);
    }
}
<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User\User;

class ReportController extends Controller
{
    public function index()
    {
        $stats = $this->getStats();
        
        // Get event reports for display
        $eventReports = DB::table('event_reports')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('modules.reports.index', compact('stats', 'eventReports'));
    }
    
    private function getStats()
    {
        try {
            return [
                'total_action_plans' => DB::table('action_plans')->count(),
                'completed_action_plans' => DB::table('action_plans')->where('status', 'completed')->count(),
                'total_discipline' => DB::table('discipline_records')->count(),
                'total_permissions' => DB::table('permission_requests')->count(),
                'total_events' => DB::table('event_reports')->count(),
                'total_users' => DB::table('users')->count(),
                'total_attendance' => DB::table('attendance_records')->count(),
                'total_forms' => DB::table('spiritual_forms')->count(),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }
    
    // ==================== ACTION PLANS REPORT ====================
    public function actionPlansReport(Request $request)
    {
        try {
            $status = $request->status;
            $department = $request->department;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            $query = DB::table('action_plans')
                ->leftJoin('users', 'action_plans.assigned_to', '=', 'users.id')
                ->select('action_plans.*', 'users.name as assigned_to_name');
            
            if ($status && $status !== 'all') {
                $query->where('action_plans.status', $status);
            }
            if ($department && $department !== 'all') {
                $query->where('action_plans.department', $department);
            }
            if ($startDate) {
                $query->whereDate('action_plans.created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('action_plans.created_at', '<=', $endDate);
            }
            
            $reports = $query->orderBy('action_plans.created_at', 'desc')->get();
            
            $summary = [
                'total' => $reports->count(),
                'completed' => $reports->where('status', 'completed')->count(),
                'in_progress' => $reports->where('status', 'in-progress')->count(),
                'pending' => $reports->where('status', 'pending')->count(),
                'avg_progress' => $reports->avg('progress') ?? 0,
            ];
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'reports' => $reports, 'summary' => $summary]);
            }
            
            return view('modules.reports.partials.action-plans-tab', compact('reports', 'summary'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // ==================== DISCIPLINE REPORT ====================
    public function disciplineReport(Request $request)
    {
        try {
            $type = $request->type;
            $status = $request->status;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            $query = DB::table('discipline_records')
                ->leftJoin('users', 'discipline_records.user_id', '=', 'users.id')
                ->select('discipline_records.*', 'users.name as user_name');
            
            if ($type && $type !== 'all') {
                $query->where('discipline_records.type', $type);
            }
            if ($status && $status !== 'all') {
                $query->where('discipline_records.status', $status);
            }
            if ($startDate) {
                $query->whereDate('discipline_records.created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('discipline_records.created_at', '<=', $endDate);
            }
            
            $reports = $query->orderBy('discipline_records.created_at', 'desc')->get();
            
            $summary = [
                'total' => $reports->count(),
                'positive' => $reports->where('type', 'positive')->count(),
                'warning' => $reports->where('type', 'warning')->count(),
                'penalty' => $reports->where('type', 'penalty')->count(),
                'suspension' => $reports->where('type', 'suspension')->count(),
                'resolved' => $reports->where('status', 'resolved')->count(),
                'active' => $reports->where('status', 'active')->count(),
            ];
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'reports' => $reports, 'summary' => $summary]);
            }
            
            return view('modules.reports.partials.discipline-tab', compact('reports', 'summary'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // ==================== PERMISSION REPORT ====================
    public function permissionReport(Request $request)
    {
        try {
            $status = $request->status;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            $query = DB::table('permission_requests')
                ->leftJoin('users', 'permission_requests.user_id', '=', 'users.id')
                ->select('permission_requests.*', 'users.name as user_name');
            
            if ($status && $status !== 'all') {
                $query->where('permission_requests.status', $status);
            }
            if ($startDate) {
                $query->whereDate('permission_requests.created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('permission_requests.created_at', '<=', $endDate);
            }
            
            $reports = $query->orderBy('permission_requests.created_at', 'desc')->get();
            
            $summary = [
                'total' => $reports->count(),
                'approved' => $reports->where('status', 'approved')->count(),
                'pending' => $reports->where('status', 'pending')->count(),
                'rejected' => $reports->where('status', 'rejected')->count(),
                'approval_rate' => $reports->count() > 0 ? round(($reports->where('status', 'approved')->count() / $reports->count()) * 100, 1) : 0,
            ];
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'reports' => $reports, 'summary' => $summary]);
            }
            
            return view('modules.reports.partials.permission-tab', compact('reports', 'summary'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // ==================== EVENTS REPORT ====================
    public function eventsReport(Request $request)
    {
        try {
            $status = $request->status;
            $category = $request->category;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            $query = DB::table('event_reports')
                ->leftJoin('users', 'event_reports.created_by', '=', 'users.id')
                ->select('event_reports.*', 'users.name as created_by_name');
            
            if ($status && $status !== 'all') {
                $query->where('event_reports.status', $status);
            }
            if ($category && $category !== 'all') {
                $query->where('event_reports.category', $category);
            }
            if ($startDate) {
                $query->whereDate('event_reports.start_date', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('event_reports.end_date', '<=', $endDate);
            }
            
            $reports = $query->orderBy('event_reports.event_date', 'desc')->get();
            
            $summary = [
                'total' => $reports->count(),
                'completed' => $reports->where('status', 'completed')->count(),
                'planned' => $reports->where('status', 'planned')->count(),
                'ongoing' => $reports->where('status', 'ongoing')->count(),
                'total_participants' => $reports->sum('participants_count'),
                'total_budget' => $reports->sum('budget'),
            ];
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'reports' => $reports, 'summary' => $summary]);
            }
            
            return view('modules.reports.partials.events-tab', compact('reports', 'summary'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // ==================== USERS REPORT ====================
    public function usersReport(Request $request)
    {
        try {
            $role = $request->role;
            $status = $request->status;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            $query = DB::table('users')
                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->leftJoin('roles', 'role_user.role_id', '=', 'roles.id')
                ->select('users.*', DB::raw('STRING_AGG(roles.display_name, \', \') as roles_list'))
                ->groupBy('users.id');
            
            if ($role && $role !== 'all') {
                $query->whereHas('roles', function($q) use ($role) {
                    $q->where('roles.id', $role);
                });
            }
            if ($status && $status !== 'all') {
                $query->where('users.is_active', $status === 'active');
            }
            if ($startDate) {
                $query->whereDate('users.created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('users.created_at', '<=', $endDate);
            }
            
            $reports = $query->orderBy('users.created_at', 'desc')->get();
            
            $summary = [
                'total' => $reports->count(),
                'active' => $reports->where('is_active', true)->count(),
                'inactive' => $reports->where('is_active', false)->count(),
                'male' => $reports->where('gender', 'Male')->count(),
                'female' => $reports->where('gender', 'Female')->count(),
            ];
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'reports' => $reports, 'summary' => $summary]);
            }
            
            return view('modules.reports.partials.users-tab', compact('reports', 'summary'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // ==================== ATTENDANCE REPORT ====================
    public function attendanceReport(Request $request)
    {
        try {
            $status = $request->status;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $sessionType = $request->session_type;
            
            $query = DB::table('attendance_records')
                ->leftJoin('users', 'attendance_records.user_id', '=', 'users.id')
                ->select('attendance_records.*', 'users.name as user_name');
            
            if ($status && $status !== 'all') {
                $query->where('attendance_records.status', $status);
            }
            if ($sessionType) {
                $query->where('attendance_records.session_type', $sessionType);
            }
            if ($startDate) {
                $query->whereDate('attendance_records.session_date', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('attendance_records.session_date', '<=', $endDate);
            }
            
            $reports = $query->orderBy('attendance_records.session_date', 'desc')->get();
            
            $summary = [
                'total' => $reports->count(),
                'present' => $reports->where('status', 'present')->count(),
                'absent' => $reports->where('status', 'absent')->count(),
                'late' => $reports->where('status', 'late')->count(),
                'excused' => $reports->where('status', 'excused')->count(),
                'attendance_rate' => $reports->count() > 0 ? round(($reports->where('status', 'present')->count() / $reports->count()) * 100, 1) : 0,
            ];
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'reports' => $reports, 'summary' => $summary]);
            }
            
            return view('modules.reports.partials.attendance-tab', compact('reports', 'summary'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // ==================== FINANCIAL REPORT ====================
    public function financialReport(Request $request)
    {
        try {
            $year = $request->year ?? date('Y');
            $type = $request->type ?? 'all';
            
            $contributions = DB::table('contributions')
                ->where('year', $year)
                ->sum('annual_amount');
            
            $collected = DB::table('payments')
                ->where('year', $year)
                ->sum('amount');
            
            $sponsorCommitments = DB::table('sponsors')->sum('commitment_amount');
            $sponsorReceived = DB::table('sponsor_payments')->sum('amount');
            $giftCommitments = DB::table('gifts')->sum('commitment_amount');
            $giftReceived = DB::table('gifts')->sum('received_amount');
            $expenses = DB::table('expenses')->sum('amount');
            
            $summary = [
                'total_expected' => $contributions + $sponsorCommitments + $giftCommitments,
                'total_received' => $collected + $sponsorReceived + $giftReceived,
                'total_expenses' => $expenses,
                'net_balance' => ($collected + $sponsorReceived + $giftReceived) - $expenses,
                'collection_rate' => $contributions > 0 ? round(($collected / $contributions) * 100, 1) : 0,
            ];
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'summary' => $summary]);
            }
            
            return view('modules.reports.partials.financial-tab', compact('summary', 'year'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // ==================== FORMS REPORT ====================
    public function formsReport(Request $request)
    {
        try {
            $formId = $request->form_id;
            $status = $request->status;
            
            $query = DB::table('form_submissions')
                ->leftJoin('spiritual_forms', 'form_submissions.form_id', '=', 'spiritual_forms.id')
                ->leftJoin('users', 'form_submissions.user_id', '=', 'users.id')
                ->select('form_submissions.*', 'spiritual_forms.title as form_title', 'users.name as user_name');
            
            if ($formId && $formId !== 'all') {
                $query->where('form_submissions.form_id', $formId);
            }
            
            $reports = $query->orderBy('form_submissions.created_at', 'desc')->get();
            
            $forms = DB::table('spiritual_forms')->select('id', 'title')->get();
            
            $summary = [
                'total' => $reports->count(),
                'avg_score' => $reports->avg('score') ?? 0,
            ];
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'reports' => $reports, 'summary' => $summary]);
            }
            
            return view('modules.reports.partials.forms-tab', compact('reports', 'summary', 'forms'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // ==================== EXPORT FUNCTIONS ====================
    public function export(Request $request)
{
    try {
        $validated = $request->validate([
            'type' => 'required|string',
            'format' => 'required|string|in:csv,pdf',
            'data' => 'nullable|array'
        ]);
        
        $type = $validated['type'];
        $format = $validated['format'];
        $data = $validated['data'] ?? [];
        
        $filename = "{$type}_report_" . date('Y-m-d_His');
        
        if ($format === 'csv') {
            return $this->exportCSV($data, $filename);
        } else {
            return $this->exportPDF($data, $filename);
        }
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
    
    private function exportCSV($data, $filename)
    {
        if (empty($data)) {
            return response()->json(['success' => false, 'message' => 'No data to export'], 404);
        }
        
        $headers = array_keys((array)$data[0]);
        $callback = function() use ($data, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($data as $row) {
                fputcsv($file, (array)$row);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}.csv"
        ]);
    }
    
    private function exportPDF($data, $filename)
    {
        // For PDF, you can use DomPDF or similar
        $html = view('modules.reports.export-pdf', compact('data'))->render();
        
        // Return as download
        return response()->json(['success' => true, 'message' => 'PDF export ready']);
    }
    
    // Create/Store Event Report
    public function storeEvent(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'event_date' => 'required|date',
                'category' => 'nullable|string',
                'status' => 'nullable|string'
            ]);
            
            $id = DB::table('event_reports')->insertGetId([
                'title' => $request->title,
                'description' => $request->description,
                'event_date' => $request->event_date,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status ?? 'planned',
                'category' => $request->category,
                'location' => $request->location,
                'organizer' => $request->organizer,
                'participants_count' => $request->participants_count ?? 0,
                'budget' => $request->budget ?? 0,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Event created successfully', 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function superAdminDashboard()
    {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('user.dashboard');
        }

        // User Statistics
        $totalUsers = DB::table('users')->count();
        $activeUsers = DB::table('users')->where('is_active', true)->count();
        $inactiveUsers = DB::table('users')->where('is_active', false)->whereNotNull('created_by')->count();
        $pendingUsers = DB::table('users')
            ->where('is_active', false)
            ->whereNull('created_by')
            ->whereNull('email_verified_at')
            ->count();
        $lastMonthUsers = DB::table('users')->whereMonth('created_at', now()->subMonth()->month)->count();
        $newUsersMonth = DB::table('users')->whereMonth('created_at', now()->month)->count();
        $growthRate = $lastMonthUsers > 0 ? round((($totalUsers - $lastMonthUsers) / $lastMonthUsers) * 100, 1) : 0;
        $pendingPermissionRequests = DB::table('permission_requests')->where('status', 'pending')->count();
        
        // Online users (sessions)
        $onlineUsers = DB::table('sessions')->where('last_activity', '>=', now()->subMinutes(15)->timestamp)->count();
        
        // Department statistics
        $stats = [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'inactive_users' => $inactiveUsers,
            'pending_users' => $pendingUsers,
            'last_month_users' => $lastMonthUsers,
            'new_users_month' => $newUsersMonth,
            'growth_rate' => $growthRate,
            'online_users' => $onlineUsers,
            'total_roles' => DB::table('roles')->count(),
            'total_pages' => DB::table('pages')->count(),
            'total_families' => DB::table('families')->count(),
            'total_members' => DB::table('family_members')->count(),
            'system_version' => '2.0.0',
            
            // Department activity (based on data presence)
            'music_activity' => $this->calculateDepartmentActivity('music'),
            'intercession_activity' => $this->calculateDepartmentActivity('intercession'),
            'social_activity' => $this->calculateDepartmentActivity('social-fellowship'),
            'discipline_activity' => $this->calculateDepartmentActivity('discipline'),
            'finance_activity' => $this->calculateDepartmentActivity('finance'),
            
            // System-wide statistics
            'total_forms' => DB::table('spiritual_forms')->count() ?? 0,
            'total_devotions' => DB::table('devotions')->count() ?? 0,
            'total_songs' => DB::table('songs')->count() ?? 0,
            'total_playlists' => DB::table('playlists')->count() ?? 0,
            'total_sponsors' => $this->tableCount('sponsors'),
            'total_announcements' => $this->tableCount('announcements'),
            'total_discipline' => DB::table('discipline_records')->count() ?? 0,
            'total_permissions' => DB::table('permission_requests')->count() ?? 0,
            'pending_permission_requests' => $pendingPermissionRequests,
            'total_payment_records' => DB::table('payments')->count() ?? 0,
            'total_expense_records' => DB::table('expenses')->count() ?? 0,
            
            // Financial statistics
            'total_expected' => DB::table('contributions')->sum('annual_amount') ?? 0,
            'total_collected' => DB::table('payments')->sum('amount') ?? 0,
            'total_expenses' => DB::table('expenses')->sum('amount') ?? 0,
        ];
        
        // Calculate collection rate
        if ($stats['total_expected'] > 0) {
            $stats['collection_rate'] = round(($stats['total_collected'] / $stats['total_expected']) * 100, 1);
        } else {
            $stats['collection_rate'] = 0;
        }
        
        // Recent activities
        $recentActivities = $this->getRecentActivities();
        
        return view('super-admin.dashboard', compact('stats', 'recentActivities'));
    }
    
    private function calculateDepartmentActivity($department)
    {
        // Calculate activity based on recent data in department tables
        $activity = 70; // Default baseline
        
        switch($department) {
            case 'music':
                $songCount = DB::table('songs')->whereMonth('created_at', now()->month)->count();
                $activity = min(100, 70 + ($songCount * 5));
                break;
            case 'intercession':
                $devotionCount = DB::table('user_devotion_completions')->whereMonth('completed_at', now()->month)->count();
                $activity = min(100, 70 + ($devotionCount * 2));
                break;
            case 'social-fellowship':
                $familyCount = DB::table('families')->whereMonth('created_at', now()->month)->count();
                $activity = min(100, 70 + ($familyCount * 10));
                break;
            case 'discipline':
                $recordCount = DB::table('discipline_records')->whereMonth('created_at', now()->month)->count();
                $activity = min(100, 70 + ($recordCount * 5));
                break;
            case 'finance':
                $paymentCount = DB::table('payments')->whereMonth('created_at', now()->month)->count();
                $activity = min(100, 70 + ($paymentCount * 3));
                break;
        }
        
        return $activity;
    }
    
    private function getRecentActivities()
    {
        $activities = [];
        
        // Get recent user registrations
        $newUsers = DB::table('users')->orderBy('created_at', 'desc')->limit(3)->get();
        foreach ($newUsers as $user) {
            $activities[] = (object)[
                'description' => "New user registered: {$user->name}",
                'created_at' => $user->created_at,
                'icon' => 'fas fa-user-plus',
                'icon_bg' => 'bg-green-100',
                'icon_color' => 'text-green-600',
                'module' => 'Users'
            ];
        }
        
        // Get recent contributions
        $payments = DB::table('payments')->orderBy('created_at', 'desc')->limit(2)->get();
        foreach ($payments as $payment) {
            $user = DB::table('users')->where('id', $payment->user_id)->first();
            $activities[] = (object)[
                'description' => "Contribution recorded: " . ($user->name ?? 'Unknown') . " - " . number_format($payment->amount) . " RWF",
                'created_at' => $payment->created_at,
                'icon' => 'fas fa-hand-holding-usd',
                'icon_bg' => 'bg-blue-100',
                'icon_color' => 'text-blue-600',
                'module' => 'Finance'
            ];
        }
        
        // Sort by created_at descending
        usort($activities, function($a, $b) {
            return strtotime($b->created_at) - strtotime($a->created_at);
        });
        
        return array_slice($activities, 0, 10);
    }

    private function tableCount(string $table): int
    {
        return Schema::hasTable($table) ? DB::table($table)->count() : 0;
    }
    
    public function adminDashboard()
    {
        return redirect()->route(
            auth()->user()->isSuperAdmin() ? 'super-admin.dashboard' : 'user.dashboard'
        );
    }
}

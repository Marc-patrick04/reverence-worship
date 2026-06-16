<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User\User;
use App\Models\User\Role;

class UserReportController extends Controller
{
    /**
     * Get user report data for charts and statistics
     */
    public function getReport(Request $request)
    {
        try {
            $startDate = $request->get('start_date', date('Y-m-01', strtotime('-11 months')));
            $endDate = $request->get('end_date', date('Y-m-t'));
            
            // Get user statistics
            $totalUsers = User::count();
            $activeUsers = User::where('is_active', true)->count();
            $inactiveUsers = User::where('is_active', false)->whereNotNull('created_by')->count();
            $pendingUsers = User::where('is_active', false)->whereNull('created_by')->whereNull('email_verified_at')->count();
            $newThisMonth = User::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
            
            // Gender stats
            $maleCount = User::whereRaw('LOWER(gender) = ?', ['male'])->count();
            $femaleCount = User::whereRaw('LOWER(gender) = ?', ['female'])->count();
            $unspecifiedCount = User::whereNull('gender')->orWhere('gender', '')->count();
            
            // Top roles
            $topRoles = DB::table('roles')
                ->leftJoin('role_user', 'roles.id', '=', 'role_user.role_id')
                ->where('roles.name', '!=', 'super-admin')
                ->select('roles.id', 'roles.name', 'roles.display_name', DB::raw('COUNT(role_user.user_id) as count'))
                ->groupBy('roles.id', 'roles.name', 'roles.display_name')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();
            
            // Recent users
            $recentUsers = User::with('roles')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->roles->first()->display_name ?? 'No Role',
                        'is_active' => $user->is_active,
                        'created_at' => $user->created_at
                    ];
                });
            
            // Registration trend data
            $months = [];
            $counts = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $monthName = date('M Y', strtotime("-$i months"));
                $year = date('Y', strtotime("-$i months"));
                $month = date('m', strtotime("-$i months"));
                $count = User::whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
                
                $months[] = $monthName;
                $counts[] = $count;
            }
            
            return response()->json([
                'success' => true,
                'stats' => [
                    'total' => $totalUsers,
                    'active' => $activeUsers,
                    'inactive' => $inactiveUsers,
                    'pending' => $pendingUsers,
                    'newThisMonth' => $newThisMonth,
                    'male' => $maleCount,
                    'female' => $femaleCount,
                    'unspecified' => $unspecifiedCount
                ],
                'topRoles' => $topRoles,
                'recentUsers' => $recentUsers,
                'registrationData' => [
                    'months' => $months,
                    'counts' => $counts
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('UserReportController error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export user report
     */
    public function exportReport(Request $request)
    {
        try {
            $startDate = $request->get('start_date', date('Y-m-01', strtotime('-11 months')));
            $endDate = $request->get('end_date', date('Y-m-t'));
            $format = $request->get('format', 'csv');
            
            $users = User::with('roles')
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            $filename = 'users_report_' . date('Y-m-d_His') . '.csv';
            $handle = fopen('php://temp', 'w+');
            
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Name', 'Email', 'Phone', 'Role', 'Status', 'Gender', 'Registered Date']);
            
            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->name,
                    $user->email,
                    $user->phone ?? '-',
                    $user->roles->first()->display_name ?? 'No Role',
                    $user->is_active ? 'Active' : 'Inactive',
                    $user->gender ?? '-',
                    $user->created_at ? $user->created_at->format('Y-m-d') : '-'
                ]);
            }
            
            rewind($handle);
            $csv = stream_get_contents($handle);
            fclose($handle);
            
            return response($csv, 200)
                ->header('Content-Type', 'text/csv; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            \Log::error('Export report error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function getSummary()
    {
        try {
            $stats = [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'inactive' => User::where('is_active', false)->whereNotNull('created_by')->count(),
                'pending' => User::where('is_active', false)->whereNull('created_by')->whereNull('email_verified_at')->count(),
                'male' => User::whereRaw('LOWER(gender) = ?', ['male'])->count(),
                'female' => User::whereRaw('LOWER(gender) = ?', ['female'])->count(),
            ];
            
            return response()->json(['success' => true, 'stats' => $stats]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    /**
 * Get all roles for filter
 */
public function getRoles()
{
    try {
        $roles = DB::table('roles')
            ->where('name', '!=', 'super-admin')
            ->select('id', 'name', 'display_name')
            ->get();
        
        return response()->json(['success' => true, 'roles' => $roles]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
}
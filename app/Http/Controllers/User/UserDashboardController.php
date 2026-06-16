<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User\User;
use App\Models\User\Role;
use App\Models\System\Page;
use App\Models\System\Feature;
use App\Models\User\RolePageFeature;

class UserDashboardController extends Controller
{
    public function index()
{
    $user = auth()->user();
    
    // Get user's accessible pages
    $accessiblePages = $user->getAccessiblePages();
    
    // Get user's module assignments
    $userModules = $this->getUserModules($user->id);
    
    // Dashboard stats
    $stats = $this->getUserDashboardStats($user, $userModules);
    
    // Recent activities
    $recentActivities = $this->getUserRecentActivities($user);
    
    // Quick links
    $quickLinks = $this->getUserQuickLinks($user, $accessiblePages);
    
    // Personal info
    $personalStats = $this->getPersonalStats($user);
    
    return view('user.dashboard', compact(
        'stats', 
        'recentActivities', 
        'quickLinks', 
        'personalStats',
        'userModules',
        'accessiblePages'  // Make sure this is passed
    ));
}
    
    private function getUserModules($userId)
    {
        try {
            // Check if table exists
            $tableExists = DB::select("SELECT to_regclass('user_module_assignments')");
            
            if (!$tableExists[0]->to_regclass) {
                return [];
            }
            
            $modules = DB::table('user_module_assignments')
                ->where('user_id', $userId)
                ->pluck('module_name')
                ->toArray();
            
            return $modules;
        } catch (\Exception $e) {
            return [];
        }
    }
    
    private function getAccessiblePages($user)
    {
        if ($user->isSuperAdmin()) {
            return Page::where('is_active', true)->orderBy('sort_order')->get();
        }
        
        try {
            // Get pages that user has access to via roles
            $roleIds = $user->roles->pluck('id')->toArray();
            
            if (empty($roleIds)) {
                return collect();
            }
            
            $pageIds = RolePageFeature::whereIn('role_id', $roleIds)
                ->pluck('page_id')
                ->unique()
                ->toArray();
            
            return Page::whereIn('id', $pageIds)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }
    
    private function getUserDashboardStats($user, $userModules)
    {
        $stats = [];
        
        // Check if user has finance access
        if (in_array('finance-view', $userModules) || $user->isSuperAdmin()) {
            try {
                // Check if contributions table exists
                $contributionsTable = DB::select("SELECT to_regclass('contributions')");
                if ($contributionsTable[0]->to_regclass) {
                    $stats['my_contributions'] = DB::table('contributions')
                        ->where('user_id', $user->id)
                        ->sum('annual_amount') ?? 0;
                } else {
                    $stats['my_contributions'] = 0;
                }
                
                // Check if payments table exists
                $paymentsTable = DB::select("SELECT to_regclass('payments')");
                if ($paymentsTable[0]->to_regclass) {
                    $stats['my_payments'] = DB::table('payments')
                        ->where('user_id', $user->id)
                        ->sum('amount') ?? 0;
                } else {
                    $stats['my_payments'] = 0;
                }
                
                $stats['payment_progress'] = $stats['my_contributions'] > 0 
                    ? round(($stats['my_payments'] / $stats['my_contributions']) * 100, 1) 
                    : 0;
            } catch (\Exception $e) {
                $stats['my_contributions'] = 0;
                $stats['my_payments'] = 0;
                $stats['payment_progress'] = 0;
            }
        }
        
        // Check if user has intercession access
        if (in_array('intercession-view', $userModules) || $user->isSuperAdmin()) {
            try {
                $prayerTable = DB::select("SELECT to_regclass('prayer_requests')");
                if ($prayerTable[0]->to_regclass) {
                    $stats['user_prayer_requests'] = DB::table('prayer_requests')
                        ->where('user_id', $user->id)
                        ->count();
                    
                    $stats['answered_prayers'] = DB::table('prayer_requests')
                        ->where('status', 'answered')
                        ->count();
                } else {
                    $stats['user_prayer_requests'] = 0;
                    $stats['answered_prayers'] = 0;
                }
            } catch (\Exception $e) {
                $stats['user_prayer_requests'] = 0;
                $stats['answered_prayers'] = 0;
            }
        }
        
        // Check if user has music access
        if (in_array('music-view', $userModules) || $user->isSuperAdmin()) {
            try {
                $songsTable = DB::select("SELECT to_regclass('songs')");
                if ($songsTable[0]->to_regclass) {
                    $stats['total_songs'] = DB::table('songs')->count();
                    $stats['user_contributions_music'] = DB::table('song_contributions')
                        ->where('user_id', $user->id)
                        ->count() ?? 0;
                } else {
                    $stats['total_songs'] = 0;
                    $stats['user_contributions_music'] = 0;
                }
            } catch (\Exception $e) {
                $stats['total_songs'] = 0;
                $stats['user_contributions_music'] = 0;
            }
        }
        
        // Check if user has fellowship access
        if (in_array('fellowship-view', $userModules) || $user->isSuperAdmin()) {
            try {
                $eventsTable = DB::select("SELECT to_regclass('events')");
                if ($eventsTable[0]->to_regclass) {
                    $stats['upcoming_events'] = DB::table('events')
                        ->where('event_date', '>=', now())
                        ->count();
                    $stats['user_event_attendance'] = DB::table('event_attendees')
                        ->where('user_id', $user->id)
                        ->count() ?? 0;
                } else {
                    $stats['upcoming_events'] = 0;
                    $stats['user_event_attendance'] = 0;
                }
            } catch (\Exception $e) {
                $stats['upcoming_events'] = 0;
                $stats['user_event_attendance'] = 0;
            }
        }
        
        // Get announcements
        try {
            $announcementsTable = DB::select("SELECT to_regclass('announcements')");
            if ($announcementsTable[0]->to_regclass) {
                $stats['recent_announcements'] = DB::table('announcements')
                    ->where('is_active', true)
                    ->where('published_at', '<=', now())
                    ->orderBy('published_at', 'desc')
                    ->limit(5)
                    ->get();
                
                $stats['unread_announcements'] = DB::table('announcement_reads')
                    ->where('user_id', $user->id)
                    ->where('is_read', false)
                    ->count() ?? 0;
            } else {
                $stats['recent_announcements'] = collect();
                $stats['unread_announcements'] = 0;
            }
        } catch (\Exception $e) {
            $stats['recent_announcements'] = collect();
            $stats['unread_announcements'] = 0;
        }
        
        // Global stats
        $stats['total_users'] = DB::table('users')->count();
        $stats['active_users'] = DB::table('users')->where('is_active', true)->count();
        $stats['online_users'] = $this->getOnlineUsersCount();
        
        return $stats;
    }
    
    private function getOnlineUsersCount()
    {
        try {
            // Check if sessions table exists
            $sessionsTable = DB::select("SELECT to_regclass('sessions')");
            if (!$sessionsTable[0]->to_regclass) {
                return 0;
            }
            
            return DB::table('sessions')
                ->where('last_activity', '>=', time() - 300)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function getUserRecentActivities($user)
    {
        try {
            $activities = collect();
            
            // Payment activities
            $paymentsTable = DB::select("SELECT to_regclass('payments')");
            if ($paymentsTable[0]->to_regclass) {
                $payments = DB::table('payments')
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function($item) {
                        $item->description = "Payment of RWF " . number_format($item->amount, 2) . " recorded";
                        $item->icon = "fas fa-credit-card";
                        $item->icon_bg = "bg-green-100";
                        $item->icon_color = "text-green-600";
                        $item->module = "Finance";
                        return $item;
                    });
                $activities = $activities->concat($payments);
            }
            
            // Prayer request activities
            $prayerTable = DB::select("SELECT to_regclass('prayer_requests')");
            if ($prayerTable[0]->to_regclass) {
                $prayerRequests = DB::table('prayer_requests')
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function($item) {
                        $item->description = "Prayer request: " . substr($item->title ?? 'Untitled', 0, 50);
                        $item->icon = "fas fa-pray";
                        $item->icon_bg = "bg-purple-100";
                        $item->icon_color = "text-purple-600";
                        $item->module = "Intercession";
                        return $item;
                    });
                $activities = $activities->concat($prayerRequests);
            }
            
            // Event attendance
            $eventsTable = DB::select("SELECT to_regclass('event_attendees')");
            if ($eventsTable[0]->to_regclass) {
                $events = DB::table('event_attendees')
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function($item) {
                        $item->description = "Registered for an event";
                        $item->icon = "fas fa-calendar";
                        $item->icon_bg = "bg-yellow-100";
                        $item->icon_color = "text-yellow-600";
                        $item->module = "Fellowship";
                        return $item;
                    });
                $activities = $activities->concat($events);
            }
            
            $activities = $activities->sortByDesc('created_at')->take(10);
            
            return $activities;
        } catch (\Exception $e) {
            return collect();
        }
    }
    
    private function getUserQuickLinks($user, $accessiblePages)
    {
        $links = [];
        
        foreach ($accessiblePages as $page) {
            $link = [
                'name' => $page->display_name,
                'icon' => $page->icon,
                'route' => $page->route ?? '#',
                'color' => $this->getColorForPage($page->name)
            ];
            
            $links[] = $link;
        }
        
        // Add default links if no pages accessible
        if (empty($links)) {
            $links = [
                ['name' => 'My Profile', 'icon' => 'fa-user', 'route' => route('profile.index'), 'color' => 'blue'],
                ['name' => 'My Contributions', 'icon' => 'fa-hand-holding-usd', 'route' => '#', 'color' => 'green'],
            ];
        }
        
        return $links;
    }
    
    private function getColorForPage($pageName)
    {
        $colors = [
            'music-ministry' => 'purple',
            'intercession' => 'blue',
            'social-fellowship' => 'green',
            'discipline' => 'red',
            'finance' => 'yellow',
            'announcements' => 'orange',
            'family' => 'pink',
            'reports' => 'indigo',
        ];
        
        return $colors[$pageName] ?? 'gray';
    }
    
    private function getPersonalStats($user)
    {
        try {
            $memberSince = $user->created_at ? $user->created_at->format('F Y') : 'N/A';
            
            $activityLogsTable = DB::select("SELECT to_regclass('activity_logs')");
            $totalLogins = 0;
            $lastLogin = null;
            
            if ($activityLogsTable[0]->to_regclass) {
                $totalLogins = DB::table('activity_logs')
                    ->where('user_id', $user->id)
                    ->where('action', 'login')
                    ->count();
                
                $lastLogin = DB::table('activity_logs')
                    ->where('user_id', $user->id)
                    ->where('action', 'login')
                    ->latest()
                    ->first();
            }
            
            return [
                'member_since' => $memberSince,
                'total_logins' => $totalLogins,
                'last_login' => $lastLogin,
                'roles' => $user->roles->pluck('display_name')->implode(', ') ?: 'Member',
            ];
        } catch (\Exception $e) {
            return [
                'member_since' => 'N/A',
                'total_logins' => 0,
                'last_login' => null,
                'roles' => 'Member',
            ];
        }
    }
}
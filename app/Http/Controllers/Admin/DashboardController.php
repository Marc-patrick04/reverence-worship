<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User\User;
use App\Models\User\Role;
use App\Models\System\ActivityLog;
use App\Models\System\ErrorLog;

class DashboardController extends Controller
{
    public function superAdminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_roles' => Role::count(),
            'today_logins' => ActivityLog::where('action', 'login')
                ->whereDate('created_at', today())
                ->count(),
            'total_errors' => ErrorLog::count()
        ];
        
        return view('super-admin.dashboard', compact('stats'));
    }
    
    public function adminDashboard()
    {
        return view('admin.dashboard');
    }
}
<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ModuleAssignmentController;
use App\Http\Controllers\Auth\GoogleController;

// Google Login Routes
Route::get('/auth/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

// ==================== TEST ROUTES ====================
Route::get('/ping', function () {
    return 'Laravel is working! Time: ' . date('Y-m-d H:i:s');
});

Route::get('/debug-music', function() {
    $user = auth()->user();
    $page = \App\Models\System\Page::where('name', 'music-ministry')->first();
    $feature = \App\Models\System\Feature::where('name', 'access')->whereHas('page', function($q) { 
        $q->where('name', 'music-ministry'); 
    })->first();
    $hasAccess = $user->canAccess('music-ministry', 'access');
    $permissions = [];
    foreach($user->roles as $role) {
        $rolePermissions = \App\Models\System\RolePageFeature::where('role_id', $role->id)->with(['page', 'feature'])->get();
        foreach($rolePermissions as $p) {
            if($p->page && $p->feature) {
                $permissions[] = $p->page->name . ' - ' . $p->feature->name;
            }
        }
    }
    return [
        'user' => $user->name,
        'user_email' => $user->email,
        'user_roles' => $user->roles->pluck('name'),
        'page_exists' => $page ? true : false,
        'page_id' => $page ? $page->id : null,
        'feature_exists' => $feature ? true : false,
        'feature_id' => $feature ? $feature->id : null,
        'user_has_access' => $hasAccess,
        'all_user_permissions' => $permissions
    ];
})->middleware('auth');

Route::get('/debug-permissions', function () {
    $user = auth()->user();
    return [
        'user' => $user->name,
        'email' => $user->email,
        'is_admin' => !$user->isSuperAdmin(),
        'roles' => $user->roles->pluck('display_name'),
    ];
})->middleware('auth');

Route::get('/test-generate', function() {
    $singers = App\Models\User\User::where('is_singer', true)->whereNotNull('voice_part')->whereNotNull('singer_level')->get();
    return [
        'total_singers' => App\Models\User\User::where('is_singer', true)->count(),
        'singers_with_voice_and_level' => $singers->count(),
        'singers_list' => $singers->map(function($s) {
            return ['name' => $s->name, 'voice_part' => $s->voice_part, 'level' => $s->singer_level];
        })
    ];
})->middleware('auth');

// ==================== GUEST ROUTES ====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ==================== AUTHENTICATED ROUTES ====================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/super-admin/dashboard', [DashboardController::class, 'superAdminDashboard'])->name('super-admin.dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    
    Route::get('/', function () {
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }
        return redirect()->route('admin.dashboard');
    });
});
Route::middleware('auth')->get('/debug-finance-data', function() {
    $data = [];
    
    // Check ALL tables and their columns
    $tables = ['payments', 'contributions', 'gifts', 'sponsors', 'sponsor_payments', 'expenses'];
    
    foreach ($tables as $table) {
        try {
            $firstRow = DB::table($table)->first();
            $data[$table] = [
                'exists' => true,
                'count' => DB::table($table)->count(),
                'has_data' => $firstRow ? true : false,
                'sample' => $firstRow,
                'columns' => $firstRow ? array_keys((array)$firstRow) : []
            ];
        } catch (\Exception $e) {
            $data[$table] = [
                'exists' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    return response()->json($data);
});
// ==================== INCLUDE ALL MODULE ROUTES ====================
require __DIR__.'/admin.php';
require __DIR__.'/users.php';
require __DIR__.'/music.php';
require __DIR__.'/intercession.php';
require __DIR__.'/social-fellowship.php';
require __DIR__.'/discipline.php';
require __DIR__.'/finance.php';
require __DIR__.'/family.php';
require __DIR__.'/profile.php';
require __DIR__.'/announcements.php';
require __DIR__.'/reports.php';
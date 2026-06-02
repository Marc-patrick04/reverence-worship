<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PageAssignmentController;
use App\Http\Controllers\Admin\PagesController;
use App\Http\Controllers\Admin\PermissionManagerController;
use App\Http\Controllers\Reports\LogController;
use App\Http\Controllers\Settings\SettingController;
use App\Http\Controllers\Music\MusicController;
use App\Http\Controllers\Intercession\IntercessionController;
use App\Http\Controllers\Intercession\FormController;
use App\Http\Controllers\Finance\ContributionController;
use App\Http\Controllers\ModuleAssignmentController;
use App\Models\Intercession\DailyDevotion;
use App\Http\Controllers\Auth\GoogleController;

// Google Login Routes
Route::get('/auth/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================== TEST ROUTES ====================
Route::get('/ping', function () {
    return 'Laravel is working! Time: ' . date('Y-m-d H:i:s');
});

Route::get('/debug-music', function() {
    $user = auth()->user();
    $page = \App\Models\System\Page::where('name', 'music-ministry')->first();
    $feature = \App\Models\System\Feature::where('name', 'access')
        ->whereHas('page', function($q) { $q->where('name', 'music-ministry'); })->first();
    $hasAccess = $user->canAccess('music-ministry', 'access');
    $permissions = [];
    foreach($user->roles as $role) {
        $rolePermissions = \App\Models\System\RolePageFeature::where('role_id', $role->id)
            ->with(['page', 'feature'])->get();
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

// ==================== GUEST ROUTES ====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ==================== AUTHENTICATED ROUTES ====================
Route::middleware('auth')->group(function () {
    
    // ---------- LOGOUT ----------
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // ---------- DASHBOARD ROUTES ----------
    Route::get('/super-admin/dashboard', [DashboardController::class, 'superAdminDashboard'])->name('super-admin.dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    
    // ==================== SUPER ADMIN ROUTES ====================
    
// User Management Routes
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/create-form', [UserController::class, 'getCreateForm'])->name('users.create-form');
    Route::post('/', [UserController::class, 'store'])->name('users.store');
    Route::get('/{id}/json', [UserController::class, 'getUserJson'])->name('users.json');
    Route::get('/{id}/edit-form', [UserController::class, 'getEditForm'])->name('users.edit-form');
    Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
    Route::get('/{id}/roles/edit', [UserController::class, 'getEditRolesForm'])->name('users.roles.edit');
    Route::put('/{id}/roles', [UserController::class, 'updateRoles'])->name('users.roles.update');
    Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/export', [UserController::class, 'export'])->name('users.export');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    // Add this route for viewing user details
    Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
});
    
    // ---------- ROLE MANAGEMENT ROUTES ----------
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/store', [RoleController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy');
        Route::get('/{id}', [RoleController::class, 'show'])->name('show');
    });
    
    // ---------- PAGE ASSIGNMENT ROUTES ----------
    Route::prefix('page-assignment')->name('page-assignment.')->group(function () {
        Route::get('/', [PageAssignmentController::class, 'index'])->name('index');
        Route::get('/role/{roleId}/pages', [PageAssignmentController::class, 'getRolePages']);
        Route::get('/role/{roleId}/page/{pageId}/features', [PageAssignmentController::class, 'getRolePageFeatures']);
        Route::get('/role/{roleId}/features', [PageAssignmentController::class, 'getAssignedFeatures']);
        Route::post('/save', [PageAssignmentController::class, 'saveAssignments']);
    });
    
    // ---------- PAGES MANAGEMENT ROUTES ----------
    Route::prefix('pages')->name('pages.')->group(function () {
        Route::get('/', [PagesController::class, 'index'])->name('index');
        Route::get('/create', [PagesController::class, 'create'])->name('create');
        Route::post('/store', [PagesController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PagesController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PagesController::class, 'update'])->name('update');
        Route::delete('/{id}', [PagesController::class, 'destroy'])->name('destroy');
        
        Route::get('/{pageId}/features', [PagesController::class, 'features'])->name('features');
        Route::post('/{pageId}/features/store', [PagesController::class, 'storeFeature'])->name('features.store');
        Route::get('/{pageId}/features/{featureId}/edit', [PagesController::class, 'editFeature'])->name('features.edit');
        Route::put('/{pageId}/features/{featureId}', [PagesController::class, 'updateFeature'])->name('features.update');
        Route::delete('/{pageId}/features/{featureId}', [PagesController::class, 'destroyFeature'])->name('features.destroy');
    });
    
    // ---------- PERMISSION MANAGER ROUTES ----------
    Route::prefix('permission-manager')->name('permission-manager.')->group(function () {
        Route::get('/', [PermissionManagerController::class, 'index'])->name('index');
        Route::post('/page/store', [PermissionManagerController::class, 'storePage'])->name('page.store');
        Route::put('/page/{id}', [PermissionManagerController::class, 'updatePage'])->name('page.update');
        Route::get('/page/{id}/delete', [PermissionManagerController::class, 'deletePage'])->name('page.delete');
        Route::post('/feature/store', [PermissionManagerController::class, 'storeFeature'])->name('feature.store');
        Route::put('/feature/{id}', [PermissionManagerController::class, 'updateFeature'])->name('feature.update');
        Route::get('/feature/{id}/delete', [PermissionManagerController::class, 'deleteFeature'])->name('feature.delete');
    });
    
    // ---------- MODULE ASSIGNMENT ROUTES ----------
    Route::prefix('module-assignment')->name('module-assignment.')->group(function () {
        Route::get('/', [ModuleAssignmentController::class, 'index'])->name('index');
        Route::post('/assign', [ModuleAssignmentController::class, 'assignModules'])->name('assign');
        Route::get('/user/{id}/modules', [ModuleAssignmentController::class, 'getUserModules'])->name('user-modules');
        Route::post('/remove', [ModuleAssignmentController::class, 'removeModule'])->name('remove');
    });
    
    // ==================== FEATURE MODULES ====================
    
  Route::prefix('music')->group(function () {
    Route::get('/', [MusicController::class, 'index'])->name('music.index');
    
    // ==================== GALLERY ROUTES ====================
    Route::post('/gallery/store', [MusicController::class, 'storeGallery'])->name('music.gallery.store');
    Route::get('/gallery/{id}/edit', [MusicController::class, 'editGallery'])->name('music.gallery.edit');
    Route::put('/gallery/{id}', [MusicController::class, 'updateGallery'])->name('music.gallery.update');
    Route::delete('/gallery/{id}', [MusicController::class, 'deleteGallery'])->name('music.gallery.delete');
    
    // ==================== PLAYLIST ROUTES ====================
    Route::post('/playlist/store', [MusicController::class, 'storePlaylist'])->name('music.playlist.store');
    Route::get('/playlist/{id}/edit', [MusicController::class, 'editPlaylist'])->name('music.playlist.edit');
    Route::put('/playlist/{id}', [MusicController::class, 'updatePlaylistSongs'])->name('music.playlist.update');
    Route::delete('/playlist/{id}', [MusicController::class, 'deletePlaylist'])->name('music.playlist.delete');
    Route::get('/playlist/{id}/songs', [MusicController::class, 'getPlaylistSongs'])->name('music.playlist.songs');
    
    // ==================== SONG ROUTES ====================
    Route::post('/song/store', [MusicController::class, 'storeSong'])->name('music.song.store');
    Route::get('/song/{id}/edit', [MusicController::class, 'editSong'])->name('music.song.edit');
    Route::put('/song/{id}', [MusicController::class, 'updateSong'])->name('music.song.update');
    Route::delete('/song/{id}', [MusicController::class, 'deleteSong'])->name('music.song.delete');
    Route::get('/song/{id}/lyrics', [MusicController::class, 'viewLyrics'])->name('music.song.lyrics');
    Route::post('/song/add-to-playlist', [MusicController::class, 'addToPlaylist'])->name('music.song.add-to-playlist');
    
    // ==================== SINGER ROUTES ====================
    Route::put('/singer/{id}/voice-part', [MusicController::class, 'updateVoicePart'])->name('music.singer.voice-part');
    Route::put('/singer/{id}/performance-level', [MusicController::class, 'updatePerformanceLevel'])->name('music.singer.performance-level');
    Route::post('/singer/settings', [MusicController::class, 'updateSingerSettings'])->name('music.singer.settings');
    
    // ==================== GROUP ROUTES ====================
    Route::post('/group/store', [MusicController::class, 'storeGroup'])->name('music.group.store');
    Route::delete('/group/{id}', [MusicController::class, 'deleteGroup'])->name('music.group.delete');
    
    // ==================== PUBLIC BOARD ROUTES ====================
    Route::post('/board/store', [MusicController::class, 'storeBoardPost'])->name('music.board.store');
    Route::post('/board/{id}/toggle-pin', [MusicController::class, 'togglePinBoard'])->name('music.board.toggle-pin');
    Route::delete('/board/{id}', [MusicController::class, 'deleteBoardPost'])->name('music.board.delete');
    
    // ==================== ACTION PLAN ROUTES ====================
    Route::post('/action-plan/store', [MusicController::class, 'storeActionPlan'])->name('music.action-plan.store');
    Route::put('/action-plan/{id}/status', [MusicController::class, 'updateActionPlanStatus'])->name('music.action-plan.status');
    Route::delete('/action-plan/{id}', [MusicController::class, 'deleteActionPlan'])->name('music.action-plan.delete');
    
   // ==================== SERVICE TEAM / GENERATION ROUTES ====================
Route::post('/teams/generate', [MusicController::class, 'generateBalancedGroups'])->name('music.teams.generate');
Route::get('/teams/generation/{id}', [MusicController::class, 'getGenerationDetails'])->name('music.teams.generation');
Route::get('/teams/generation/{id}/export', [MusicController::class, 'exportGeneration'])->name('music.teams.export');
Route::post('/teams/generation/{id}/restore', [MusicController::class, 'restoreGeneration'])->name('music.teams.restore');
Route::delete('/teams/service/{id}', [MusicController::class, 'deleteServiceTeam'])->name('music.teams.delete');
}); 
    
    // ---------- INTERCESSION & SPIRITUAL GROWTH ----------
    Route::prefix('intercession')->name('intercession.')->group(function () {
        Route::get('/', [IntercessionController::class, 'index'])->name('index');
        
        Route::get('/devotion/{id}', [IntercessionController::class, 'showDevotion'])->name('devotion.show');
        Route::post('/devotion/{id}/complete', [IntercessionController::class, 'completeDevotion'])->name('devotion.complete');
        
        Route::get('/action-plans', [IntercessionController::class, 'actionPlans'])->name('action-plans');
        Route::post('/action-plans/store', [IntercessionController::class, 'storeActionPlan'])->name('action-plans.store');
        Route::put('/action-plans/{id}/status', [IntercessionController::class, 'updateActionPlanStatus'])->name('action-plans.status');
        
        Route::get('/archives', [IntercessionController::class, 'archives'])->name('archives');
    });
    // Devotion routes
Route::post('/intercession/devotion/{id}/complete', [IntercessionController::class, 'completeDevotion'])->name('intercession.devotion.complete');
Route::get('/intercession/devotion/{id}', function ($id) {
    $devotion = DailyDevotion::findOrFail($id);
    
    $completed = false;
    $canComplete = false;
    
    if (auth()->check()) {
        $completed = $devotion->isCompletedByUser(auth()->id());
        $canComplete = !$completed;
    }
    
    return response()->json([
        'success' => true,
        'devotion' => [
            'id' => $devotion->id,
            'title' => $devotion->title,
            'content' => $devotion->content,
            'bible_verse' => $devotion->bible_verse,
            'date' => $devotion->date,
            'formatted_date' => $devotion->date->format('l, F j, Y'),
            'completed_by_user' => $completed,
            'can_complete' => $canComplete
        ]
    ]);
});

Route::get('/intercession/devotion/show/{id}', function ($id) {
    $devotion = DailyDevotion::findOrFail($id);
    
    $hasCompleted = false;
    if (auth()->check()) {
        $hasCompleted = $devotion->isCompletedByUser(auth()->id());
    }
    
    return view('modules.intercession.devotion-show', compact('devotion', 'hasCompleted'));
})->name('intercession.devotion.show');
    // ---------- SPIRITUAL FORMS (Public) ----------
    Route::prefix('forms')->name('forms.')->group(function () {
        Route::get('/', [FormController::class, 'index'])->name('index');
        Route::get('/{id}/take', [FormController::class, 'takeForm'])->name('take');
        Route::post('/{id}/submit', [FormController::class, 'submitForm'])->name('submit');
        Route::get('/{id}/results', [FormController::class, 'results'])->name('results');
    });
    
    // ---------- FORM MANAGEMENT ----------
    Route::prefix('forms/manage')->name('forms.manage.')->group(function () {
        Route::get('/', [FormController::class, 'manageForms'])->name('index');
        Route::get('/create', [FormController::class, 'createForm'])->name('create');
        Route::post('/store', [FormController::class, 'storeForm'])->name('store');
        Route::get('/{id}/edit', [FormController::class, 'editForm'])->name('edit');
        Route::put('/{id}', [FormController::class, 'updateForm'])->name('update');
        Route::delete('/{id}', [FormController::class, 'deleteForm'])->name('delete');
        Route::get('/{id}/submissions', [FormController::class, 'viewSubmissions'])->name('submissions');
    });
    
    // ---------- FINANCIAL / CONTRIBUTIONS ----------
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/my-contributions', [ContributionController::class, 'myContributions'])->name('my-contributions');
        Route::post('/submit-payment', [ContributionController::class, 'submitPayment'])->name('submit-payment');
        Route::post('/update-annual-amount', [ContributionController::class, 'updateAnnualAmount'])->name('update-annual-amount');
        Route::get('/admin', [ContributionController::class, 'adminIndex'])->name('admin.index');
        Route::post('/approve/{id}', [ContributionController::class, 'approveContribution'])->name('approve');
    });
    
    // ---------- SYSTEM LOGS ----------
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/activity', [LogController::class, 'activityLogs'])->name('activity');
        Route::get('/errors', [LogController::class, 'errorLogs'])->name('errors');
        Route::get('/activity/{id}', [LogController::class, 'viewActivity'])->name('view-activity');
        Route::get('/error/{id}', [LogController::class, 'viewError'])->name('view-error');
        Route::get('/clear-activity', [LogController::class, 'clearActivityLogs'])->name('clear.activity');
        Route::get('/clear-errors', [LogController::class, 'clearErrorLogs'])->name('clear.errors');
        Route::get('/export-activity', [LogController::class, 'exportActivityLogs'])->name('export.activity');
    });
    
    // ---------- SYSTEM SETTINGS ----------
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/general', [SettingController::class, 'updateGeneral'])->name('update.general');
        Route::post('/email', [SettingController::class, 'updateEmail'])->name('update.email');
        Route::post('/security', [SettingController::class, 'updateSecurity'])->name('update.security');
        Route::post('/clear-cache', [SettingController::class, 'clearCache'])->name('clear-cache');
        Route::post('/backup', [SettingController::class, 'backupDatabase'])->name('backup');
    });
    
    // ---------- DEFAULT REDIRECT ----------
    Route::get('/', function () {
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }
        return redirect()->route('admin.dashboard');
    });

    // ========== ADD THESE DEVOTION ROUTES ==========
    // Mark devotion as complete
    Route::post('/intercession/devotion/complete', function (Illuminate\Http\Request $request) {
        try {
            $userId = auth()->id();
            $devotionId = $request->devotion_id;
            
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Please login first'], 401);
            }
            
            // Check if already completed
            $exists = DB::selectOne(
                "SELECT * FROM user_devotion_completions WHERE user_id = ? AND devotion_id = ?",
                [$userId, $devotionId]
            );
            
            if (!$exists) {
                DB::insert(
                    "INSERT INTO user_devotion_completions (user_id, devotion_id, completed_at) VALUES (?, ?, NOW())",
                    [$userId, $devotionId]
                );
            }
            
            return response()->json(['success' => true, 'message' => 'Devotion completed successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    })->name('intercession.devotion.complete');
    
    // View single devotion
    Route::get('/intercession/devotion/{id}', function ($id) {
        try {
            $devotion = DB::selectOne("
                SELECT * FROM devotions 
                WHERE id = ? AND is_active = true
            ", [$id]);
            
            if (!$devotion) {
                return response()->json(['success' => false, 'message' => 'Devotion not found'], 404);
            }
            
            // Check if user completed
            $completed = null;
            $canComplete = false;
            
            if (auth()->check()) {
                $completed = DB::selectOne(
                    "SELECT * FROM user_devotion_completions WHERE user_id = ? AND devotion_id = ?",
                    [auth()->id(), $id]
                );
                $canComplete = !$completed;
            }
            
            return response()->json([
                'success' => true,
                'devotion' => [
                    'id' => $devotion->id,
                    'title' => $devotion->title,
                    'content' => $devotion->content,
                    'bible_verse' => $devotion->bible_verse,
                    'date' => $devotion->date,
                    'formatted_date' => date('l, F j, Y', strtotime($devotion->date)),
                    'completed_by_user' => !is_null($completed),
                    'can_complete' => $canComplete
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    });
    
    // Show devotion page (optional)
    Route::get('/intercession/devotion/show/{id}', function ($id) {
        $devotion = DB::selectOne("SELECT * FROM devotions WHERE id = ? AND is_active = true", [$id]);
        
        if (!$devotion) {
            abort(404);
        }
        
        $hasCompleted = false;
        if (auth()->check()) {
            $completed = DB::selectOne(
                "SELECT * FROM user_devotion_completions WHERE user_id = ? AND devotion_id = ?",
                [auth()->id(), $id]
            );
            $hasCompleted = !is_null($completed);
        }
        
        return view('intercession.devotion-show', compact('devotion', 'hasCompleted'));
    })->name('intercession.devotion.show');
});

// ==================== TEST ROUTES ====================
Route::get('/test-generate', function() {
    $singers = App\Models\User\User::where('is_singer', true)
        ->whereNotNull('voice_part')
        ->whereNotNull('singer_level')
        ->get();
    
    return [
        'total_singers' => App\Models\User\User::where('is_singer', true)->count(),
        'singers_with_voice_and_level' => $singers->count(),
        'singers_list' => $singers->map(function($s) {
            return ['name' => $s->name, 'voice_part' => $s->voice_part, 'level' => $s->singer_level];
        })
    ];
})->middleware('auth');

Route::get('/forms/manage/{id}/settings', [FormController::class, 'settings'])->name('forms.manage.settings');
Route::put('/forms/manage/{id}/settings', [FormController::class, 'updateSettings'])->name('forms.manage.settings.update');
Route::get('/forms/manage/{id}/submissions/export', [FormController::class, 'exportSubmissions'])->name('forms.manage.submissions.export');


Route::post('/forms/manage/{id}/toggle-publish', [FormController::class, 'togglePublish'])->name('forms.manage.toggle-publish');

// Form routes
Route::prefix('forms')->group(function () {
    Route::get('/manage', [FormController::class, 'index'])->name('forms.manage.index');
    Route::get('/manage/create', [FormController::class, 'create'])->name('forms.manage.create');
    Route::post('/manage/store', [FormController::class, 'store'])->name('forms.manage.store');
    Route::get('/{id}/take', [FormController::class, 'take'])->name('forms.take');
    Route::post('/{id}/submit', [FormController::class, 'submit'])->name('forms.submit');
    Route::get('/manage/{id}/edit', [FormController::class, 'edit'])->name('forms.manage.edit');
    Route::put('/manage/{id}', [FormController::class, 'update'])->name('forms.manage.update');
    Route::delete('/manage/{id}', [FormController::class, 'destroy'])->name('forms.manage.delete');
    Route::post('/manage/{id}/toggle-publish', [FormController::class, 'togglePublish'])->name('forms.manage.toggle-publish');
    Route::get('/manage/{id}/submissions', [FormController::class, 'submissions'])->name('forms.manage.submissions');
    Route::get('/{id}/results', [FormController::class, 'results'])->name('forms.results');
});

// Interception main page
Route::get('/intercession', [IntercessionController::class, 'index'])->name('intercession.index');

// Action Plan routes
Route::post('/intercession/action-plans/store', [IntercessionController::class, 'storeActionPlan'])->name('intercession.action-plans.store');

// Devotion routes
Route::post('/intercession/devotion/{id}/complete', [IntercessionController::class, 'completeDevotion'])->name('intercession.devotion.complete');
// Action Plan routes
Route::prefix('intercession/action-plans')->group(function () {
    Route::post('/store', [IntercessionController::class, 'storeActionPlan'])->name('intercession.action-plans.store');
    Route::put('/{id}/status', [IntercessionController::class, 'updateActionPlanStatus'])->name('intercession.action-plans.status');
    Route::delete('/{id}', [IntercessionController::class, 'deleteActionPlan'])->name('intercession.action-plans.delete');
    Route::get('/{id}/edit', [IntercessionController::class, 'editActionPlan'])->name('intercession.action-plans.edit');
    Route::put('/{id}', [IntercessionController::class, 'updateActionPlan'])->name('intercession.action-plans.update');
});
// Devotion management routes
Route::prefix('intercession/devotions')->group(function () {
    Route::post('/store', [IntercessionController::class, 'storeDevotion'])->name('intercession.devotions.store');
    Route::get('/{id}/edit', [IntercessionController::class, 'editDevotion'])->name('intercession.devotions.edit');
    Route::post('/{id}', [IntercessionController::class, 'updateDevotion'])->name('intercession.devotions.update');
    Route::delete('/{id}', [IntercessionController::class, 'deleteDevotion'])->name('intercession.devotions.delete');
});

// Devotion complete route
Route::post('/intercession/devotion/{id}/complete', [IntercessionController::class, 'completeDevotion'])->name('intercession.devotion.complete');
Route::get('/intercession/devotion/show/{id}', [IntercessionController::class, 'showDevotion'])->name('intercession.devotion.show');
// Prayer request route
Route::post('/intercession/prayer/store', [IntercessionController::class, 'storePrayerRequest'])->name('intercession.prayer.store');
// Archives routes
Route::prefix('intercession/archives')->group(function () {
    // Sections
    Route::post('/sections/store', [IntercessionController::class, 'storeArchiveSection'])->name('intercession.archives.sections.store');
    Route::put('/sections/{id}', [IntercessionController::class, 'updateArchiveSection']);
    Route::delete('/sections/{id}', [IntercessionController::class, 'deleteArchiveSection']);
    Route::get('/sections/{id}/pages', [IntercessionController::class, 'getSectionPages']);
    
    // Pages
    Route::post('/pages/store', [IntercessionController::class, 'storeArchivePage'])->name('intercession.archives.pages.store');
    Route::put('/pages/{id}', [IntercessionController::class, 'updateArchivePage']);
    Route::delete('/pages/{id}', [IntercessionController::class, 'deleteArchivePage']);
    Route::get('/pages/{id}/edit', [IntercessionController::class, 'editArchivePage']);
    Route::get('/pages/{id}', [IntercessionController::class, 'showArchivePage']);
});

// Archives routes
Route::prefix('intercession/archives')->group(function () {
    Route::post('/sections/store', [IntercessionController::class, 'storeArchiveSection'])->name('intercession.archives.sections.store');
    Route::put('/sections/{id}', [IntercessionController::class, 'updateArchiveSection']);
    Route::delete('/sections/{id}', [IntercessionController::class, 'deleteArchiveSection']);
    Route::get('/sections/{id}/pages', [IntercessionController::class, 'getSectionPages']);
    
    Route::post('/pages/store', [IntercessionController::class, 'storeArchivePage'])->name('intercession.archives.pages.store');
    Route::put('/pages/{id}', [IntercessionController::class, 'updateArchivePage']);
    Route::delete('/pages/{id}', [IntercessionController::class, 'deleteArchivePage']);
    Route::get('/pages/{id}/edit', [IntercessionController::class, 'editArchivePage']);
    Route::get('/pages/{id}', [IntercessionController::class, 'showArchivePage']);
});
use App\Http\Controllers\SocialFellowship\SocialFellowshipController;
// Social Fellowship Routes
Route::prefix('social-fellowship')->group(function () {
    Route::get('/', [SocialFellowshipController::class, 'index'])->name('social-fellowship.index');
    
    // Family routes
    Route::post('/families/store', [SocialFellowshipController::class, 'storeFamily'])->name('social-fellowship.families.store');
    Route::get('/family/{id}/details', [SocialFellowshipController::class, 'getFamilyDetails'])->name('social-fellowship.family.details');
    Route::get('/family/{id}', [SocialFellowshipController::class, 'getFamily'])->name('social-fellowship.family.get');
    Route::delete('/family/{id}', [SocialFellowshipController::class, 'deleteFamily'])->name('social-fellowship.family.delete');
    Route::post('/family/{id}/member', [SocialFellowshipController::class, 'addMember'])->name('social-fellowship.family.add-member');
    Route::delete('/family/{familyId}/member/{userId}', [SocialFellowshipController::class, 'removeMember'])->name('social-fellowship.family.remove-member');
    
    // Task routes
    Route::post('/tasks/store', [SocialFellowshipController::class, 'storeTask'])->name('social-fellowship.tasks.store');
    Route::get('/tasks/{id}', [SocialFellowshipController::class, 'getTask'])->name('social-fellowship.tasks.get');
    Route::get('/tasks/{id}/edit', [SocialFellowshipController::class, 'editTask'])->name('social-fellowship.tasks.edit');
    Route::put('/tasks/{id}', [SocialFellowshipController::class, 'updateTask'])->name('social-fellowship.tasks.update');
    Route::delete('/tasks/{id}', [SocialFellowshipController::class, 'deleteTask'])->name('social-fellowship.tasks.delete');
    
    // Action Plan routes
    Route::post('/action-plans/store', [SocialFellowshipController::class, 'storeActionPlan'])->name('social-fellowship.action-plans.store');
    Route::get('/action-plans/{id}/edit', [SocialFellowshipController::class, 'editActionPlan'])->name('social-fellowship.action-plans.edit');
    Route::put('/action-plans/{id}', [SocialFellowshipController::class, 'updateActionPlan'])->name('social-fellowship.action-plans.update');
    Route::delete('/action-plans/{id}', [SocialFellowshipController::class, 'deleteActionPlan'])->name('social-fellowship.action-plans.delete');
});

// Discipline Management Routes (Placeholder)
Route::prefix('discipline')->group(function () {
    Route::get('/', function () {
        return view('modules.discipline.index');
    })->name('discipline.index');
});

// ==================== FINANCIAL MANAGEMENT MODULE ====================
Route::prefix('finance')->group(function () {
    Route::get('/', function () {
        return view('modules.finance.index');
    })->name('finance.index');
});

// ==================== ADMIN ANNOUNCEMENTS MODULE ====================
Route::prefix('announcements')->group(function () {
    Route::get('/', function () {
        return view('modules.announcements.index');
    })->name('announcements.index');
});

// ==================== REPORTS MODULE ====================
Route::prefix('reports')->group(function () {
    Route::get('/', function () {
        return view('modules.reports.index');
    })->name('reports.index');
});

Route::get('/profile', function () {
    return view('profile.index');
})->name('profile.index');
// ==================== MY FAMILY MODULE ====================
Route::prefix('family')->group(function () {
    Route::get('/', function () {
        return view('modules.family.index');
    })->name('family.index');
});
// Archives routes for Social Fellowship
Route::prefix('social-fellowship/archives')->group(function () {
    Route::post('/sections/store', [SocialFellowshipController::class, 'storeArchiveSection'])->name('social-fellowship.archives.sections.store');
    Route::put('/sections/{id}', [SocialFellowshipController::class, 'updateArchiveSection']);
    Route::delete('/sections/{id}', [SocialFellowshipController::class, 'deleteArchiveSection']);
    Route::get('/sections/{id}/pages', [SocialFellowshipController::class, 'getSectionPages']);
    
    Route::post('/pages/store', [SocialFellowshipController::class, 'storeArchivePage'])->name('social-fellowship.archives.pages.store');
    Route::put('/pages/{id}', [SocialFellowshipController::class, 'updateArchivePage']);
    Route::delete('/pages/{id}', [SocialFellowshipController::class, 'deleteArchivePage']);
    Route::get('/pages/{id}/edit', [SocialFellowshipController::class, 'editArchivePage']);
    Route::get('/pages/{id}', [SocialFellowshipController::class, 'showArchivePage']);
});
// ==================== MY FAMILY MODULE ====================
use App\Http\Controllers\Family\FamilyController;

Route::prefix('family')->group(function () {
    Route::get('/', [FamilyController::class, 'index'])->name('family.index');
    Route::put('/task/{id}/status', [FamilyController::class, 'updateTaskStatus'])->name('family.task.status');
    Route::get('/member/{id}/details', [FamilyController::class, 'getMemberDetails'])->name('family.member.details');
});
Route::get('/social-fellowship/debug', [SocialFellowshipController::class, 'debugData'])->name('social-fellowship.debug');
// Add to index method to pass action plan stats
$actionPlans = DB::table('family_action_plans')
    ->join('families', 'family_action_plans.family_id', '=', 'families.id')
    ->select('family_action_plans.*', 'families.name as family_name')
    ->orderBy('created_at', 'desc')
    ->get();

$totalActionPlans = $actionPlans->count();
$completedPlans = $actionPlans->where('status', 'completed')->count();
$inProgressPlans = $actionPlans->where('status', 'in-progress')->count();
$pendingPlans = $actionPlans->where('status', 'pending')->count();
$overallProgress = $totalActionPlans > 0 ? round(($completedPlans / $totalActionPlans) * 100) : 0;

// Add to compact
return view('modules.social-fellowship.index', compact(
    // ... existing variables
    'actionPlans', 'totalActionPlans', 'completedPlans', 'inProgressPlans', 'pendingPlans', 'overallProgress'
));

<?php

namespace Database\Seeders;

use App\Models\System\Feature;
use App\Models\System\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleActionPermissionsSeeder extends Seeder
{
    private array $permissionAliases = [
        'music-ministry' => [
            'create-song' => 'add-songs',
            'edit-song' => 'edit-songs',
            'delete-song' => 'delete-songs',
            'create-playlist' => 'add-playlists',
            'edit-playlist' => 'edit-playlists',
            'delete-playlist' => 'delete-playlists',
            'create-gallery-item' => 'add-gallery',
            'edit-gallery-item' => 'edit-gallery',
            'delete-gallery-item' => 'delete-gallery',
            'create-group' => 'add-groups',
            'delete-group' => 'delete-groups',
            'create-board-post' => 'add-board',
            'edit-board-post' => 'edit-board',
            'delete-board-post' => 'delete-board',
        ],
        'intercession' => [
            'create-form' => 'create-forms',
            'edit-form' => 'edit-forms',
            'delete-form' => 'delete-forms',
            'publish-form' => 'publish-forms',
            'view-form-results' => 'view-results',
        ],
        'discipline' => [
            'approve-permission-request' => 'approve-permission',
            'reject-permission-request' => 'reject-permission',
        ],
    ];

    /**
     * Application modules managed through role permissions.
     * My Family, Parent Dashboard, My Profile, Activity Logs, and Permission Manager are excluded.
     */
    private array $modules = [
        'dashboard' => [
            'display_name' => 'Dashboard',
            'icon' => 'fa-tachometer-alt',
            'route' => 'user.dashboard',
            'actions' => [
                'view' => 'View Dashboard',
            ],
        ],
        'users' => [
            'display_name' => 'User Management',
            'icon' => 'fa-users',
            'route' => 'users.index',
            'actions' => [
                'view' => 'View User Management',
                'view-users' => 'View Users List',
                'view-user-details' => 'View User Details',
                'search-users' => 'Search and Filter Users',
                'create-user' => 'Create Users',
                'edit-user' => 'Edit User Information',
                'manage-user-roles' => 'Manage User Roles',
                'approve-user' => 'Approve Users',
                'reject-user' => 'Reject Users',
                'activate-user' => 'Activate Users',
                'deactivate-user' => 'Deactivate Users',
                'delete-user' => 'Delete Users',
                'export-users-csv' => 'Export Users to CSV',
                'export-users-pdf' => 'Export Users to PDF',
            ],
        ],
        'financial' => [
            'display_name' => 'My Contributions',
            'icon' => 'fa-hand-holding-usd',
            'route' => 'financial.my-contributions',
            'actions' => [
                'view' => 'View My Contributions',
                'view-history' => 'View Payment History',
                'submit-payment' => 'Submit Payments',
                'update-annual-amount' => 'Update Annual Contribution Amount',
            ],
        ],
        'music-ministry' => [
            'display_name' => 'Music & Evangelism',
            'icon' => 'fa-music',
            'route' => 'music.index',
            'actions' => [
                'view' => 'View Music Module',
                'view-songs' => 'View Songs',
                'add-songs' => 'Add Songs',
                'edit-songs' => 'Edit Songs',
                'delete-songs' => 'Delete Songs',
                'view-lyrics' => 'View Song Lyrics',
                'add-song-to-playlist' => 'Add Songs to Playlists',
                'view-playlists' => 'View Playlists',
                'add-playlists' => 'Add Playlists',
                'edit-playlists' => 'Edit Playlists',
                'delete-playlists' => 'Delete Playlists',
                'view-gallery' => 'View Music Gallery',
                'add-gallery' => 'Add Gallery Items',
                'edit-gallery' => 'Edit Gallery Items',
                'delete-gallery' => 'Delete Gallery Items',
                'view-singers' => 'View Singers',
                'edit-singer' => 'Update Singer Details',
                'manage-singer-settings' => 'Manage Singer Settings',
                'view-groups' => 'View Music Groups',
                'add-groups' => 'Create Music Groups',
                'delete-groups' => 'Delete Music Groups',
                'view-board' => 'View Public Board',
                'add-board' => 'Create Board Posts',
                'edit-board' => 'Edit Board Posts',
                'delete-board' => 'Delete Board Posts',
                'publish-board-post' => 'Publish Board Posts',
                'pin-board-post' => 'Pin Board Posts',
                'view-action-plans' => 'View Music Action Plans',
                'create-action-plan' => 'Create Music Action Plans',
                'edit-action-plan' => 'Edit Music Action Plans',
                'delete-action-plan' => 'Delete Music Action Plans',
                'update-action-plan-status' => 'Update Music Action Plan Status',
                'manage-action-plan-tasks' => 'Manage Music Action Plan Tasks',
                'view-service-teams' => 'View Service Teams',
                'generate-service-teams' => 'Generate Service Teams',
                'restore-service-teams' => 'Restore Service Teams',
                'delete-service-teams' => 'Delete Service Teams',
                'export-service-teams' => 'Export Service Teams',
                'manage-landing-videos' => 'Manage Landing Page Videos',
                'manage-featured-images' => 'Manage Featured Images',
                'publish-landing-content' => 'Publish Landing Page Content',
                'reorder-landing-content' => 'Reorder Landing Page Content',
            ],
        ],
        'intercession' => [
            'display_name' => 'Intercession & Spiritual Growth',
            'icon' => 'fa-pray',
            'route' => 'intercession.index',
            'actions' => [
                'view' => 'View Intercession Module',
                'create-prayer-request' => 'Create Prayer Requests',
                'view-action-plans' => 'View Intercession Action Plans',
                'create-action-plan' => 'Create Intercession Action Plans',
                'edit-action-plan' => 'Edit Intercession Action Plans',
                'delete-action-plan' => 'Delete Intercession Action Plans',
                'update-action-plan-status' => 'Update Intercession Action Plan Status',
                'manage-action-plan-tasks' => 'Manage Intercession Action Plan Tasks',
                'view-forms' => 'View Forms',
                'create-forms' => 'Create Forms',
                'edit-forms' => 'Edit Forms',
                'delete-forms' => 'Delete Forms',
                'publish-forms' => 'Publish Forms',
                'submit-form' => 'Submit Forms',
                'view-form-submissions' => 'View Form Submissions',
                'release-form-submissions' => 'Release Form Submissions',
                'delete-form-submissions' => 'Delete Form Submissions',
                'view-results' => 'View Form Results',
                'view-reports' => 'View Intercession Reports',
                'export-reports' => 'Export Intercession Reports',
            ],
        ],
        'social-fellowship' => [
            'display_name' => 'Social Fellowship',
            'icon' => 'fa-hand-holding-heart',
            'route' => 'social-fellowship.index',
            'actions' => [
                'view' => 'View Social Fellowship',
                'view-families' => 'View Families',
                'create-family' => 'Create Families',
                'edit-family' => 'Edit Families',
                'delete-family' => 'Delete Families',
                'manage-family-members' => 'Manage Family Members',
                'change-family-parent' => 'Change Family Parent',
                'view-users' => 'View Social Fellowship Users',
                'view-tasks' => 'View Social Tasks',
                'create-task' => 'Create Social Tasks',
                'edit-task' => 'Edit Social Tasks',
                'delete-task' => 'Delete Social Tasks',
                'view-action-plans' => 'View Social Action Plans',
                'create-action-plan' => 'Create Social Action Plans',
                'edit-action-plan' => 'Edit Social Action Plans',
                'delete-action-plan' => 'Delete Social Action Plans',
                'manage-action-plan-tasks' => 'Manage Social Action Plan Tasks',
                'export' => 'Export Social Fellowship Data',
            ],
        ],
        'discipline' => [
            'display_name' => 'Discipline Management',
            'icon' => 'fa-gavel',
            'route' => 'discipline.index',
            'actions' => [
                'view' => 'View Discipline Module',
                'view-overview' => 'View Discipline Overview',
                'view-attendance' => 'View Attendance',
                'create-attendance' => 'Create Attendance Sessions',
                'edit-attendance' => 'Edit Attendance',
                'delete-attendance' => 'Delete Attendance',
                'complete-attendance' => 'Complete Attendance Sessions',
                'view-permission-requests' => 'View Permission Requests',
                'create-permission-request' => 'Create Permission Requests',
                'edit-permission-request' => 'Edit Permission Requests',
                'delete-permission-request' => 'Delete Permission Requests',
                'approve-permission' => 'Approve Permission Requests',
                'reject-permission' => 'Reject Permission Requests',
                'view-records' => 'View Discipline Records',
                'create-record' => 'Create Discipline Records',
                'edit-record' => 'Edit Discipline Records',
                'delete-record' => 'Delete Discipline Records',
                'resolve-record' => 'Resolve Discipline Records',
                'view-action-plans' => 'View Discipline Action Plans',
                'create-action-plan' => 'Create Discipline Action Plans',
                'edit-action-plan' => 'Edit Discipline Action Plans',
                'delete-action-plan' => 'Delete Discipline Action Plans',
                'manage-action-plan-tasks' => 'Manage Discipline Action Plan Tasks',
                'view-reports' => 'View Discipline Reports',
                'generate-reports' => 'Generate Discipline Reports',
                'export-reports' => 'Export Discipline Reports',
            ],
        ],
        'finance' => [
            'display_name' => 'Financial Management',
            'icon' => 'fa-chart-line',
            'route' => 'finance.index',
            'actions' => [
                'view' => 'View Financial Management',
                'view-overview' => 'View Finance Overview',
                'view-contributions' => 'View Contributions',
                'create-contribution' => 'Create Contributions',
                'edit-contribution' => 'Edit Contributions',
                'delete-contribution' => 'Delete Contributions',
                'export-contributions' => 'Export Contributions',
                'view-payments' => 'View Payments',
                'create-payment' => 'Create Payments',
                'edit-payment' => 'Edit Payments',
                'delete-payment' => 'Delete Payments',
                'approve-payment' => 'Approve Payments',
                'export-payments' => 'Export Payments',
                'view-sponsors' => 'View Sponsors',
                'create-sponsor' => 'Create Sponsors',
                'edit-sponsor' => 'Edit Sponsors',
                'delete-sponsor' => 'Delete Sponsors',
                'record-sponsor-payment' => 'Record Sponsor Payments',
                'export-sponsors' => 'Export Sponsors',
                'view-expenses' => 'View Expenses',
                'create-expense' => 'Create Expenses',
                'edit-expense' => 'Edit Expenses',
                'delete-expense' => 'Delete Expenses',
                'approve-expense' => 'Approve Expenses',
                'view-gifts' => 'View Gifts',
                'create-gift' => 'Create Gifts',
                'edit-gift' => 'Edit Gifts',
                'delete-gift' => 'Delete Gifts',
                'view-budget' => 'View Budget',
                'create-budget' => 'Create Budget',
                'edit-budget' => 'Edit Budget',
                'delete-budget' => 'Delete Budget',
                'view-action-plans' => 'View Finance Action Plans',
                'create-action-plan' => 'Create Finance Action Plans',
                'edit-action-plan' => 'Edit Finance Action Plans',
                'delete-action-plan' => 'Delete Finance Action Plans',
                'manage-action-plan-tasks' => 'Manage Finance Action Plan Tasks',
                'view-reports' => 'View Financial Reports',
                'export-reports' => 'Export Financial Reports',
                'manage-settings' => 'Manage Finance Settings',
            ],
        ],
        'announcements' => [
            'display_name' => 'Announcements',
            'icon' => 'fa-bullhorn',
            'route' => 'announcements.index',
            'actions' => [
                'view' => 'View Announcements',
                'view-recipients' => 'View Announcement Recipients',
                'create' => 'Create Announcements',
                'edit' => 'Edit Announcements',
                'delete' => 'Delete Announcements',
                'publish' => 'Publish Announcements',
                'resend' => 'Resend Announcements',
            ],
        ],
        'reports' => [
            'display_name' => 'Reports',
            'icon' => 'fa-chart-bar',
            'route' => 'reports.index',
            'actions' => [
                'view' => 'View Reports Module',
                'view-overview' => 'View Reports Overview',
                'view-user-reports' => 'View User Reports',
                'view-attendance-reports' => 'View Attendance Reports',
                'view-financial-reports' => 'View Financial Reports',
                'view-contribution-reports' => 'View Contribution Reports',
                'view-expense-reports' => 'View Expense Reports',
                'view-sponsor-reports' => 'View Sponsor Reports',
                'view-discipline-reports' => 'View Discipline Reports',
                'view-form-reports' => 'View Form Reports',
                'export-user-reports' => 'Export User Reports',
                'export-attendance-reports' => 'Export Attendance Reports',
                'export-financial-reports' => 'Export Financial Reports',
                'export-contribution-reports' => 'Export Contribution Reports',
                'export-expense-reports' => 'Export Expense Reports',
                'export-sponsor-reports' => 'Export Sponsor Reports',
            ],
        ],
        'settings' => [
            'display_name' => 'Settings',
            'icon' => 'fa-cog',
            'route' => 'settings.index',
            'actions' => [
                'view' => 'View System Settings',
                'update-general' => 'Update General Settings',
                'update-email' => 'Update Email Settings',
                'update-security' => 'Update Security Settings',
                'clear-cache' => 'Clear Application Cache',
                'backup-database' => 'Back Up Database',
            ],
        ],
    ];

    public function run(): void
    {
        DB::transaction(function (): void {
            $membershipBasedPages = Page::whereIn('name', ['family', 'parent', 'profile'])->get();

            foreach ($membershipBasedPages as $membershipBasedPage) {
                DB::table('role_page_features')->where('page_id', $membershipBasedPage->id)->delete();
                Feature::where('page_id', $membershipBasedPage->id)->delete();
                $membershipBasedPage->delete();
            }

            foreach ($this->modules as $index => $module) {
                $page = Page::updateOrCreate(
                    ['name' => $index],
                    [
                        'display_name' => $module['display_name'],
                        'icon' => $module['icon'],
                        'route' => $module['route'],
                        'sort_order' => array_search($index, array_keys($this->modules), true) + 1,
                        'is_active' => true,
                    ]
                );

                foreach ($module['actions'] as $name => $displayName) {
                    $feature = Feature::firstOrNew([
                        'page_id' => $page->id,
                        'name' => $name,
                    ]);
                    $feature->display_name = $displayName;
                    $feature->description = $displayName . ' in ' . $module['display_name'];
                    $feature->timestamps = false;

                    if (!$feature->exists) {
                        $feature->created_at = now();
                    }

                    $feature->save();
                }
            }

            $this->mergePermissionAliases();
            $this->mergeLegacySocialFellowshipPage();
        });

        $moduleCount = count($this->modules);
        $permissionCount = collect($this->modules)->sum(
            fn (array $module): int => count($module['actions'])
        );

        $this->command?->info("Seeded {$permissionCount} action permissions across {$moduleCount} modules.");
    }

    private function mergePermissionAliases(): void
    {
        foreach ($this->permissionAliases as $pageName => $aliases) {
            $page = Page::where('name', $pageName)->first();

            if (!$page) {
                continue;
            }

            foreach ($aliases as $aliasName => $canonicalName) {
                $alias = Feature::where('page_id', $page->id)->where('name', $aliasName)->first();
                $canonical = Feature::where('page_id', $page->id)->where('name', $canonicalName)->first();

                if (!$alias || !$canonical || $alias->id === $canonical->id) {
                    continue;
                }

                $roleIds = DB::table('role_page_features')
                    ->where('feature_id', $alias->id)
                    ->pluck('role_id');

                foreach ($roleIds as $roleId) {
                    DB::table('role_page_features')->updateOrInsert([
                        'role_id' => $roleId,
                        'page_id' => $page->id,
                        'feature_id' => $canonical->id,
                    ]);
                }

                DB::table('role_page_features')->where('feature_id', $alias->id)->delete();
                DB::table('features')->where('id', $alias->id)->delete();
            }
        }
    }

    private function mergeLegacySocialFellowshipPage(): void
    {
        $legacyPage = Page::where('name', 'fellowship')->first();
        $currentPage = Page::where('name', 'social-fellowship')->first();

        if (!$legacyPage || !$currentPage) {
            return;
        }

        $permissionMap = [
            'view' => 'view',
            'create' => 'create-family',
            'manage' => 'edit-family',
        ];

        foreach ($permissionMap as $legacyName => $currentName) {
            $legacyFeature = Feature::where('page_id', $legacyPage->id)
                ->where('name', $legacyName)
                ->first();
            $currentFeature = Feature::where('page_id', $currentPage->id)
                ->where('name', $currentName)
                ->first();

            if (!$legacyFeature || !$currentFeature) {
                continue;
            }

            $roleIds = DB::table('role_page_features')
                ->where('feature_id', $legacyFeature->id)
                ->pluck('role_id');

            foreach ($roleIds as $roleId) {
                DB::table('role_page_features')->updateOrInsert([
                    'role_id' => $roleId,
                    'page_id' => $currentPage->id,
                    'feature_id' => $currentFeature->id,
                ]);
            }
        }

        DB::table('role_page_features')->where('page_id', $legacyPage->id)->delete();
        DB::table('features')->where('page_id', $legacyPage->id)->delete();
        $legacyPage->delete();
    }
}

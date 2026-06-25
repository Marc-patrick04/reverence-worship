<?php

namespace App\Http\Controllers\Intercession;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User\User;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function index(Request $request)
    {
        try {
            // Get all users - dynamically select columns that exist
            $users = $this->getUsersWithStatus();
            
            // Get all forms that are published
            $allForms = DB::table('forms')
                
                ->orderBy('created_at', 'desc')
                ->get();

            // If no forms, return empty data
            if ($allForms->isEmpty()) {
                return view('modules.intercession.forms.reports', [
                    'reportData' => [],
                    'allForms' => collect(),
                    'selectedFormIds' => [],
                    'summary' => [
                        'total_users' => 0,
                        'complete' => 0,
                        'partial' => 0,
                        'not_started' => 0,
                    ],
                ]);
            }

            // Get selected form IDs from request or default to all
            $selectedFormIds = $request->get('form_ids', []);
            if (empty($selectedFormIds)) {
                $selectedFormIds = $allForms->pluck('id')->toArray();
            }

            // Get all submissions for these forms
            $submissions = DB::table('form_submissions')
                ->whereIn('form_id', $selectedFormIds)
                ->whereIn('user_id', $users->pluck('id'))
                ->select('user_id', 'form_id', 'score', 'submitted_at')
                ->get()
                ->groupBy('user_id');

            // Build report data
            $reportData = [];
            foreach ($users as $user) {
                $userSubmissions = $submissions->get($user->id, collect());
                $userData = [
                    'user' => $user,
                    'submissions' => [],
                    'total_submitted' => 0,
                    'total_forms' => count($selectedFormIds),
                    'percentage' => 0,
                    'status' => 'Not Started',
                ];

                // Check each form
                foreach ($allForms as $form) {
                    // Only track selected forms
                    if (!in_array($form->id, $selectedFormIds)) {
                        continue;
                    }

                    $submission = $userSubmissions->firstWhere('form_id', $form->id);
                    $isSubmitted = !is_null($submission);

                    $userData['submissions'][$form->id] = [
                        'submitted' => $isSubmitted,
                        'score' => $isSubmitted ? $submission->score : null,
                        'submitted_at' => $isSubmitted ? $submission->submitted_at : null,
                        'form_title' => $form->title,
                        'form_id' => $form->id
                    ];

                    if ($isSubmitted) {
                        $userData['total_submitted']++;
                    }
                }

                // Calculate percentage
                if ($userData['total_forms'] > 0) {
                    $userData['percentage'] = round(($userData['total_submitted'] / $userData['total_forms']) * 100);
                }

                // Determine status
                if ($userData['total_submitted'] == 0) {
                    $userData['status'] = 'Not Started';
                } elseif ($userData['total_submitted'] == $userData['total_forms']) {
                    $userData['status'] = 'Complete';
                } else {
                    $userData['status'] = 'Partial';
                }

                $reportData[] = $userData;
            }

            // Calculate summary stats
            $summary = [
                'total_users' => $users->count(),
                'complete' => collect($reportData)->filter(fn($d) => $d['status'] === 'Complete')->count(),
                'partial' => collect($reportData)->filter(fn($d) => $d['status'] === 'Partial')->count(),
                'not_started' => collect($reportData)->filter(fn($d) => $d['status'] === 'Not Started')->count(),
            ];

            return view('modules.intercession.forms.reports', [
                'reportData' => $reportData,
                'allForms' => $allForms,
                'selectedFormIds' => $selectedFormIds,
                'summary' => $summary,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Report index error: ' . $e->getMessage());
            
            return view('modules.intercession.forms.reports', [
                'reportData' => [],
                'allForms' => collect(),
                'selectedFormIds' => [],
                'summary' => [
                    'total_users' => 0,
                    'complete' => 0,
                    'partial' => 0,
                    'not_started' => 0,
                ],
            ]);
        }
    }

    /**
     * Get users with available status columns
     */
    private function getUsersWithStatus()
    {
        // Check which columns exist in the users table
        $table = 'users';
        $hasIsActive = Schema::hasColumn($table, 'is_active');
        $hasStatus = Schema::hasColumn($table, 'status');
        
        // Build select columns
        $columns = ['id', 'name', 'email', 'membership_type'];
        
        if ($hasIsActive) {
            $columns[] = 'is_active';
        }
        
        if ($hasStatus) {
            $columns[] = 'status';
        }
        
        // Get all users
        $allUsers = User::select($columns)->orderBy('name')->get();
        
        // Filter users: Must be Permanent AND Active (if columns exist)
        return $allUsers->filter(function($user) use ($hasIsActive, $hasStatus) {
            // Check if Permanent
            $isPermanent = ($user->membership_type ?? '') === 'Permanent';
            
            if (!$isPermanent) {
                return false;
            }
            
            // Check if Active (if column exists)
            $isActive = true;
            
            if ($hasIsActive && isset($user->is_active)) {
                $isActive = ($user->is_active == true || $user->is_active == 1);
            }
            
            if ($hasStatus && isset($user->status) && $isActive) {
                $isActive = ($user->status === 'active' || $user->status === 'Active');
            }
            
            return $isActive;
        })->values();
    }

    /**
     * Filter reports based on criteria
     */
    public function filter(Request $request)
    {
        try {
            $status = $request->get('status', 'all');
            $search = $request->get('search', '');
            $formIds = $request->get('form_ids', []);

            // If no form IDs, return empty
            if (empty($formIds)) {
                return response()->json([
                    'success' => true,
                    'reportData' => [],
                    'summary' => [
                        'total_users' => 0,
                        'complete' => 0,
                        'partial' => 0,
                        'not_started' => 0,
                    ],
                    'forms' => []
                ]);
            }

            // Check which columns exist
            $table = 'users';
            $hasIsActive = Schema::hasColumn($table, 'is_active');
            $hasStatus = Schema::hasColumn($table, 'status');
            
            // Build select columns
            $columns = ['id', 'name', 'email', 'membership_type'];
            
            if ($hasIsActive) {
                $columns[] = 'is_active';
            }
            
            if ($hasStatus) {
                $columns[] = 'status';
            }
            
            // Get users with search filter
            $query = User::select($columns);
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            $allUsers = $query->orderBy('name')->get();
            
            // Filter: Permanent AND Active
            $users = $allUsers->filter(function($user) use ($hasIsActive, $hasStatus) {
                // Check if Permanent
                $isPermanent = ($user->membership_type ?? '') === 'Permanent';
                
                if (!$isPermanent) {
                    return false;
                }
                
                // Check if Active (if column exists)
                $isActive = true;
                
                if ($hasIsActive && isset($user->is_active)) {
                    $isActive = ($user->is_active == true || $user->is_active == 1);
                }
                
                if ($hasStatus && isset($user->status) && $isActive) {
                    $isActive = ($user->status === 'active' || $user->status === 'Active');
                }
                
                return $isActive;
            })->values();

            // Get all forms details
            $allForms = DB::table('forms')
                ->whereIn('id', $formIds)
                ->orderBy('created_at', 'desc')
                ->get();

            // Get submissions
            $submissions = collect();
            if ($users->isNotEmpty()) {
                $submissions = DB::table('form_submissions')
                    ->whereIn('form_id', $formIds)
                    ->whereIn('user_id', $users->pluck('id'))
                    ->select('user_id', 'form_id', 'score', 'submitted_at')
                    ->get()
                    ->groupBy('user_id');
            }

            // Build report data
            $reportData = [];
            foreach ($users as $user) {
                $userSubmissions = $submissions->get($user->id, collect());
                $userData = [
                    'user' => $user,
                    'submissions' => [],
                    'total_submitted' => 0,
                    'total_forms' => count($formIds),
                    'percentage' => 0,
                    'status' => 'Not Started',
                ];

                foreach ($allForms as $form) {
                    $submission = $userSubmissions->firstWhere('form_id', $form->id);
                    $isSubmitted = !is_null($submission);

                    $userData['submissions'][$form->id] = [
                        'submitted' => $isSubmitted,
                        'score' => $isSubmitted ? $submission->score : null,
                        'submitted_at' => $isSubmitted ? $submission->submitted_at : null,
                        'form_title' => $form->title,
                        'form_id' => $form->id
                    ];

                    if ($isSubmitted) {
                        $userData['total_submitted']++;
                    }
                }

                if ($userData['total_forms'] > 0) {
                    $userData['percentage'] = round(($userData['total_submitted'] / $userData['total_forms']) * 100);
                }

                if ($userData['total_submitted'] == 0) {
                    $userData['status'] = 'Not Started';
                } elseif ($userData['total_submitted'] == $userData['total_forms']) {
                    $userData['status'] = 'Complete';
                } else {
                    $userData['status'] = 'Partial';
                }

                // Apply status filter
                if ($status && $status !== 'all' && $userData['status'] !== $status) {
                    continue;
                }

                $reportData[] = $userData;
            }

            // Calculate summary stats
            $summary = [
                'total_users' => $users->count(),
                'complete' => collect($reportData)->filter(fn($d) => $d['status'] === 'Complete')->count(),
                'partial' => collect($reportData)->filter(fn($d) => $d['status'] === 'Partial')->count(),
                'not_started' => collect($reportData)->filter(fn($d) => $d['status'] === 'Not Started')->count(),
            ];

            return response()->json([
                'success' => true,
                'reportData' => $reportData,
                'summary' => $summary,
                'forms' => $allForms
            ]);

        } catch (\Exception $e) {
            Log::error('Report filter error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error loading report data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export report as CSV
     */
    public function export(Request $request)
    {
        try {
            $formIds = $request->get('form_ids', []);
            
            if (empty($formIds)) {
                $formIds = DB::table('forms')
                    ->pluck('id')
                    ->toArray();
            }

            // Check which columns exist
            $table = 'users';
            $hasIsActive = Schema::hasColumn($table, 'is_active');
            $hasStatus = Schema::hasColumn($table, 'status');
            
            // Build select columns
            $columns = ['id', 'name', 'email', 'membership_type'];
            
            if ($hasIsActive) {
                $columns[] = 'is_active';
            }
            
            if ($hasStatus) {
                $columns[] = 'status';
            }
            
            // Get all users
            $allUsers = User::select($columns)->orderBy('name')->get();
            
            // Filter: Permanent AND Active
            $users = $allUsers->filter(function($user) use ($hasIsActive, $hasStatus) {
                // Check if Permanent
                $isPermanent = ($user->membership_type ?? '') === 'Permanent';
                
                if (!$isPermanent) {
                    return false;
                }
                
                // Check if Active (if column exists)
                $isActive = true;
                
                if ($hasIsActive && isset($user->is_active)) {
                    $isActive = ($user->is_active == true || $user->is_active == 1);
                }
                
                if ($hasStatus && isset($user->status) && $isActive) {
                    $isActive = ($user->status === 'active' || $user->status === 'Active');
                }
                
                return $isActive;
            })->values();

            $allForms = DB::table('forms')
                ->whereIn('id', $formIds)
                ->orderBy('created_at', 'desc')
                ->get();

            // If no users or forms, return empty CSV
            if ($users->isEmpty() || $allForms->isEmpty()) {
                $headers = ['User', 'Email', 'No Data Available'];
                $callback = function() use ($headers) {
                    $handle = fopen('php://output', 'w');
                    fputcsv($handle, $headers);
                    fclose($handle);
                };

                return response()->stream($callback, 200, [
                    'Content-Type' => 'text/csv; charset=utf-8',
                    'Content-Disposition' => 'attachment; filename="reports_empty.csv"',
                ]);
            }

            $submissions = DB::table('form_submissions')
                ->whereIn('form_id', $formIds)
                ->whereIn('user_id', $users->pluck('id'))
                ->select('user_id', 'form_id', 'score', 'submitted_at')
                ->get()
                ->groupBy('user_id');

            // Build CSV headers
            $headers = ['User', 'Email'];
            foreach ($allForms as $form) {
                $headers[] = $form->title;
            }
            $headers[] = 'Total Submitted';
            $headers[] = 'Progress';
            $headers[] = 'Status';

            $callback = function() use ($users, $allForms, $submissions, $headers, $formIds) {
                $handle = fopen('php://output', 'w');
                fwrite($handle, "\xEF\xBB\xBF");
                fputcsv($handle, $headers);

                foreach ($users as $user) {
                    $userSubmissions = $submissions->get($user->id, collect());
                    $row = [
                        $user->name,
                        $user->email,
                    ];

                    $totalSubmitted = 0;
                    foreach ($allForms as $form) {
                        $submission = $userSubmissions->firstWhere('form_id', $form->id);
                        if ($submission) {
                            $row[] = '✅';
                            $totalSubmitted++;
                        } else {
                            $row[] = '❌';
                        }
                    }

                    $percentage = count($formIds) > 0 ? round(($totalSubmitted / count($formIds)) * 100) : 0;

                    if ($totalSubmitted == 0) {
                        $status = 'Not Started';
                    } elseif ($totalSubmitted == count($formIds)) {
                        $status = 'Complete';
                    } else {
                        $status = 'Partial';
                    }

                    $row[] = $totalSubmitted . '/' . count($formIds);
                    $row[] = $percentage . '%';
                    $row[] = $status;

                    fputcsv($handle, $row);
                }

                fclose($handle);
            };

            $fileName = 'reports_' . date('Y-m-d_H-i-s') . '.csv';

            return response()->stream($callback, 200, [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);

        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error exporting report: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
 * Get user progress for the popup
 */
public function userProgress(Request $request)
{
    try {
        $userId = $request->get('user_id');
        $formIds = $request->get('form_ids', []);
        
        if (empty($userId) || empty($formIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Missing required parameters'
            ], 400);
        }
        
        // Get user
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        // Get forms
        $allForms = DB::table('forms')
            ->whereIn('id', $formIds)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get user submissions
        $submissions = DB::table('form_submissions')
            ->where('user_id', $userId)
            ->whereIn('form_id', $formIds)
            ->select('form_id', 'score', 'submitted_at')
            ->get()
            ->keyBy('form_id');
        
        $formData = [];
        $submittedCount = 0;
        
        foreach ($allForms as $form) {
            $submission = $submissions->get($form->id);
            $isSubmitted = !is_null($submission);
            
            if ($isSubmitted) {
                $submittedCount++;
            }
            
            $formData[] = [
                'id' => $form->id,
                'title' => $form->title,
                'submitted' => $isSubmitted,
                'submitted_at' => $isSubmitted ? \Carbon\Carbon::parse($submission->submitted_at)->format('M d, Y h:i A') : null,
                'score' => $isSubmitted ? $submission->score : null
            ];
        }
        
        $total = count($formIds);
        $percentage = $total > 0 ? round(($submittedCount / $total) * 100) : 0;
        
        if ($submittedCount == 0) {
            $status = 'Not Started';
        } elseif ($submittedCount == $total) {
            $status = 'Complete';
        } else {
            $status = 'Partial';
        }
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'forms' => $formData,
            'submitted' => $submittedCount,
            'total' => $total,
            'percentage' => $percentage,
            'status' => $status
        ]);
        
    } catch (\Exception $e) {
        Log::error('User progress error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}
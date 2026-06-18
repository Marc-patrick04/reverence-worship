<?php

namespace App\Http\Controllers\Intercession;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User\User;

class FormController extends Controller
{
    // Display manage forms index
    public function index()
    {
        // Check permission
        if (!auth()->user()->canAccess('intercession', 'manage-forms')) {
            abort(403, 'You do not have permission to manage forms.');
        }
        
        $forms = DB::table('forms')->orderBy('created_at', 'desc')->get();
        return view('modules.intercession.forms.index', compact('forms'));
    }
    
    // Display create form page
    public function create()
    {
        // Check permission
        if (!auth()->user()->canAccess('intercession', 'create-forms')) {
            abort(403, 'You do not have permission to create forms.');
        }
        
        return view('modules.intercession.forms.create');
    }
    
    // Store new form
   // Store new form
public function store(Request $request)
{
    // Check permission
    if (!auth()->user()->canAccess('intercession', 'create-forms')) {
        return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
    }
    
    try {
        // Get JSON data from your form builder
        if ($request->isJson()) {
            $data = $request->json()->all();
        } else {
            $data = $request->all();
        }
        
        $title = $data['title'] ?? 'Untitled form';
        $description = $data['description'] ?? '';
        
        // Clean and filter questions - remove null/empty options
        $questions = [];
        if (isset($data['questions']) && is_array($data['questions'])) {
            foreach ($data['questions'] as $q) {
                $cleanQuestion = [];
                
                // Only include valid fields
                if (isset($q['type'])) $cleanQuestion['type'] = $q['type'];
                if (isset($q['text']) && !empty($q['text'])) $cleanQuestion['text'] = $q['text'];
                if (isset($q['title']) && !empty($q['title'])) $cleanQuestion['title'] = $q['title'];
                if (isset($q['description']) && !empty($q['description'])) $cleanQuestion['description'] = $q['description'];
                if (isset($q['imageUrl']) && !empty($q['imageUrl'])) $cleanQuestion['imageUrl'] = $q['imageUrl'];
                if (isset($q['altText']) && !empty($q['altText'])) $cleanQuestion['altText'] = $q['altText'];
                if (isset($q['required'])) $cleanQuestion['required'] = (bool)$q['required'];
                if (isset($q['points'])) $cleanQuestion['points'] = (int)$q['points'];
                
                // Clean options - remove null values
                if (isset($q['options']) && is_array($q['options'])) {
                    $cleanOptions = array_filter($q['options'], function($opt) {
                        return $opt !== null && $opt !== 'null' && !empty($opt);
                    });
                    if (!empty($cleanOptions)) {
                        $cleanQuestion['options'] = array_values($cleanOptions);
                    }
                }
                
                // Clean rows and columns for grid questions
                if (isset($q['rows']) && is_array($q['rows'])) {
                    $cleanRows = array_filter($q['rows'], function($row) {
                        return $row !== null && !empty($row);
                    });
                    if (!empty($cleanRows)) $cleanQuestion['rows'] = array_values($cleanRows);
                }
                
                if (isset($q['columns']) && is_array($q['columns'])) {
                    $cleanCols = array_filter($q['columns'], function($col) {
                        return $col !== null && !empty($col);
                    });
                    if (!empty($cleanCols)) $cleanQuestion['columns'] = array_values($cleanCols);
                }
                
                // Handle scale values
                if (isset($q['min'])) $cleanQuestion['min'] = (int)$q['min'];
                if (isset($q['max'])) $cleanQuestion['max'] = (int)$q['max'];
                if (isset($q['minLabel'])) $cleanQuestion['minLabel'] = $q['minLabel'];
                if (isset($q['maxLabel'])) $cleanQuestion['maxLabel'] = $q['maxLabel'];
                
                // ✅ FIX: Handle correct answers for quizzes
                // Single correct answer (multiple choice, dropdown, short answer, date, time)
                if (isset($q['correctAnswer']) && !empty($q['correctAnswer'])) {
                    $cleanQuestion['correctAnswer'] = $q['correctAnswer'];
                }
                
                // ✅ FIX: Multiple correct answers (checkboxes)
                if (isset($q['correctAnswers']) && is_array($q['correctAnswers'])) {
                    $cleanAnswers = array_filter($q['correctAnswers'], function($ans) {
                        return $ans !== null && !empty($ans);
                    });
                    if (!empty($cleanAnswers)) {
                        $cleanQuestion['correctAnswers'] = array_values($cleanAnswers);
                    }
                }
                
                $questions[] = $cleanQuestion;
            }
        }
        
        // Clean settings
        $settings = [];
        if (isset($data['settings']) && is_array($data['settings'])) {
            $settings = $data['settings'];
        }
        if (!isset($settings['is_published'])) $settings['is_published'] = false;
        
        $id = DB::table('forms')->insertGetId([
            'title' => $title,
            'description' => $description,
            'questions' => json_encode($questions),
            'settings' => json_encode($settings),
            'is_active' => true,
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'form_id' => $id,
            'message' => 'Form created successfully'
        ]);
        
    } catch (\Exception $e) {
        Log::error('Form store error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    
    // Display edit form page
    public function edit($id)
    {
        // Check permission
        if (!auth()->user()->canAccess('intercession', 'edit-forms')) {
            abort(403, 'You do not have permission to edit forms.');
        }
        
        $form = DB::table('forms')->where('id', $id)->first();
        
        if (!$form) {
            abort(404);
        }
        
        // Decode questions and settings for the form builder
        $form->questions = json_decode($form->questions, true);
        $form->settings = json_decode($form->settings, true);
        
        return view('modules.intercession.forms.edit', compact('form'));
    }
    
    // Update existing form
    // Update existing form
public function update(Request $request, $id)
{
    // Check permission
    if (!auth()->user()->canAccess('intercession', 'edit-forms')) {
        return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
    }
    
    try {
        if ($request->isJson()) {
            $data = $request->json()->all();
        } else {
            $data = $request->all();
        }
        
        $title = $data['title'] ?? 'Untitled form';
        $description = $data['description'] ?? '';
        
        // Clean and filter questions
        $questions = [];
        if (isset($data['questions']) && is_array($data['questions'])) {
            foreach ($data['questions'] as $q) {
                $cleanQuestion = [];
                
                if (isset($q['type'])) $cleanQuestion['type'] = $q['type'];
                if (isset($q['text']) && !empty($q['text'])) $cleanQuestion['text'] = $q['text'];
                if (isset($q['title']) && !empty($q['title'])) $cleanQuestion['title'] = $q['title'];
                if (isset($q['description']) && !empty($q['description'])) $cleanQuestion['description'] = $q['description'];
                if (isset($q['imageUrl']) && !empty($q['imageUrl'])) $cleanQuestion['imageUrl'] = $q['imageUrl'];
                if (isset($q['required'])) $cleanQuestion['required'] = (bool)$q['required'];
                if (isset($q['points'])) $cleanQuestion['points'] = (int)$q['points'];
                
                if (isset($q['options']) && is_array($q['options'])) {
                    $cleanOptions = array_filter($q['options'], function($opt) {
                        return $opt !== null && $opt !== 'null' && !empty($opt);
                    });
                    if (!empty($cleanOptions)) $cleanQuestion['options'] = array_values($cleanOptions);
                }
                
                if (isset($q['rows']) && is_array($q['rows'])) {
                    $cleanRows = array_filter($q['rows'], function($row) {
                        return $row !== null && !empty($row);
                    });
                    if (!empty($cleanRows)) $cleanQuestion['rows'] = array_values($cleanRows);
                }
                
                if (isset($q['columns']) && is_array($q['columns'])) {
                    $cleanCols = array_filter($q['columns'], function($col) {
                        return $col !== null && !empty($col);
                    });
                    if (!empty($cleanCols)) $cleanQuestion['columns'] = array_values($cleanCols);
                }
                
                if (isset($q['min'])) $cleanQuestion['min'] = (int)$q['min'];
                if (isset($q['max'])) $cleanQuestion['max'] = (int)$q['max'];
                
                // ✅ FIX: Handle single correct answer (multiple choice, dropdown, short answer, date, time)
                if (isset($q['correctAnswer']) && !empty($q['correctAnswer'])) {
                    $cleanQuestion['correctAnswer'] = $q['correctAnswer'];
                }
                
                // ✅ FIX: Handle multiple correct answers (checkboxes)
                if (isset($q['correctAnswers']) && is_array($q['correctAnswers'])) {
                    $cleanAnswers = array_filter($q['correctAnswers'], function($ans) {
                        return $ans !== null && !empty($ans);
                    });
                    if (!empty($cleanAnswers)) {
                        $cleanQuestion['correctAnswers'] = array_values($cleanAnswers);
                    }
                }
                
                $questions[] = $cleanQuestion;
            }
        }
        
        $settings = $data['settings'] ?? [];
        
        DB::table('forms')->where('id', $id)->update([
            'title' => $title,
            'description' => $description,
            'questions' => json_encode($questions),
            'settings' => json_encode($settings),
            'updated_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Form updated successfully'
        ]);
        
    } catch (\Exception $e) {
        Log::error('Form update error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    
    // Delete form
    public function destroy($id)
    {
        // Check permission
        if (!auth()->user()->canAccess('intercession', 'delete-forms')) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
            }
            abort(403, 'You do not have permission to delete forms.');
        }
        
        try {
            DB::table('form_submissions')->where('form_id', $id)->delete();
            DB::table('forms')->where('id', $id)->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->back()->with('success', 'Form deleted successfully');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // Toggle publish status
    public function togglePublish($id)
    {
        // Check permission
        if (!auth()->user()->canAccess('intercession', 'publish-forms')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }
        
        try {
            $form = DB::table('forms')->where('id', $id)->first();
            $settings = json_decode($form->settings, true);
            $settings['is_published'] = !($settings['is_published'] ?? false);
            
            DB::table('forms')->where('id', $id)->update([
                'settings' => json_encode($settings)
            ]);
            
            return response()->json([
                'success' => true,
                'is_published' => $settings['is_published']
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // Take a form (view and fill)
    public function take($id)
    {
        // Check permission - must have view-forms permission
        if (!auth()->user()->canAccess('intercession', 'view-forms')) {
            abort(403, 'You do not have permission to view forms.');
        }
        
        $form = DB::table('forms')->where('id', $id)->first();
        if (!$form) abort(404);
        
        $questions = json_decode($form->questions, true);
        $settings = json_decode($form->settings, true);
        
        // Check if user has already submitted (if limit_one_response is enabled)
        $hasSubmitted = DB::table('form_submissions')
            ->where('form_id', $id)
            ->where('user_id', auth()->id())
            ->exists();
        
        if ($hasSubmitted && isset($settings['limit_one_response']) && $settings['limit_one_response']) {
            return redirect()->route('intercession.index')
                ->with('error', 'You have already submitted this form. Only one response is allowed.');
        }
        
        return view('modules.intercession.forms.take', compact('form', 'questions', 'settings'));
    }
    
    // Submit form answers
   // Submit form answers
public function submit(Request $request, $id)
{
    // Check permission
    if (!auth()->user()->canAccess('intercession', 'view-forms')) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }
        abort(403, 'You do not have permission to submit forms.');
    }
    
    try {
        $answers = json_encode($request->except('_token'));
        
        $form = DB::table('forms')->where('id', $id)->first();
        if (!$form) {
            return response()->json(['success' => false, 'message' => 'Form not found'], 404);
        }
        
        $settings = json_decode($form->settings, true);
        $questions = json_decode($form->questions, true);
        
        // Check if already submitted
        $hasSubmitted = DB::table('form_submissions')
            ->where('form_id', $id)
            ->where('user_id', auth()->id())
            ->exists();
        
        if ($hasSubmitted && isset($settings['limit_one_response']) && $settings['limit_one_response']) {
            return response()->json(['success' => false, 'message' => 'You have already submitted this form'], 400);
        }
        
        // Calculate score with partial grading for checkboxes
        $score = 0;
        $totalPoints = 0;
        $earnedPoints = 0;
        
        if (isset($settings['is_quiz']) && $settings['is_quiz']) {
            foreach ($questions as $index => $question) {
                $points = $question['points'] ?? 1;
                $totalPoints += $points;
                $userAnswer = $request->input('question_' . $index);
                $questionType = $question['type'] ?? 'short_answer';
                
                // Skip sections
                if ($questionType == 'title_section' || $questionType == 'section_break') {
                    continue;
                }
                
                // Handle different question types
                if (isset($question['correctAnswer']) && !empty($question['correctAnswer'])) {
                    // Single correct answer (multiple choice, dropdown, short answer, date, time)
                    if ($questionType == 'short_answer' || $questionType == 'paragraph') {
                        // Case-insensitive comparison for text answers
                        if (strtolower(trim($userAnswer)) == strtolower(trim($question['correctAnswer']))) {
                            $earnedPoints += $points;
                        }
                    } else {
                        // Exact match for other types
                        if ($userAnswer == $question['correctAnswer']) {
                            $earnedPoints += $points;
                        }
                    }
                } elseif (isset($question['correctAnswers']) && is_array($question['correctAnswers']) && !empty($question['correctAnswers'])) {
                    // ✅ FIX: Multiple correct answers (checkboxes) - ONLY COUNT CORRECT SELECTIONS
                    $userAnswers = is_array($userAnswer) ? $userAnswer : [];
                    $correctAnswers = $question['correctAnswers'];
                    
                    if (count($userAnswers) > 0) {
                        $totalCorrectCount = count($correctAnswers);
                        $userCorrectCount = 0;
                        
                        // Count how many correct answers the user selected
                        foreach ($userAnswers as $answer) {
                            if (in_array($answer, $correctAnswers)) {
                                $userCorrectCount++;
                            }
                        }
                        
                        // ✅ FIX: Calculate points - ONLY for correct selections, no subtraction
                        // Each correct selection gives (points / total_correct_answers)
                        $pointsPerCorrect = $points / $totalCorrectCount;
                        $earnedPoints += $userCorrectCount * $pointsPerCorrect;
                        
                        // ✅ Ensure points don't exceed the question's total points
                        if ($earnedPoints > $points) {
                            $earnedPoints = $points;
                        }
                    }
                }
            }
            
            $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 1) : 100;
        } else {
            $score = 100;
        }
        
        DB::table('form_submissions')->insert([
            'form_id' => $id,
            'user_id' => auth()->id(),
            'answers' => $answers,
            'score' => $score,
            'submitted_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Form submitted successfully',
                'score' => $score,
                'form_id' => $id
            ]);
        }
        
        return redirect()->route('forms.results', $id)->with('success', 'Form submitted successfully! Your score: ' . $score . '%');
        
    } catch (\Exception $e) {
        Log::error('Form submission error: ' . $e->getMessage());
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
        return redirect()->back()->with('error', 'Error submitting form: ' . $e->getMessage());
    }
}
    
    // View submissions for a form (admin)
    public function submissions($id)
    {
        // Check permission
        if (!auth()->user()->canAccess('intercession', 'view-results')) {
            abort(403, 'You do not have permission to view submissions.');
        }
        
        $submissions = DB::table('form_submissions')
            ->where('form_id', $id)
            ->leftJoin('users', 'form_submissions.user_id', '=', 'users.id')
            ->select('form_submissions.*', 'users.name as user_name', 'users.email')
            ->orderBy('submitted_at', 'desc')
            ->get();
        
        $form = DB::table('forms')->where('id', $id)->first();
        return view('modules.intercession.forms.submissions', compact('submissions', 'form'));
    }
    
    // View results for a specific submission (user)
    public function results($id)
    {
        // Check permission
        if (!auth()->user()->canAccess('intercession', 'view-results')) {
            abort(403, 'You do not have permission to view results.');
        }
        
        $submission = DB::table('form_submissions')
            ->where('form_id', $id)
            ->where('user_id', auth()->id())
            ->first();
        
        if (!$submission) {
            return redirect()->route('intercession.index')->with('error', 'No submission found');
        }
        
        $form = DB::table('forms')->where('id', $id)->first();
        $questions = json_decode($form->questions, true);
        $answers = json_decode($submission->answers, true);
        
        return view('modules.intercession.forms.results', compact('form', 'questions', 'answers', 'submission'));
    }
}
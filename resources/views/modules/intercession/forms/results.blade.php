@extends('layouts.app')

@section('title', 'Form Results')
@section('page-title', 'Form Results')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('intercession.index') }}#forms-tab" class="inline-flex items-center text-gray-600 hover:text-blue-600 transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to My Results
        </a>
    </div>
    
    <!-- Results Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $form->title }}</h1>
                    <p class="text-blue-100 text-sm mt-1">Form Results</p>
                </div>
                <div class="text-right">
                    @if(isset($submission) && isset($submission->submitted_at))
                    <p class="text-blue-200 text-xs">Submitted</p>
                    <p class="text-white text-sm font-medium">{{ \Carbon\Carbon::parse($submission->submitted_at)->format('M d, Y h:i A') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Score Summary -->
        @php
            // Get settings
            $settings = json_decode($form->settings, true) ?? [];
            $isQuiz = $settings['is_quiz'] ?? false;
            $releaseGrade = $settings['release_grade'] ?? 'immediately';
            $allowViewResponse = $settings['allow_view_response'] ?? true;
            $allowPartialPoints = $settings['allow_partial_points'] ?? true;
            
            // Check if submission is released - Check BOTH column types
            $isReleased = false;
            if (isset($submission)) {
                if (isset($submission->released_at) && $submission->released_at !== null) {
                    $isReleased = true;
                }
                if (isset($submission->is_released) && $submission->is_released == true) {
                    $isReleased = true;
                }
            }
            
            // Determine if score and answers should be shown
            $showScore = false;
            $showAnswers = false;
            
            if ($releaseGrade == 'immediately') {
                $showScore = true;
                $showAnswers = true;
            } elseif ($releaseGrade == 'later') {
                if ($isReleased) {
                    $showScore = true;
                    $showAnswers = true;
                } else {
                    $showScore = false;
                    $showAnswers = false;
                }
            } elseif ($releaseGrade == 'never') {
                $showScore = false;
                $showAnswers = false;
            } else {
                $showScore = $isReleased || $releaseGrade == 'immediately';
                $showAnswers = $isReleased || $releaseGrade == 'immediately';
            }
            
            // Get user name
            $userName = 'Unknown User';
            if (isset($submission) && isset($submission->user_id)) {
                $user = DB::table('users')->where('id', $submission->user_id)->first();
                if ($user) {
                    $userName = $user->name;
                }
            }
            
            // Check for multiple submissions
            $submissionCount = 0;
            if ($isQuiz && !isset($submission_id)) {
                $submissionCount = DB::table('form_submissions')
                    ->where('form_id', $form->id)
                    ->where('user_id', auth()->id())
                    ->count();
            }
            
            // Calculate total possible points - SKIP SECTIONS
            $totalPossiblePoints = 0;
            $earnedPoints = 0;
            $totalQuestionsCount = 0;
            $questionScores = [];
            
            foreach ($questions as $index => $question) {
                $questionType = $question['type'] ?? 'short_answer';
                if ($questionType == 'title_section' || $questionType == 'section_break') {
                    continue;
                }
                
                $totalQuestionsCount++;
                $points = isset($question['points']) ? (int)$question['points'] : 1;
                $totalPossiblePoints += $points;
                
                $answerKey = 'question_' . $index;
                $answer = $answers[$answerKey] ?? null;
                
                // For grid questions, collect ALL answers with the question prefix
                $gridAnswers = null;
                if ($questionType == 'multiple_choice_grid' || $questionType == 'checkbox_grid') {
                    $gridAnswers = [];
                    $rows = $question['rows'] ?? [];
                    foreach ($rows as $rowIndex => $row) {
                        $rowKey = 'question_' . $index . '_' . $rowIndex;
                        if (isset($answers[$rowKey])) {
                            $gridAnswers[$rowKey] = $answers[$rowKey];
                        }
                    }
                    if (empty($gridAnswers) && is_array($answer)) {
                        $gridAnswers = $answer;
                    }
                }
                
                $questionEarnedPoints = 0;
                $isCorrect = false;
                $correctDisplay = '';
                $userAnswerDisplay = '';
                $isPartiallyCorrect = false;
                $storedRows = null;
                $storedColumns = null;
                
                // Format user answer for display
                if (is_array($answer)) {
                    $userAnswerDisplay = implode(', ', $answer);
                } else {
                    $userAnswerDisplay = $answer ?: 'Not answered';
                }
                
                // If it's a grid question and we have grid answers, format display properly
                if ($questionType == 'multiple_choice_grid' || $questionType == 'checkbox_grid') {
                    if (!empty($gridAnswers) && is_array($gridAnswers)) {
                        $displayParts = [];
                        $rows = $question['rows'] ?? [];
                        foreach ($gridAnswers as $key => $value) {
                            $rowIndex = str_replace('question_' . $index . '_', '', $key);
                            $rowLabel = isset($rows[$rowIndex]) ? $rows[$rowIndex] : 'Row ' . ($rowIndex + 1);
                            if (is_array($value)) {
                                $displayParts[] = $rowLabel . ': ' . implode(', ', $value);
                            } else {
                                $displayParts[] = $rowLabel . ': ' . $value;
                            }
                        }
                        if (!empty($displayParts)) {
                            $userAnswerDisplay = implode('; ', $displayParts);
                        }
                    }
                }
                
                // Handle different question types
                if ($questionType == 'multiple_choice' || $questionType == 'dropdown') {
                    // DEBUG: Check if correct answer exists
                    if (isset($question['correctAnswer']) && $question['correctAnswer'] !== '') {
                        $correctDisplay = $question['correctAnswer'];
                        if ($answer == $question['correctAnswer']) {
                            $isCorrect = true;
                            $questionEarnedPoints = $points;
                        }
                    }
                } elseif ($questionType == 'checkboxes') {
                    if (isset($question['correctAnswers']) && is_array($question['correctAnswers']) && !empty($question['correctAnswers'])) {
                        $correctDisplay = implode(', ', $question['correctAnswers']);
                        $correctAnswers = $question['correctAnswers'];
                        $userAnswers = is_array($answer) ? $answer : [];
                        
                        if (!empty($userAnswers)) {
                            $totalCorrect = count($correctAnswers);
                            $correctSelected = 0;
                            
                            foreach ($userAnswers as $userAnswer) {
                                if (in_array($userAnswer, $correctAnswers)) {
                                    $correctSelected++;
                                }
                            }
                            
                            if ($allowPartialPoints) {
                                if ($correctSelected > 0) {
                                    $questionEarnedPoints = ($correctSelected / $totalCorrect) * $points;
                                } else {
                                    $questionEarnedPoints = 0;
                                }
                                $questionEarnedPoints = round($questionEarnedPoints, 2);
                                
                                if ($correctSelected > 0 && $correctSelected < $totalCorrect) {
                                    $isPartiallyCorrect = true;
                                }
                            } else {
                                if ($correctSelected == $totalCorrect) {
                                    $questionEarnedPoints = $points;
                                    $isCorrect = true;
                                } else {
                                    $questionEarnedPoints = 0;
                                }
                            }
                            
                            if ($correctSelected == $totalCorrect) {
                                $isCorrect = true;
                            }
                        }
                    }
                } elseif ($questionType == 'short_answer' || $questionType == 'paragraph') {
                    if (isset($question['correctAnswer']) && $question['correctAnswer'] !== '') {
                        $correctDisplay = $question['correctAnswer'];
                        if (strtolower(trim($answer)) == strtolower(trim($question['correctAnswer']))) {
                            $isCorrect = true;
                            $questionEarnedPoints = $points;
                        }
                    }
                } elseif ($questionType == 'date' || $questionType == 'time') {
                    if (isset($question['correctAnswer']) && $question['correctAnswer'] !== '') {
                        $correctDisplay = $question['correctAnswer'];
                        if ($answer == $question['correctAnswer']) {
                            $isCorrect = true;
                            $questionEarnedPoints = $points;
                        }
                    }
                } elseif ($questionType == 'linear_scale') {
                    if (isset($question['correctAnswer']) && $question['correctAnswer'] !== '') {
                        $correctDisplay = $question['correctAnswer'];
                        if ($answer == $question['correctAnswer']) {
                            $isCorrect = true;
                            $questionEarnedPoints = $points;
                        }
                    } else {
                        if ($answer !== null && $answer !== '') {
                            $questionEarnedPoints = $points;
                            $isCorrect = true;
                        }
                    }
                } elseif ($questionType == 'rating') {
                    if (isset($question['correctAnswer']) && $question['correctAnswer'] !== '') {
                        $correctDisplay = $question['correctAnswer'];
                        if ($answer == $question['correctAnswer']) {
                            $isCorrect = true;
                            $questionEarnedPoints = $points;
                        }
                    } else {
                        if ($answer !== null && $answer !== '') {
                            $questionEarnedPoints = $points;
                            $isCorrect = true;
                        }
                    }
                } elseif ($questionType == 'multiple_choice_grid') {
                    $rows = $question['rows'] ?? [];
                    $columns = $question['columns'] ?? [];
                    $correctAnswers = $question['correctAnswers'] ?? [];
                    $storedRows = $rows;
                    $storedColumns = $columns;
                    
                    if (!empty($correctAnswers) && is_array($correctAnswers)) {
                        $gridEarned = 0;
                        $totalRows = count($rows);
                        $rowPoints = $totalRows > 0 ? $points / $totalRows : 0;
                        $allCorrect = true;
                        $correctDisplayParts = [];
                        
                        foreach ($rows as $rowIndex => $row) {
                            $rowKey = 'question_' . $index . '_' . $rowIndex;
                            $userRowAnswer = $gridAnswers[$rowKey] ?? null;
                            $correctRowAnswer = $correctAnswers[$rowIndex] ?? null;
                            
                            if ($correctRowAnswer !== null && $correctRowAnswer !== '') {
                                $correctDisplayParts[] = $row . ': ' . $correctRowAnswer;
                                if ($userRowAnswer == $correctRowAnswer) {
                                    $gridEarned += $rowPoints;
                                } else {
                                    $allCorrect = false;
                                }
                            }
                        }
                        $correctDisplay = implode(', ', $correctDisplayParts);
                        $questionEarnedPoints = round($gridEarned, 2);
                        $isCorrect = $allCorrect && $gridEarned > 0;
                    }
                } elseif ($questionType == 'checkbox_grid') {
                    $rows = $question['rows'] ?? [];
                    $columns = $question['columns'] ?? [];
                    $correctAnswers = $question['correctAnswers'] ?? [];
                    $storedRows = $rows;
                    $storedColumns = $columns;
                    
                    if (!empty($correctAnswers) && is_array($correctAnswers)) {
                        $gridEarned = 0;
                        $totalRows = count($rows);
                        $rowPoints = $totalRows > 0 ? $points / $totalRows : 0;
                        $allCorrect = true;
                        $correctDisplayParts = [];
                        
                        foreach ($rows as $rowIndex => $row) {
                            $rowKey = 'question_' . $index . '_' . $rowIndex;
                            $userRowAnswers = isset($gridAnswers[$rowKey]) ? (array)$gridAnswers[$rowKey] : [];
                            $correctRowAnswers = $correctAnswers[$rowIndex] ?? [];
                            
                            if (!empty($correctRowAnswers)) {
                                $correctDisplayParts[] = $row . ': ' . implode(', ', $correctRowAnswers);
                                
                                if (!empty($userRowAnswers)) {
                                    $correctCount = 0;
                                    foreach ($correctRowAnswers as $correctAns) {
                                        if (in_array($correctAns, $userRowAnswers)) {
                                            $correctCount++;
                                        }
                                    }
                                    if ($allowPartialPoints) {
                                        $gridEarned += ($correctCount / count($correctRowAnswers)) * $rowPoints;
                                    } else {
                                        if ($correctCount == count($correctRowAnswers)) {
                                            $gridEarned += $rowPoints;
                                        }
                                    }
                                    if ($correctCount != count($correctRowAnswers)) {
                                        $allCorrect = false;
                                    }
                                } else {
                                    $allCorrect = false;
                                }
                            }
                        }
                        $correctDisplay = implode(', ', $correctDisplayParts);
                        $questionEarnedPoints = round($gridEarned, 2);
                        $isCorrect = $allCorrect && $gridEarned > 0;
                    }
                }
                
                $earnedPoints += $questionEarnedPoints;
                
                $questionScores[$index] = [
                    'earned' => $questionEarnedPoints,
                    'total' => $points,
                    'is_correct' => $isCorrect,
                    'is_partial' => $isPartiallyCorrect ?? false,
                    'correct_display' => $correctDisplay,
                    'user_answer' => $userAnswerDisplay,
                    'type' => $questionType,
                    'grid_answers' => $gridAnswers,
                    'rows' => $storedRows,
                    'columns' => $storedColumns
                ];
            }
            
            $earnedPoints = round($earnedPoints, 2);
            $scorePercentage = $totalPossiblePoints > 0 ? ($earnedPoints / $totalPossiblePoints) * 100 : 0;
            
            
            
            // Get release status message
            $releaseStatusMessage = '';
            $releaseStatusIcon = '';
            if ($releaseGrade == 'immediately') {
                $releaseStatusMessage = 'Auto-graded';
                $releaseStatusIcon = 'fa-check-circle text-green-500';
            } elseif ($releaseGrade == 'later') {
                if ($isReleased) {
                    $releaseStatusMessage = 'Released on ' . \Carbon\Carbon::parse($submission->released_at)->format('M d, Y h:i A');
                    $releaseStatusIcon = 'fa-check-circle text-green-500';
                } else {
                    $releaseStatusMessage = 'Pending Review';
                    $releaseStatusIcon = 'fa-clock text-yellow-500';
                }
            } elseif ($releaseGrade == 'never') {
                $releaseStatusMessage = 'Private';
                $releaseStatusIcon = 'fa-lock text-gray-500';
            }
        @endphp

        <!-- Multiple Submissions Notice -->
        @if($isQuiz && $submissionCount > 1 && !isset($submission_id))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mx-6 mt-4 flex items-center gap-3">
                <i class="fas fa-info-circle text-blue-500"></i>
                <span class="text-sm text-blue-700">
                    Showing latest submission ({{ $submissionCount }} total submissions)
                </span>
                <a href="{{ route('forms.manage.submissions', $form->id) }}" class="text-sm text-blue-600 hover:underline ml-auto">
                    View all submissions
                </a>
            </div>
        @endif
        
        <!-- Release Status Banner -->
        <div class="px-6 py-3 border-b flex items-center gap-3 {{ $showScore ? 'bg-green-50' : 'bg-yellow-50' }}">
            <i class="fas {{ $releaseStatusIcon }} {{ $showScore ? 'text-green-500' : 'text-yellow-500' }}"></i>
            <span class="text-sm {{ $showScore ? 'text-green-700' : 'text-yellow-700' }}">
                <strong>Status:</strong> {{ $releaseStatusMessage }}
                @if($isQuiz && $releaseGrade == 'later' && $isReleased)
                    <span class="text-xs text-green-600 ml-2">
                        <i class="fas fa-check-circle mr-1"></i> Score released
                    </span>
                @endif
            </span>
        </div>
        
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-5 border-b">
            @if($showScore)
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-center md:text-left">
                        <p class="text-gray-600 text-sm">Score</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-blue-600">{{ number_format($scorePercentage, 1) }}%</span>
                            <span class="text-gray-400 text-sm">/ 100%</span>
                        </div>
                        <div class="mt-2 flex items-center justify-center md:justify-start gap-2">
                            <i class="fas fa-user-circle text-blue-500 text-2xl"></i>
                            <span class="text-xl font-bold text-gray-700">{{ $userName }}</span>
                        </div>
                    </div>
                    
                    <div class="w-px h-12 bg-gray-300 hidden md:block"></div>
                    
                    <div class="text-center">
                        <p class="text-gray-600 text-sm">Points</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($earnedPoints, 1) }}</p>
                        <p class="text-xs text-gray-400">out of {{ $totalPossiblePoints }} total points</p>
                    </div>
                    
                    <div class="w-px h-12 bg-gray-300 hidden md:block"></div>
                    
                    <div class="text-center">
                        <p class="text-gray-600 text-sm">Questions</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $totalQuestionsCount }}</p>
                        <p class="text-xs text-gray-400">total questions</p>
                    </div>
                    
                </div>
                
                <div class="mt-4">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Progress</span>
                        <span>{{ number_format($scorePercentage, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $scorePercentage }}%"></div>
                    </div>
                </div>
            @else
                <div class="text-center py-6">
                    <i class="fas fa-clock text-yellow-400 text-5xl mb-3"></i>
                    <p class="text-gray-500 text-lg font-medium">Results are pending review</p>
                    @if($releaseGrade == 'later')
                        <p class="text-sm text-gray-400 mt-1">Your results will be available after manual review</p>
                        <div class="mt-4 inline-flex items-center gap-2 bg-yellow-100 px-4 py-2 rounded-lg">
                            <i class="fas fa-clock text-yellow-600"></i>
                            <span class="text-sm text-yellow-700">Please check back later</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
        
        <!-- Questions & Answers -->
        @if($showAnswers)
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-list-check text-blue-600"></i>
                        Your Responses
                    </h2>
                    <span class="text-sm text-gray-500">{{ number_format($earnedPoints, 1) }} / {{ $totalPossiblePoints }} points</span>
                </div>
                
                <div class="space-y-5">
                    @php $questionCounter = 0; @endphp
                    @foreach($questions as $index => $question)
                        @php
                            $questionType = $question['type'] ?? 'short_answer';
                            $isSection = ($questionType == 'title_section' || $questionType == 'section_break');
                        @endphp
                        
                        @if($isSection)
                            @php
                                $sectionTitle = $question['title'] ?? 'Section';
                                $sectionDescription = $question['description'] ?? '';
                            @endphp
                            <div class="bg-gray-100 rounded-xl p-5 border-l-4 border-indigo-500">
                                <h3 class="text-xl font-bold text-gray-800">{{ $sectionTitle }}</h3>
                                @if($sectionDescription)
                                    <p class="text-gray-600 text-sm mt-1">{{ $sectionDescription }}</p>
                                @endif
                                @if($questionType == 'section_break')
                                    <div class="mt-3 pt-3 border-t border-gray-300">
                                        <span class="text-xs text-gray-400">Section break</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            @php
                                $questionCounter++;
                                $questionText = $question['text'] ?? $question['title'] ?? $question['question'] ?? 'Question';
                                $questionPoints = isset($question['points']) ? (int)$question['points'] : 1;
                                
                                $answerKey = 'question_' . $index;
                                $answer = $answers[$answerKey] ?? null;
                                
                                // Get grid answers
                                $gridAnswers = null;
                                if ($questionType == 'multiple_choice_grid' || $questionType == 'checkbox_grid') {
                                    $gridAnswers = [];
                                    $rows = $question['rows'] ?? [];
                                    foreach ($rows as $rowIndex => $row) {
                                        $rowKey = 'question_' . $index . '_' . $rowIndex;
                                        if (isset($answers[$rowKey])) {
                                            $gridAnswers[$rowKey] = $answers[$rowKey];
                                        }
                                    }
                                    if (empty($gridAnswers) && is_array($answer)) {
                                        $gridAnswers = $answer;
                                    }
                                }
                                
                                $qData = $questionScores[$index] ?? [
                                    'earned' => 0,
                                    'total' => $questionPoints,
                                    'is_correct' => false,
                                    'is_partial' => false,
                                    'correct_display' => '',
                                    'user_answer' => 'Not answered',
                                    'type' => $questionType,
                                    'grid_answers' => null,
                                    'rows' => null,
                                    'columns' => null
                                ];
                                
                                $isCorrect = $qData['is_correct'];
                                $isPartial = $qData['is_partial'] ?? false;
                                $correctDisplay = $qData['correct_display'];
                                $userAnswerDisplay = $qData['user_answer'];
                                $earned = $qData['earned'];
                                $total = $qData['total'];
                                $rows = $qData['rows'] ?? null;
                                $columns = $qData['columns'] ?? null;
                                
                                // Use grid answers if available
                                if (!empty($gridAnswers) && ($questionType == 'multiple_choice_grid' || $questionType == 'checkbox_grid')) {
                                    $rows = $rows ?? $qData['rows'] ?? [];
                                    $columns = $columns ?? $qData['columns'] ?? [];
                                }
                                
                                $statusBadge = '';
                                $statusBadgeColor = '';
                                
                                // For multiple choice and dropdown, always show status
                                if ($questionType == 'multiple_choice' || $questionType == 'dropdown') {
                                    if ($isCorrect) {
                                        $statusBadge = 'Correct';
                                        $statusBadgeColor = 'bg-green-100 text-green-700';
                                    } else {
                                        $statusBadge = 'Incorrect';
                                        $statusBadgeColor = 'bg-red-100 text-red-700';
                                    }
                                } elseif ($correctDisplay) {
                                    if ($isCorrect) {
                                        $statusBadge = 'Correct';
                                        $statusBadgeColor = 'bg-green-100 text-green-700';
                                    } elseif ($isPartial) {
                                        $statusBadge = 'Partial';
                                        $statusBadgeColor = 'bg-yellow-100 text-yellow-700';
                                    } else {
                                        $statusBadge = 'Incorrect';
                                        $statusBadgeColor = 'bg-red-100 text-red-700';
                                    }
                                }
                            @endphp
                            
                            <div class="border rounded-xl p-5 hover:shadow-md transition-all">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <span class="text-blue-600 text-sm font-bold">{{ $questionCounter }}</span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-800">{{ $questionText }}</h3>
                                            @if($statusBadge)
                                            <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full {{ $statusBadgeColor }}">
                                                {{ $statusBadge }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($isQuiz)
                                        <div class="flex items-center gap-1 flex-shrink-0 ml-2">
                                            @if($questionType == 'checkboxes' || $questionType == 'multiple_choice_grid' || $questionType == 'checkbox_grid')
                                                <span class="text-xs {{ $earned > 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                                    {{ number_format($earned, 1) }}/{{ $total }} pts
                                                </span>
                                            @elseif($questionType == 'linear_scale' || $questionType == 'rating')
                                                <span class="text-xs text-green-600 font-medium">
                                                    +{{ $total }} pts
                                                </span>
                                            @elseif($questionType == 'multiple_choice' || $questionType == 'dropdown')
                                                @if($isCorrect)
                                                    <i class="fas fa-check-circle text-green-500"></i>
                                                    <span class="text-xs text-green-600 font-medium">+{{ $total }} pts</span>
                                                @else
                                                    <i class="fas fa-times-circle text-red-500"></i>
                                                    <span class="text-xs text-red-600 font-medium">0 pts</span>
                                                @endif
                                            @else
                                                @if($isCorrect)
                                                    <i class="fas fa-check-circle text-green-500"></i>
                                                    <span class="text-xs text-green-600 font-medium">+{{ $total }} pts</span>
                                                @else
                                                    <i class="fas fa-times-circle text-red-500"></i>
                                                    <span class="text-xs text-red-600 font-medium">0 pts</span>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="ml-10">
                                    <!-- User's Answer -->
                                    <div class="mb-2">
                                        <p class="text-xs text-gray-500 mb-1">Your Answer:</p>
                                        <div class="bg-gray-50 rounded-lg p-3 
                                            {{ $correctDisplay ? ($isCorrect ? 'border-l-4 border-green-500' : ($isPartial ? 'border-l-4 border-yellow-500' : 'border-l-4 border-red-500')) : '' }}">
                                            
                                            @if($questionType == 'checkboxes' && is_array($answer))
                                                @if(!empty($answer))
                                                    <ul class="list-disc list-inside space-y-0.5">
                                                        @foreach($answer as $selectedOption)
                                                            <li class="text-gray-700 text-sm">{{ $selectedOption }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p class="text-gray-400 italic">No options selected</p>
                                                @endif
                                            @elseif(($questionType == 'multiple_choice' || $questionType == 'dropdown') && $answer !== null)
                                                <p class="text-gray-700">{{ $answer }}</p>
                                            @elseif($questionType == 'linear_scale' || $questionType == 'rating')
                                                <p class="text-gray-700">{{ $userAnswerDisplay ?: 'Not answered' }}</p>
                                            @elseif(($questionType == 'multiple_choice_grid' || $questionType == 'checkbox_grid') && !empty($gridAnswers) && $rows && $columns)
                                                <div class="overflow-x-auto">
                                                    <table class="min-w-full border rounded-lg text-sm">
                                                        <thead>
                                                            <tr class="bg-gray-100">
                                                                <th class="px-3 py-2 text-left font-medium text-gray-700">Row</th>
                                                                @foreach($columns as $col)
                                                                    <th class="px-3 py-2 text-center font-medium text-gray-700">{{ $col }}</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($rows as $rowIndex => $row)
                                                                <tr class="border-t">
                                                                    <td class="px-3 py-2 font-medium text-gray-700">{{ $row }}</td>
                                                                    @foreach($columns as $col)
                                                                        @php
                                                                            $rowKey = 'question_' . $index . '_' . $rowIndex;
                                                                            $isChecked = false;
                                                                            if ($questionType == 'multiple_choice_grid') {
                                                                                $userValue = $gridAnswers[$rowKey] ?? null;
                                                                                $isChecked = ($userValue == $col);
                                                                            } else {
                                                                                $userRowAnswers = isset($gridAnswers[$rowKey]) ? (array)$gridAnswers[$rowKey] : [];
                                                                                $isChecked = in_array($col, $userRowAnswers);
                                                                            }
                                                                        @endphp
                                                                        <td class="px-3 py-2 text-center">
                                                                            @if($isChecked)
                                                                                <span class="text-green-600"><i class="fas fa-check-circle"></i></span>
                                                                            @else
                                                                                <span class="text-gray-300"><i class="far fa-circle"></i></span>
                                                                            @endif
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-gray-700">{{ $userAnswerDisplay ?: 'Not answered' }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Correct Answer - ALWAYS SHOW for multiple choice and dropdown -->
                                    @if($isQuiz && ($questionType == 'multiple_choice' || $questionType == 'dropdown'))
                                        <div class="mb-2">
                                            <p class="text-xs text-green-600 mb-1">
                                                <i class="fas fa-check-circle mr-1"></i> Correct Answer:
                                            </p>
                                            <div class="bg-green-50 rounded-lg p-3">
                                                <p class="text-green-700">{{ $correctDisplay ?: 'Not set' }}</p>
                                            </div>
                                        </div>
                                    @elseif($correctDisplay && $isQuiz)
                                        <div class="mb-2">
                                            <p class="text-xs text-green-600 mb-1">
                                                <i class="fas fa-check-circle mr-1"></i> Correct Answer:
                                            </p>
                                            <div class="bg-green-50 rounded-lg p-3">
                                                @if($questionType == 'checkboxes')
                                                    <ul class="list-disc list-inside space-y-0.5">
                                                        @foreach(explode(', ', $correctDisplay) as $correctOption)
                                                            <li class="text-green-700 text-sm">{{ $correctOption }}</li>
                                                        @endforeach
                                                    </ul>
                                                @elseif(($questionType == 'multiple_choice_grid' || $questionType == 'checkbox_grid') && $rows && $columns)
                                                    @php
                                                        $correctAnswers = $question['correctAnswers'] ?? [];
                                                    @endphp
                                                    <div class="overflow-x-auto">
                                                        <table class="min-w-full border rounded-lg text-sm">
                                                            <thead>
                                                                <tr class="bg-gray-100">
                                                                    <th class="px-3 py-2 text-left font-medium text-gray-700">Row</th>
                                                                    @if($questionType == 'multiple_choice_grid')
                                                                        <th class="px-3 py-2 text-center font-medium text-green-600">Correct Answer</th>
                                                                    @else
                                                                        <th class="px-3 py-2 text-center font-medium text-green-600">Correct Answers</th>
                                                                    @endif
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($rows as $rowIndex => $row)
                                                                    <tr class="border-t">
                                                                        <td class="px-3 py-2 font-medium text-gray-700">{{ $row }}</td>
                                                                        <td class="px-3 py-2 text-center">
                                                                            @php
                                                                                $correctRowAnswer = $correctAnswers[$rowIndex] ?? null;
                                                                            @endphp
                                                                            @if($questionType == 'multiple_choice_grid')
                                                                                <span class="text-green-700 font-medium">{{ $correctRowAnswer ?? '-' }}</span>
                                                                            @else
                                                                                @if(!empty($correctRowAnswer) && is_array($correctRowAnswer))
                                                                                    <span class="text-green-700 font-medium">{{ implode(', ', $correctRowAnswer) }}</span>
                                                                                @else
                                                                                    <span class="text-gray-400">-</span>
                                                                                @endif
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <p class="text-green-700">{{ $correctDisplay }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Partial points info -->
                                    @if(($questionType == 'checkboxes' || $questionType == 'multiple_choice_grid' || $questionType == 'checkbox_grid') && $allowPartialPoints && $correctDisplay)
                                        <div class="mt-2 p-2 bg-blue-50 rounded-lg border border-blue-200">
                                            <p class="text-xs text-blue-700">
                                                <i class="fas fa-info-circle mr-1"></i> 
                                                Partial grading: {{ number_format($earned, 1) }} out of {{ $total }} points
                                                @if($isPartial)
                                                    <span class="text-blue-500"></span>
                                                @elseif($isCorrect)
                                                    <span class="text-green-500"></span>
                                                @else
                                                    <span class="text-red-500"></span>
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                    
                                    <div class="mt-2">
                                        <span class="text-xs text-gray-400">
                                            <i class="fas fa-tag mr-1"></i> 
                                            {{ ucfirst(str_replace('_', ' ', $questionType)) }}
                                            @if($isQuiz && isset($question['points']) && $question['points'] > 0)
                                            • {{ $question['points'] }} point{{ $question['points'] > 1 ? 's' : '' }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @elseif(!$showScore && $releaseGrade == 'later')
           
        @else
            <div class="p-6 text-center py-12">
                @if($releaseGrade == 'never')
                    <p class="text-sm text-gray-400 mt-1">This form is set to never release results</p>
                @endif
            </div>
        @endif
        
        
        
    </div>
</div>

<style>
.bg-gradient-to-r {
    transition: all 0.3s ease;
}

.border {
    transition: border-color 0.2s ease;
}
.border:hover {
    border-color: #93c5fd;
}

.bg-gray-100 {
    background-color: #f3f4f6;
}

.border-l-4.border-indigo-500 {
    border-left-width: 4px;
}

ul.list-disc {
    padding-left: 1.5rem;
}
ul.list-disc li {
    margin-bottom: 2px;
}

.status-badge {
    transition: all 0.3s ease;
}

.user-name-display {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
}

/* Table styles for grid questions */
table {
    border-collapse: collapse;
}
table th, table td {
    border: 1px solid #e5e7eb;
    padding: 8px 12px;
}

@media print {
    .bg-gradient-to-r {
        background: #4f46e5 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .bg-gray-50, .bg-blue-50, .bg-green-50, .bg-gray-100 {
        background: #f9fafb !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .shadow-lg {
        box-shadow: none !important;
    }
    .hover\:shadow-md:hover {
        box-shadow: none !important;
    }
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
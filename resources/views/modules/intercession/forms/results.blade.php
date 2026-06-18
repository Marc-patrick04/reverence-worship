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
                @if(isset($submission) && isset($submission->submitted_at))
                <div class="text-right">
                    <p class="text-blue-200 text-xs">Submitted</p>
                    <p class="text-white text-sm font-medium">{{ \Carbon\Carbon::parse($submission->submitted_at)->format('M d, Y h:i A') }}</p>
                </div>
                @endif
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
            
            // Calculate total possible points - SKIP SECTIONS
            $totalPossiblePoints = 0;
            $earnedPoints = 0;
            $totalQuestionsCount = 0;
            $questionIndex = 0;
            $questionScores = [];
            
            foreach ($questions as $index => $question) {
                // Skip if this is a section or title section
                $questionType = $question['type'] ?? 'short_answer';
                if ($questionType == 'title_section' || $questionType == 'section_break') {
                    continue;
                }
                
                $totalQuestionsCount++;
                $points = isset($question['points']) ? (int)$question['points'] : 1;
                $totalPossiblePoints += $points;
                
                // Get answer using the question index
                $answerKey = 'question_' . $index;
                $answer = $answers[$answerKey] ?? null;
                
                $questionEarnedPoints = 0;
                $isCorrect = false;
                $correctDisplay = '';
                $userAnswerDisplay = '';
                $isPartiallyCorrect = false;
                
                // Format user answer for display
                if (is_array($answer)) {
                    $userAnswerDisplay = implode(', ', $answer);
                } else {
                    $userAnswerDisplay = $answer ?: 'Not answered';
                }
                
                // Handle different question types
                if ($questionType == 'multiple_choice' || $questionType == 'dropdown') {
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
                            
                            // Count only the correct answers the user selected
                            foreach ($userAnswers as $userAnswer) {
                                if (in_array($userAnswer, $correctAnswers)) {
                                    $correctSelected++;
                                }
                            }
                            
                            // Calculate points based on settings
                            if ($allowPartialPoints) {
                                // Partial grading: (correct_selected / total_correct) * points
                                if ($correctSelected > 0) {
                                    $questionEarnedPoints = ($correctSelected / $totalCorrect) * $points;
                                } else {
                                    $questionEarnedPoints = 0;
                                }
                                $questionEarnedPoints = round($questionEarnedPoints, 2);
                                
                                // Check if partially correct
                                if ($correctSelected > 0 && $correctSelected < $totalCorrect) {
                                    $isPartiallyCorrect = true;
                                }
                            } else {
                                // Full points only if all correct
                                if ($correctSelected == $totalCorrect) {
                                    $questionEarnedPoints = $points;
                                    $isCorrect = true;
                                } else {
                                    $questionEarnedPoints = 0;
                                }
                            }
                            
                            // Check if fully correct
                            if ($correctSelected == $totalCorrect) {
                                $isCorrect = true;
                            }
                        }
                    }
                } elseif ($questionType == 'short_answer' || $questionType == 'paragraph') {
                    if (isset($question['correctAnswer']) && $question['correctAnswer'] !== '') {
                        $correctDisplay = $question['correctAnswer'];
                        // Case-insensitive comparison for text answers
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
                } elseif ($questionType == 'linear_scale' || $questionType == 'rating') {
                    // For scale questions, show if answered but no correct/incorrect
                    if ($answer !== null && $answer !== '') {
                        $questionEarnedPoints = $points; // Give full points for answering
                        $isCorrect = true;
                    }
                }
                
                $earnedPoints += $questionEarnedPoints;
                
                // Store question data for display
                $questionScores[$index] = [
                    'earned' => $questionEarnedPoints,
                    'total' => $points,
                    'is_correct' => $isCorrect,
                    'is_partial' => $isPartiallyCorrect ?? false,
                    'correct_display' => $correctDisplay,
                    'user_answer' => $userAnswerDisplay,
                    'type' => $questionType
                ];
            }
            
            // Round total earned points to 2 decimals
            $earnedPoints = round($earnedPoints, 2);
            
            // Calculate score percentage
            $scorePercentage = $totalPossiblePoints > 0 ? ($earnedPoints / $totalPossiblePoints) * 100 : 0;
            
           
        @endphp
        
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-5 border-b">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-center md:text-left">
                    <p class="text-gray-600 text-sm">Your Score</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-bold text-blue-600">{{ number_format($scorePercentage, 1) }}%</span>
                        <span class="text-gray-400 text-sm">/ 100%</span>
                    </div>
                </div>
                
                <div class="w-px h-12 bg-gray-300 hidden md:block"></div>
                
                <div class="text-center">
                    <p class="text-gray-600 text-sm">Points Earned</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($earnedPoints, 1) }}</p>
                    <p class="text-xs text-gray-400">out of {{ $totalPossiblePoints }} total points</p>
                </div>
                
                <div class="w-px h-12 bg-gray-300 hidden md:block"></div>
                
                <div class="text-center">
                    <p class="text-gray-600 text-sm">Questions</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $totalQuestionsCount }}</p>
                    <p class="text-xs text-gray-400">total questions</p>
                </div>
                
                <div class="w-px h-12 bg-gray-300 hidden md:block"></div>
                
                
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-4">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>Progress</span>
                    <span>{{ number_format($scorePercentage, 1) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $scorePercentage }}%"></div>
                </div>
            </div>
        </div>
        
        <!-- Questions & Answers -->
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
                            
                            // Get stored question data
                            $qData = $questionScores[$index] ?? [
                                'earned' => 0,
                                'total' => $questionPoints,
                                'is_correct' => false,
                                'is_partial' => false,
                                'correct_display' => '',
                                'user_answer' => 'Not answered',
                                'type' => $questionType
                            ];
                            
                            $isCorrect = $qData['is_correct'];
                            $isPartial = $qData['is_partial'] ?? false;
                            $correctDisplay = $qData['correct_display'];
                            $userAnswerDisplay = $qData['user_answer'];
                            $earned = $qData['earned'];
                            $total = $qData['total'];
                            
                            // Determine status badge
                            $statusBadge = '';
                            $statusBadgeColor = '';
                            if ($correctDisplay) {
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
                                
                                @if($isQuiz && $correctDisplay)
                                    <div class="flex items-center gap-1 flex-shrink-0 ml-2">
                                        @if($questionType == 'checkboxes')
                                            <span class="text-xs {{ $earned > 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                                {{ number_format($earned, 1) }}/{{ $total }} pts
                                            </span>
                                        @elseif($questionType == 'linear_scale' || $questionType == 'rating')
                                            <span class="text-xs text-green-600 font-medium">
                                                +{{ $total }} pts
                                            </span>
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
                                        @elseif($questionType == 'multiple_choice' || $questionType == 'dropdown')
                                            <p class="text-gray-700">{{ $userAnswerDisplay ?: 'Not answered' }}</p>
                                        @elseif($questionType == 'linear_scale' || $questionType == 'rating')
                                            <p class="text-gray-700">{{ $userAnswerDisplay ?: 'Not answered' }}</p>
                                        @else
                                            <p class="text-gray-700">{{ $userAnswerDisplay ?: 'Not answered' }}</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Correct Answer (if applicable) -->
                                @if($correctDisplay && $isQuiz)
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
                                            @else
                                                <p class="text-green-700">{{ $correctDisplay }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Show partial points for checkboxes -->
                                    @if($questionType == 'checkboxes' && $allowPartialPoints)
                                        <div class="mt-2 p-2 bg-blue-50 rounded-lg border border-blue-200">
                                            <p class="text-xs text-blue-700">
                                                <i class="fas fa-info-circle mr-1"></i> 
                                                Partial grading: {{ number_format($earned, 1) }} out of {{ $total }} points earned
                                                @if($isPartial)
                                                    <span class="text-blue-500">(Partial credit)</span>
                                                @elseif($isCorrect)
                                                    <span class="text-green-500">(Full credit)</span>
                                                @else
                                                    <span class="text-red-500">(No credit)</span>
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                @endif
                                
                                <!-- Show question type badge -->
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
        
        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-5 border-t">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-simple text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Summary</p>
                        <p class="text-gray-800 font-medium">{{ $totalQuestionsCount }} Questions • {{ $totalPossiblePoints }} Total Points</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-star text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Final Score</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($scorePercentage, 1) }}%</p>
                        <p class="text-xs text-gray-400">{{ number_format($earnedPoints, 1) }} / {{ $totalPossiblePoints }} points</p>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    
                    <a href="{{ route('intercession.index') }}#forms-tab" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Back to Forms
                    </a>
                </div>
            </div>
        </div>
        
    </div>
</div>

<style>
/* Smooth transitions */
.bg-gradient-to-r {
    transition: all 0.3s ease;
}

/* Hover effects for question cards */
.border {
    transition: border-color 0.2s ease;
}

.border:hover {
    border-color: #93c5fd;
}

/* Section styling */
.bg-gray-100 {
    background-color: #f3f4f6;
}

.border-l-4.border-indigo-500 {
    border-left-width: 4px;
}

/* Checkbox list styling */
ul.list-disc {
    padding-left: 1.5rem;
}
ul.list-disc li {
    margin-bottom: 2px;
}

/* Status badge animation */
.status-badge {
    transition: all 0.3s ease;
}

/* Print styles */
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
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
            <h1 class="text-2xl font-bold text-white">{{ $form->title }}</h1>
            <p class="text-blue-100 text-sm mt-1">Form Results</p>
        </div>
        
        <!-- Score Summary -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-5 border-b">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-center md:text-left">
                    <p class="text-gray-600 text-sm">Your Score</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-bold text-blue-600">{{ number_format($submission->score, 1) }}%</span>
                        <span class="text-gray-400 text-sm">/ 100%</span>
                    </div>
                </div>
                
                <div class="w-px h-12 bg-gray-300 hidden md:block"></div>
                
                <div class="text-center">
                    <p class="text-gray-600 text-sm">Submitted</p>
                    <p class="text-gray-800 font-medium">{{ \Carbon\Carbon::parse($submission->submitted_at)->format('l, F j, Y') }}</p>
                    <p class="text-gray-400 text-xs">{{ \Carbon\Carbon::parse($submission->submitted_at)->format('g:i A') }}</p>
                </div>
                
                <div class="w-px h-12 bg-gray-300 hidden md:block"></div>
                
                <div class="text-center">
                    <p class="text-gray-600 text-sm">Status</p>
                    @php
                        $score = $submission->score;
                        $status = $score >= 80 ? 'Excellent' : ($score >= 60 ? 'Good' : ($score >= 40 ? 'Average' : 'Needs Improvement'));
                        $statusColor = $score >= 80 ? 'text-green-600' : ($score >= 60 ? 'text-blue-600' : ($score >= 40 ? 'text-yellow-600' : 'text-red-600'));
                    @endphp
                    <p class="text-lg font-semibold {{ $statusColor }}">{{ $status }}</p>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ $submission->score }}%"></div>
                </div>
            </div>
        </div>
        
        <!-- Questions & Answers -->
        <div class="p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-list-check text-blue-600"></i>
                Your Responses
            </h2>
            
            <div class="space-y-5">
                @foreach($questions as $index => $question)
                @php
                    // Safely get question text from different possible keys
                    $questionText = $question['text'] ?? $question['title'] ?? $question['question'] ?? 'Question';
                    $questionType = $question['type'] ?? 'text';
                    $questionPoints = $question['points'] ?? 1;
                    
                    // Get the user's answer safely
                    $answerKey = 'question_' . $index;
                    $answer = $answers[$answerKey] ?? 'Not answered';
                    
                    // Format the answer display based on type
                    if (is_array($answer)) {
                        $answerDisplay = implode(', ', $answer);
                    } else {
                        $answerDisplay = $answer ?: 'Not answered';
                    }
                    
                    // Determine if correct (for quiz questions)
                    $isCorrect = false;
                    if (isset($question['correctAnswer']) && $question['correctAnswer']) {
                        $isCorrect = ($answer == $question['correctAnswer']);
                    }
                @endphp
                
                <div class="border rounded-xl p-5 hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <span class="text-blue-600 text-sm font-bold">{{ $index + 1 }}</span>
                            </div>
                            <h3 class="font-semibold text-gray-800">{{ $questionText }}</h3>
                        </div>
                        @if(isset($question['correctAnswer']) && $question['correctAnswer'])
                            <div class="flex items-center gap-1">
                                @if($isCorrect)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    <span class="text-xs text-green-600 font-medium">+{{ $questionPoints }} pts</span>
                                @else
                                    <i class="fas fa-times-circle text-red-500"></i>
                                    <span class="text-xs text-red-600 font-medium">0 pts</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <div class="ml-10">
                        <!-- User's Answer -->
                        <div class="mb-2">
                            <p class="text-xs text-gray-500 mb-1">Your Answer:</p>
                            <div class="bg-gray-50 rounded-lg p-3 {{ $isCorrect ? 'border-l-4 border-green-500' : '' }}">
                                <p class="text-gray-700">{{ $answerDisplay }}</p>
                            </div>
                        </div>
                        
                        <!-- Correct Answer (if applicable) -->
                        @if(isset($question['correctAnswer']) && $question['correctAnswer'])
                            <div class="mb-2">
                                <p class="text-xs text-green-600 mb-1">
                                    <i class="fas fa-check-circle mr-1"></i> Correct Answer:
                                </p>
                                <div class="bg-green-50 rounded-lg p-3">
                                    <p class="text-green-700">{{ $question['correctAnswer'] }}</p>
                                </div>
                            </div>
                        @endif
                        
                        @if(isset($question['correctAnswers']) && is_array($question['correctAnswers']))
                            <div class="mb-2">
                                <p class="text-xs text-green-600 mb-1">
                                    <i class="fas fa-check-circle mr-1"></i> Correct Answers:
                                </p>
                                <div class="bg-green-50 rounded-lg p-3">
                                    <p class="text-green-700">{{ implode(', ', $question['correctAnswers']) }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Quiz Summary (if applicable) -->
        @if(isset($form->settings['is_quiz']) && $form->settings['is_quiz'])
        <div class="bg-gray-50 px-6 py-5 border-t">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-simple text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Quiz Summary</p>
                        <p class="text-gray-800 font-medium">{{ count($questions) }} Total Questions</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-star text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Final Score</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($submission->score, 1) }}%</p>
                    </div>
                </div>
                
                <a href="{{ route('intercession.index') }}#forms-tab" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back to Forms
                </a>
            </div>
        </div>
        @endif
        
    </div>
</div>

<style>
/* Smooth transitions */
.bg-gradient-to-r {
    transition: all 0.3s ease;
}
</style>
@endsection
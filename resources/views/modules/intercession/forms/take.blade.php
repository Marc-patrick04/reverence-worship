@extends('layouts.app')

@section('title', $form->title)

@section('content')
@php
    $hasSubmitted = DB::table('form_submissions')
        ->where('form_id', $form->id)
        ->where('user_id', auth()->id())
        ->exists();
    
    // Get settings with defaults
    $settings = json_decode($form->settings, true) ?? [];
    $showProgressBar = $settings['show_progress_bar'] ?? false;
    $shuffleQuestions = $settings['shuffle_questions'] ?? false;
    $limitOneResponse = $settings['limit_one_response'] ?? true;
    $isQuiz = $settings['is_quiz'] ?? false;
    $confirmationMessage = $settings['confirmation_message'] ?? 'Your response has been recorded.';
    $showQuestionNumbers = $settings['show_question_numbers'] ?? true;
    $onePageAtATime = $settings['one_page_at_a_time'] ?? false;
    $showTimer = $settings['show_timer'] ?? false;
    $timeLimit = $settings['time_limit'] ?? 30;
    $requireLogin = $settings['require_login'] ?? true;
    $allowEditing = $settings['allow_editing'] ?? false;
    $releaseGrade = $settings['release_grade'] ?? 'immediately';
    $allowViewResponse = $settings['allow_view_response'] ?? true;
    
    // Shuffle questions if enabled
    // Shuffle questions if enabled - BUT preserve original indices
$displayQuestions = $questions;
if ($shuffleQuestions) {
    // Get the keys (original indices) and shuffle them
    $keys = array_keys($questions);
    shuffle($keys);
    // Rebuild the array with shuffled order but preserve original keys
    $shuffled = [];
    foreach ($keys as $key) {
        $shuffled[$key] = $questions[$key];
    }
    $displayQuestions = $shuffled;
}
    
    // Check if user is logged in for require_login
    $isLoggedIn = auth()->check();
@endphp

@if($hasSubmitted && $limitOneResponse)
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
        <i class="fas fa-info-circle text-yellow-500 text-4xl mb-3"></i>
        <h2 class="text-xl font-bold text-yellow-700 mb-2">Already Submitted</h2>
        <p class="text-gray-600 mb-4">You have already submitted this form. Only one response is allowed per user.</p>
        <a href="{{ route('intercession.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-block">
            Back to Dashboard
        </a>
    </div>
</div>
@elseif($requireLogin && !$isLoggedIn)
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
        <i class="fas fa-lock text-yellow-500 text-4xl mb-3"></i>
        <h2 class="text-xl font-bold text-yellow-700 mb-2">Login Required</h2>
        <p class="text-gray-600 mb-4">You must be logged in to submit this form.</p>
        <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-block">
            <i class="fas fa-sign-in-alt mr-2"></i> Login
        </a>
    </div>
</div>
@else
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        
        {{-- Timer Display --}}
        @if($showTimer)
        <div class="bg-red-50 border-b border-red-200 px-8 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2 text-red-700">
                <i class="fas fa-clock"></i>
                <span class="text-sm font-medium">Time Remaining:</span>
            </div>
            <div id="timerDisplay" class="text-red-700 font-bold text-lg">
                <span id="timerMinutes">{{ $timeLimit }}</span>:<span id="timerSeconds">00</span>
            </div>
            <div class="text-xs text-red-500">
                <i class="fas fa-exclamation-circle"></i> Form will auto-submit when time expires
            </div>
        </div>
        @endif
        
        {{-- Form Header with Gradient --}}
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-8 py-6">
            <div class="flex justify-between items-center text-white">
                <a href="{{ route('intercession.index') }}" class="text-white/80 hover:text-white transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <div class="flex items-center gap-2 text-sm">
                    <i class="fas fa-file-alt"></i>
                    <span>Form</span>
                </div>
            </div>
            <div class="mt-4">
                <h1 class="text-3xl font-bold text-white mb-2">{{ $form->title }}</h1>
                <p class="text-indigo-100">{{ $form->description }}</p>
            </div>
        </div>
        
        {{-- Progress Bar (only if enabled) --}}
        @if($showProgressBar)
        <div class="px-8 pt-6">
            <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>Your Progress</span>
                <span id="progressPercent" class="font-semibold text-indigo-600">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="progressBar" class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>
        @endif
        
        {{-- Auto-save indicator --}}
        <div id="autoSaveIndicator" class="hidden fixed bottom-4 right-4 bg-green-500 text-white px-3 py-2 rounded-lg shadow-lg text-sm z-50">
            <i class="fas fa-check-circle mr-1"></i> Draft saved
        </div>
        
        {{-- Form Body --}}
        <form method="POST" action="{{ route('forms.submit', $form->id) }}" id="formSubmission">
            @csrf
            
            <div class="p-8 space-y-8" id="formContainer">
                @php 
                    $totalQuestions = count($displayQuestions);
                    $questionCounter = 0;
                    $pageCounter = 1;
                    $questionsPerPage = $onePageAtATime ? 5 : PHP_INT_MAX;
                    $questionIndex = 0;
                @endphp
                
                {{-- Start first page --}}
                <div class="form-page" data-page="1" style="{{ $onePageAtATime ? '' : 'display: block;' }}">
                
                @foreach($displayQuestions as $index => $question)
                @php
                    $questionText = $question['text'] ?? $question['title'] ?? $question['question'] ?? 'Question';
                    $isRequired = $question['required'] ?? false;
                    $questionType = $question['type'] ?? 'short_answer';
                    $options = $question['options'] ?? [];
                    $min = $question['min'] ?? 1;
                    $max = $question['max'] ?? 5;
                    $minLabel = $question['minLabel'] ?? '';
                    $maxLabel = $question['maxLabel'] ?? '';
                    $rows = $question['rows'] ?? [];
                    $columns = $question['columns'] ?? [];
                    $points = $question['points'] ?? 1;
                    $correctAnswers = $question['correctAnswers'] ?? [];
                    $correctAnswer = $question['correctAnswer'] ?? '';
                    
                    $isSection = ($questionType === 'section_break' || $questionType === 'title_section');
                    $originalIndex = $index;
                    
                    if ($onePageAtATime && $questionIndex > 0 && $questionIndex % $questionsPerPage == 0 && !$isSection) {
                        echo '</div><div class="form-page" data-page="' . ($pageCounter + 1) . '" style="display: none;">';
                        $pageCounter++;
                    }
                    $questionIndex++;
                @endphp
                
                @if($isSection)
                    <div class="section-card bg-gray-50 rounded-xl p-6 border-l-4 border-indigo-500">
                        <h2 class="text-2xl font-bold text-gray-800">{{ $questionText }}</h2>
                        @if(!empty($question['description']))
                            <p class="text-gray-600 mt-2">{{ $question['description'] }}</p>
                        @endif
                        @if($questionType === 'section_break')
                            <div class="mt-4 border-t border-gray-300 pt-4">
                                <span class="text-xs text-gray-400">Section break</span>
                            </div>
                        @endif
                    </div>
                @else
                    @php $questionCounter++; @endphp
                    <div class="question-card bg-gray-50 rounded-xl p-6 transition-all duration-300 hover:shadow-md" data-question="{{ $originalIndex }}">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                @if($showQuestionNumbers)
                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-sm">
                                    {{ $questionCounter }}
                                </div>
                                @endif
                                <label class="text-lg font-semibold text-gray-800">
                                    {{ $questionText }}
                                    @if($isRequired)
                                        <span class="text-red-500 text-sm ml-1">*</span>
                                    @endif
                                </label>
                            </div>
                            @if($isQuiz && isset($points) && $points > 0)
                            <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">
                                <i class="fas fa-star mr-1"></i> {{ $points }} pts
                            </span>
                            @endif
                        </div>
                        
                        <div class="{{ $showQuestionNumbers ? 'ml-11' : '' }}">
                            @if($questionType == 'short_answer')
                                <input type="text" 
                                       name="question_{{ $originalIndex }}" 
                                       class="question-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                       placeholder="Type your answer here..."
                                       {{ $isRequired ? 'required' : '' }}
                                       data-question-index="{{ $originalIndex }}">
                                
                            @elseif($questionType == 'paragraph')
                                <textarea name="question_{{ $originalIndex }}" 
                                          rows="4" 
                                          class="question-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                          placeholder="Write your detailed answer here..."
                                          {{ $isRequired ? 'required' : '' }}
                                          data-question-index="{{ $originalIndex }}"></textarea>
                                
                            @elseif($questionType == 'multiple_choice')
                                <div class="space-y-3">
                                    @foreach($options as $option)
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-indigo-50 transition group">
                                        <input type="radio" 
                                               name="question_{{ $originalIndex }}" 
                                               value="{{ $option }}" 
                                               class="w-4 h-4 text-indigo-600 focus:ring-indigo-500"
                                               {{ $isRequired ? 'required' : '' }}
                                               data-question-index="{{ $originalIndex }}">
                                        <span class="ml-3 text-gray-700 group-hover:text-indigo-700">{{ $option }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                
                            @elseif($questionType == 'checkboxes')
                                <div class="space-y-3">
                                    <p class="text-xs text-gray-500 mb-2">Select all that apply</p>
                                    @foreach($options as $option)
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-indigo-50 transition group">
                                        <input type="checkbox" 
                                               name="question_{{ $originalIndex }}[]" 
                                               value="{{ $option }}" 
                                               class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500"
                                               data-question-index="{{ $originalIndex }}">
                                        <span class="ml-3 text-gray-700 group-hover:text-indigo-700">{{ $option }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                
                            @elseif($questionType == 'dropdown')
                                <select name="question_{{ $originalIndex }}" 
                                        class="question-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                        {{ $isRequired ? 'required' : '' }}
                                        data-question-index="{{ $originalIndex }}">
                                    <option value="">Select an option</option>
                                    @foreach($options as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                                
                            @elseif($questionType == 'linear_scale')
                                <div class="flex flex-wrap items-center justify-between gap-2 mt-2">
                                    <span class="text-sm text-gray-500">{{ $minLabel ?: $min }}</span>
                                    <div class="flex flex-wrap gap-3">
                                        @for($i = $min; $i <= $max; $i++)
                                        <label class="flex flex-col items-center cursor-pointer">
                                            <input type="radio" 
                                                   name="question_{{ $originalIndex }}" 
                                                   value="{{ $i }}" 
                                                   class="w-5 h-5 text-indigo-600"
                                                   {{ $isRequired ? 'required' : '' }}
                                                   data-question-index="{{ $originalIndex }}">
                                            <span class="text-sm mt-1 font-medium">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $maxLabel ?: $max }}</span>
                                </div>
                                
                            @elseif($questionType == 'rating')
                                <div class="flex flex-wrap gap-4">
                                    @for($i = 1; $i <= $max; $i++)
                                    <label class="flex flex-col items-center cursor-pointer hover:scale-110 transition">
                                        <input type="radio" 
                                               name="question_{{ $originalIndex }}" 
                                               value="{{ $i }}" 
                                               class="hidden star-radio"
                                               data-question-index="{{ $originalIndex }}">
                                        <i class="far fa-star text-3xl text-yellow-400 hover:text-yellow-500 transition star-icon" data-value="{{ $i }}"></i>
                                        <span class="text-xs text-gray-500 mt-1">{{ $i }}</span>
                                    </label>
                                    @endfor
                                </div>
                                
                            @elseif($questionType == 'date')
                                <input type="date" 
                                       name="question_{{ $originalIndex }}" 
                                       class="question-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                       {{ $isRequired ? 'required' : '' }}
                                       data-question-index="{{ $originalIndex }}">
                                
                            @elseif($questionType == 'time')
                                <input type="time" 
                                       name="question_{{ $originalIndex }}" 
                                       class="question-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                       {{ $isRequired ? 'required' : '' }}
                                       data-question-index="{{ $originalIndex }}">
                                
                            @elseif($questionType == 'multiple_choice_grid')
                                <div class="overflow-x-auto">
                                    <table class="min-w-full border rounded-lg">
                                        <thead>
                                            <tr class="bg-gray-100">
                                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700"></th>
                                                @foreach($columns as $col)
                                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">{{ $col }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rows as $rowIndex => $row)
                                            <tr class="border-t">
                                                <td class="px-4 py-2 text-sm font-medium text-gray-700">{{ $row }}</td>
                                                @foreach($columns as $colIndex => $col)
                                                <td class="px-4 py-2 text-center">
                                                    <input type="radio" 
                                                           name="question_{{ $originalIndex }}_{{ $rowIndex }}" 
                                                           value="{{ $col }}"
                                                           class="w-4 h-4 text-indigo-600"
                                                           {{ $isRequired ? 'required' : '' }}
                                                           data-question-index="{{ $originalIndex }}">
                                                </td>
                                                @endforeach
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                            @elseif($questionType == 'checkbox_grid')
                                <div class="overflow-x-auto">
                                    <table class="min-w-full border rounded-lg">
                                        <thead>
                                            <tr class="bg-gray-100">
                                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700"></th>
                                                @foreach($columns as $col)
                                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">{{ $col }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rows as $rowIndex => $row)
                                            <tr class="border-t">
                                                <td class="px-4 py-2 text-sm font-medium text-gray-700">{{ $row }}</td>
                                                @foreach($columns as $colIndex => $col)
                                                <td class="px-4 py-2 text-center">
                                                    <input type="checkbox" 
                                                           name="question_{{ $originalIndex }}_{{ $rowIndex }}[]" 
                                                           value="{{ $col }}"
                                                           class="w-4 h-4 text-indigo-600 rounded">
                                                </td>
                                                @endforeach
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                @endforeach
                
                </div>
                
                @if($onePageAtATime && $pageCounter > 1)
                <div class="flex justify-between items-center mt-6 pt-4 border-t">
                    <button type="button" id="prevPageBtn" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition disabled:opacity-50" disabled>
                        <i class="fas fa-arrow-left mr-1"></i> Previous
                    </button>
                    <span id="pageIndicator" class="text-sm text-gray-500">Page 1 of {{ $pageCounter }}</span>
                    <button type="button" id="nextPageBtn" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        Next <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                </div>
                @endif
            </div>
            
            <div class="bg-gray-50 px-8 py-6 border-t">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-lock mr-1"></i> Your responses are secure
                        @if($allowEditing)
                        <span class="ml-2 text-xs text-blue-600">(Editing allowed after submission)</span>
                        @endif
                    </div>
                    <button type="submit" id="submitBtn" class="bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white px-8 py-3 rounded-xl font-medium transition transform hover:scale-105 flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i> Submit Form
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

<script>
const totalQuestions = {{ $totalQuestions }};
const showProgressBar = {{ $showProgressBar ? 'true' : 'false' }};
const showTimer = {{ $showTimer ? 'true' : 'false' }};
const timeLimit = {{ $timeLimit }};
const onePageAtATime = {{ $onePageAtATime ? 'true' : 'false' }};
const totalPages = {{ $pageCounter ?? 1 }};
const allowEditing = {{ $allowEditing ? 'true' : 'false' }};

let autoSaveTimer = null;
let isSubmitting = false;
let timerInterval = null;
let timeRemaining = timeLimit * 60;
let currentPage = 1;

// Timer
if (showTimer) {
    function startTimer() {
        timerInterval = setInterval(function() {
            timeRemaining--;
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            document.getElementById('timerMinutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('timerSeconds').textContent = String(seconds).padStart(2, '0');
            if (timeRemaining < 30) {
                document.getElementById('timerDisplay').classList.add('text-red-600', 'animate-pulse');
            }
            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                alert('Time is up! Your form will be submitted automatically.');
                document.getElementById('formSubmission').submit();
            }
        }, 1000);
    }
    document.addEventListener('DOMContentLoaded', function() { startTimer(); });
}

// Page Navigation
if (onePageAtATime) {
    const pages = document.querySelectorAll('.form-page');
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');
    const pageIndicator = document.getElementById('pageIndicator');
    
    function showPage(pageNumber) {
        pages.forEach((page, index) => {
            page.style.display = (index + 1 === pageNumber) ? 'block' : 'none';
        });
        currentPage = pageNumber;
        if (prevBtn) prevBtn.disabled = pageNumber === 1;
        if (nextBtn) nextBtn.disabled = pageNumber === totalPages;
        if (pageIndicator) pageIndicator.textContent = `Page ${pageNumber} of ${totalPages}`;
        document.getElementById('formContainer').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    if (nextBtn) nextBtn.addEventListener('click', function() { if (currentPage < totalPages) showPage(currentPage + 1); });
    if (prevBtn) prevBtn.addEventListener('click', function() { if (currentPage > 1) showPage(currentPage - 1); });
    showPage(1);
}

// Progress Tracking
function updateProgress() {
    if (!showProgressBar) return;
    
    let answeredCount = 0;
    let questionCount = 0;
    
    @foreach($displayQuestions as $index => $question)
    @php
        $questionType = $question['type'] ?? 'short_answer';
        $isSection = ($questionType === 'section_break' || $questionType === 'title_section');
        $originalIndex = $index;
    @endphp
    @if(!$isSection)
        questionCount++;
        @if($questionType == 'multiple_choice' || $questionType == 'linear_scale' || $questionType == 'rating' || $questionType == 'dropdown')
            const radioGroup{{ $originalIndex }} = document.querySelectorAll('input[name="question_{{ $originalIndex }}"]');
            let isAnswered{{ $originalIndex }} = false;
            radioGroup{{ $originalIndex }}.forEach(input => { if (input.checked) isAnswered{{ $originalIndex }} = true; });
            if (isAnswered{{ $originalIndex }}) answeredCount++;
        @elseif($questionType == 'checkboxes')
            const checkGroup{{ $originalIndex }} = document.querySelectorAll('input[name="question_{{ $originalIndex }}[]"]');
            let isChecked{{ $originalIndex }} = false;
            checkGroup{{ $originalIndex }}.forEach(input => { if (input.checked) isChecked{{ $originalIndex }} = true; });
            if (isChecked{{ $originalIndex }}) answeredCount++;
        @elseif($questionType == 'multiple_choice_grid')
            const gridRadios{{ $originalIndex }} = document.querySelectorAll('input[name^="question_{{ $originalIndex }}_"]');
            let isGridAnswered{{ $originalIndex }} = false;
            gridRadios{{ $originalIndex }}.forEach(input => { if (input.checked) isGridAnswered{{ $originalIndex }} = true; });
            if (isGridAnswered{{ $originalIndex }}) answeredCount++;
        @elseif($questionType == 'checkbox_grid')
            const gridCheckboxes{{ $originalIndex }} = document.querySelectorAll('input[name^="question_{{ $originalIndex }}_"][type="checkbox"]');
            let isGridChecked{{ $originalIndex }} = false;
            gridCheckboxes{{ $originalIndex }}.forEach(input => { if (input.checked) isGridChecked{{ $originalIndex }} = true; });
            if (isGridChecked{{ $originalIndex }}) answeredCount++;
        @else
            const input{{ $originalIndex }} = document.querySelector('input[name="question_{{ $originalIndex }}"]:not([type="checkbox"]):not([type="radio"]), textarea[name="question_{{ $originalIndex }}"], select[name="question_{{ $originalIndex }}"]');
            if (input{{ $originalIndex }}) {
                const val{{ $originalIndex }} = input{{ $originalIndex }}.value;
                if (val{{ $originalIndex }} !== null && val{{ $originalIndex }} !== undefined && val{{ $originalIndex }} !== '') {
                    if (typeof val{{ $originalIndex }} === 'string' && val{{ $originalIndex }}.trim() !== '') {
                        answeredCount++;
                    } else if (Array.isArray(val{{ $originalIndex }}) && val{{ $originalIndex }}.length > 0) {
                        answeredCount++;
                    }
                }
            }
        @endif
    @endif
    @endforeach
    
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');
    const percentage = questionCount > 0 ? Math.round((answeredCount / questionCount) * 100) : 0;
    if (progressBar) progressBar.style.width = percentage + '%';
    if (progressPercent) progressPercent.textContent = percentage + '%';
}

// Auto-Save
function autoSaveForm() {
    if (isSubmitting) return;
    const formData = {};
    @foreach($displayQuestions as $index => $question)
    @php
        $questionType = $question['type'] ?? 'short_answer';
        $isSection = ($questionType === 'section_break' || $questionType === 'title_section');
        $originalIndex = $index;
    @endphp
    @if(!$isSection)
        @if($questionType == 'multiple_choice' || $questionType == 'linear_scale' || $questionType == 'rating' || $questionType == 'dropdown')
            const radio{{ $originalIndex }} = document.querySelector('input[name="question_{{ $originalIndex }}"]:checked');
            formData['question_{{ $originalIndex }}'] = radio{{ $originalIndex }} ? radio{{ $originalIndex }}.value : null;
        @elseif($questionType == 'checkboxes')
            const checkboxes{{ $originalIndex }} = document.querySelectorAll('input[name="question_{{ $originalIndex }}[]"]:checked');
            formData['question_{{ $originalIndex }}'] = Array.from(checkboxes{{ $originalIndex }}).map(cb => cb.value);
        @elseif($questionType == 'multiple_choice_grid')
            const gridInputs{{ $originalIndex }} = document.querySelectorAll('input[name^="question_{{ $originalIndex }}_"]:checked');
            const gridData{{ $originalIndex }} = {};
            gridInputs{{ $originalIndex }}.forEach(input => { gridData{{ $originalIndex }}[input.name] = input.value; });
            formData['question_{{ $originalIndex }}'] = gridData{{ $originalIndex }};
        @else
            const input{{ $originalIndex }} = document.querySelector('input[name="question_{{ $originalIndex }}"], textarea[name="question_{{ $originalIndex }}"], select[name="question_{{ $originalIndex }}"]');
            formData['question_{{ $originalIndex }}'] = input{{ $originalIndex }} ? input{{ $originalIndex }}.value : '';
        @endif
    @endif
    @endforeach
    formData['form_id'] = {{ $form->id }};
    formData['timestamp'] = new Date().toISOString();
    localStorage.setItem(`form_auto_save_{{ $form->id }}`, JSON.stringify(formData));
    showAutoSaveIndicator();
}

function showAutoSaveIndicator() {
    const indicator = document.getElementById('autoSaveIndicator');
    if (indicator) {
        indicator.classList.remove('hidden');
        setTimeout(() => indicator.classList.add('hidden'), 2000);
    }
}

function loadAutoSavedData() {
    const saved = localStorage.getItem(`form_auto_save_{{ $form->id }}`);
    if (saved) {
        try {
            const formData = JSON.parse(saved);
            if (confirm('You have a saved draft. Do you want to continue where you left off?')) {
                @foreach($displayQuestions as $index => $question)
                @php
                    $questionType = $question['type'] ?? 'short_answer';
                    $isSection = ($questionType === 'section_break' || $questionType === 'title_section');
                    $originalIndex = $index;
                @endphp
                @if(!$isSection)
                    @if($questionType == 'multiple_choice' || $questionType == 'linear_scale' || $questionType == 'rating' || $questionType == 'dropdown')
                        if (formData['question_{{ $originalIndex }}']) {
                            const radio = document.querySelector(`input[name="question_{{ $originalIndex }}"][value="${formData['question_{{ $originalIndex }}']}"]`);
                            if (radio) radio.checked = true;
                        }
                    @elseif($questionType == 'checkboxes')
                        if (formData['question_{{ $originalIndex }}'] && Array.isArray(formData['question_{{ $originalIndex }}'])) {
                            document.querySelectorAll(`input[name="question_{{ $originalIndex }}[]"]`).forEach(cb => {
                                cb.checked = formData['question_{{ $originalIndex }}'].includes(cb.value);
                            });
                        }
                    @elseif($questionType == 'multiple_choice_grid')
                        if (formData['question_{{ $originalIndex }}'] && typeof formData['question_{{ $originalIndex }}'] === 'object') {
                            const gridData = formData['question_{{ $originalIndex }}'];
                            Object.keys(gridData).forEach(name => {
                                const input = document.querySelector(`input[name="${name}"][value="${gridData[name]}"]`);
                                if (input) input.checked = true;
                            });
                        }
                    @else
                        const input = document.querySelector(`input[name="question_{{ $originalIndex }}"], textarea[name="question_{{ $originalIndex }}"], select[name="question_{{ $originalIndex }}"]`);
                        if (input && formData['question_{{ $originalIndex }}'] !== undefined && formData['question_{{ $originalIndex }}'] !== null) {
                            input.value = formData['question_{{ $originalIndex }}'];
                        }
                    @endif
                @endif
                @endforeach
                updateProgress();
                showNotification('Draft loaded successfully!', 'success');
            }
        } catch(e) {
            console.log('Error loading saved data:', e);
        }
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg text-white z-50 transition-all duration-300 ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'}`;
    notification.innerHTML = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

// Event Listeners
document.querySelectorAll('input, textarea, select').forEach(input => {
    input.addEventListener('change', () => {
        if (autoSaveTimer) clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(autoSaveForm, 1000);
        updateProgress();
    });
    input.addEventListener('keyup', () => {
        if (autoSaveTimer) clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(autoSaveForm, 1000);
        updateProgress();
    });
});

document.addEventListener('DOMContentLoaded', function() {
    updateProgress();
    loadAutoSavedData();
});

// Star Rating
document.querySelectorAll('.star-icon').forEach(star => {
    star.addEventListener('click', function() {
        const value = this.dataset.value;
        const radio = this.parentElement.querySelector('.star-radio');
        if (radio) radio.checked = true;
        const container = this.closest('.flex');
        const allStars = container.querySelectorAll('.star-icon');
        allStars.forEach((s, idx) => {
            if (idx < value) { s.classList.remove('far'); s.classList.add('fas'); }
            else { s.classList.remove('fas'); s.classList.add('far'); }
        });
        updateProgress();
        if (autoSaveTimer) clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(autoSaveForm, 1000);
    });
});

function clearAutoSave() {
    localStorage.removeItem(`form_auto_save_{{ $form->id }}`);
}

// Form Submission
document.getElementById('formSubmission').addEventListener('submit', function(e) {
    e.preventDefault();
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    if (timerInterval) clearInterval(timerInterval);
    const formData = new FormData(this);
    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            clearAutoSave();
            const scoreMessage = data.score !== undefined ? ' Your score: ' + data.score + '%' : '';
            alert('Form submitted successfully!' + scoreMessage + '\n\nClick OK to view your results.');
            window.location.href = `/forms/${data.form_id}/results`;
        } else {
            alert('Error: ' + data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Form';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error submitting form. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Form';
    });
});
</script>

<style>
.question-card { transition: all 0.3s ease; }
.question-card:hover { transform: translateX(5px); }
.section-card { transition: all 0.3s ease; }
.question-input:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}
.star-icon { cursor: pointer; transition: transform 0.2s ease; }
.star-icon:hover { transform: scale(1.2); }
input[type="radio"]:checked + span { color: #4f46e5; font-weight: 500; }
#progressBar { transition: width 0.3s ease-in-out; }
#autoSaveIndicator { transition: all 0.3s ease; }
.form-page { animation: fadeIn 0.3s ease-in-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
.animate-pulse { animation: pulse 1s ease-in-out infinite; }
</style>
@endsection
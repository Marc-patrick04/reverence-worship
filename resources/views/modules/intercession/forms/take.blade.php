@extends('layouts.app')

@section('title', $form->title)

@section('content')
@php
    $hasSubmitted = DB::table('form_submissions')
        ->where('form_id', $form->id)
        ->where('user_id', auth()->id())
        ->exists();
@endphp

@if($hasSubmitted && isset($form->settings['limit_one_response']) && $form->settings['limit_one_response'])
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
@else
<!-- Rest of the take form content -->
@endif
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        
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
        
        {{-- Progress Bar --}}
        <div class="px-8 pt-6">
            <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>Your Progress</span>
                <span id="progressPercent" class="font-semibold text-indigo-600">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="progressBar" class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>
        
        {{-- Auto-save indicator --}}
        <div id="autoSaveIndicator" class="hidden fixed bottom-4 right-4 bg-green-500 text-white px-3 py-2 rounded-lg shadow-lg text-sm z-50">
            <i class="fas fa-check-circle mr-1"></i> Draft saved
        </div>
        
        {{-- Form Body --}}
        <form method="POST" action="{{ route('forms.submit', $form->id) }}" id="formSubmission">
            @csrf
            
            <div class="p-8 space-y-8">
                @php $totalQuestions = count($questions); @endphp
                
                @foreach($questions as $index => $question)
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
                @endphp
                
                <div class="question-card bg-gray-50 rounded-xl p-6 transition-all duration-300 hover:shadow-md" data-question="{{ $index }}">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <label class="text-lg font-semibold text-gray-800">
                                {{ $questionText }}
                                @if($isRequired)
                                    <span class="text-red-500 text-sm ml-1">*</span>
                                @endif
                            </label>
                        </div>
                        @if(isset($points) && $points > 0)
                        <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">
                            <i class="fas fa-star mr-1"></i> {{ $points }} pts
                        </span>
                        @endif
                    </div>
                    
                    {{-- Question Type Inputs --}}
                    <div class="ml-11">
                        @if($questionType == 'short_answer')
                            <input type="text" 
                                   name="question_{{ $index }}" 
                                   class="question-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                   placeholder="Type your answer here..."
                                   {{ $isRequired ? 'required' : '' }}
                                   data-question-index="{{ $index }}">
                            
                        @elseif($questionType == 'paragraph')
                            <textarea name="question_{{ $index }}" 
                                      rows="4" 
                                      class="question-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                      placeholder="Write your detailed answer here..."
                                      {{ $isRequired ? 'required' : '' }}
                                      data-question-index="{{ $index }}"></textarea>
                            
                        @elseif($questionType == 'multiple_choice')
                            <div class="space-y-3">
                                @foreach($options as $option)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-indigo-50 transition group">
                                    <input type="radio" 
                                           name="question_{{ $index }}" 
                                           value="{{ $option }}" 
                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-500"
                                           {{ $isRequired ? 'required' : '' }}
                                           data-question-index="{{ $index }}">
                                    <span class="ml-3 text-gray-700 group-hover:text-indigo-700">{{ $option }}</span>
                                    @if($correctAnswer == $option)
                                    <span class="ml-auto text-xs text-green-600">(Correct)</span>
                                    @endif
                                </label>
                                @endforeach
                            </div>
                            
                        @elseif($questionType == 'checkboxes')
                            <div class="space-y-3">
                                <p class="text-xs text-gray-500 mb-2">Select all that apply (Partial credit given)</p>
                                @foreach($options as $option)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-indigo-50 transition group">
                                    <input type="checkbox" 
                                           name="question_{{ $index }}[]" 
                                           value="{{ $option }}" 
                                           class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500"
                                           data-question-index="{{ $index }}">
                                    <span class="ml-3 text-gray-700 group-hover:text-indigo-700">{{ $option }}</span>
                                    @if(in_array($option, $correctAnswers))
                                    <span class="ml-auto text-xs text-green-600">(Correct)</span>
                                    @endif
                                </label>
                                @endforeach
                                <p class="text-xs text-gray-400 mt-2">Points are awarded based on number of correct selections</p>
                            </div>
                            
                        @elseif($questionType == 'dropdown')
                            <select name="question_{{ $index }}" 
                                    class="question-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                    {{ $isRequired ? 'required' : '' }}
                                    data-question-index="{{ $index }}">
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
                                               name="question_{{ $index }}" 
                                               value="{{ $i }}" 
                                               class="w-5 h-5 text-indigo-600"
                                               {{ $isRequired ? 'required' : '' }}
                                               data-question-index="{{ $index }}">
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
                                           name="question_{{ $index }}" 
                                           value="{{ $i }}" 
                                           class="hidden star-radio"
                                           data-question-index="{{ $index }}">
                                    <i class="far fa-star text-3xl text-yellow-400 hover:text-yellow-500 transition star-icon" data-value="{{ $i }}"></i>
                                </label>
                                @endfor
                            </div>
                            
                        @elseif($questionType == 'date')
                            <input type="date" 
                                   name="question_{{ $index }}" 
                                   class="question-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                   {{ $isRequired ? 'required' : '' }}
                                   data-question-index="{{ $index }}">
                            
                        @elseif($questionType == 'time')
                            <input type="time" 
                                   name="question_{{ $index }}" 
                                   class="question-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                   {{ $isRequired ? 'required' : '' }}
                                   data-question-index="{{ $index }}">
                            
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
                                                       name="question_{{ $index }}_{{ $rowIndex }}" 
                                                       value="{{ $col }}"
                                                       class="w-4 h-4 text-indigo-600"
                                                       {{ $isRequired ? 'required' : '' }}
                                                       data-question-index="{{ $index }}">
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
                                                       name="question_{{ $index }}_{{ $rowIndex }}[]" 
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
                @endforeach
            </div>
            
            {{-- Submit Section --}}
            <div class="bg-gray-50 px-8 py-6 border-t">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-lock mr-1"></i> Your responses are secure
                    </div>
                    <button type="submit" id="submitBtn" class="bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white px-8 py-3 rounded-xl font-medium transition transform hover:scale-105 flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i> Submit Form
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Progress tracking
const totalQuestions = {{ $totalQuestions }};
const progressBar = document.getElementById('progressBar');
const progressPercent = document.getElementById('progressPercent');

let autoSaveTimer = null;
let isSubmitting = false;

function updateProgress() {
    let answeredCount = 0;
    
    @foreach($questions as $index => $question)
    @php
        $questionType = $question['type'] ?? 'short_answer';
    @endphp
    
    @if($questionType == 'multiple_choice' || $questionType == 'linear_scale' || $questionType == 'rating' || $questionType == 'dropdown')
        const radioGroup{{ $index }} = document.querySelectorAll('input[name="question_{{ $index }}"]');
        let isAnswered{{ $index }} = false;
        radioGroup{{ $index }}.forEach(input => {
            if (input.checked) isAnswered{{ $index }} = true;
        });
        if (isAnswered{{ $index }}) answeredCount++;
        
    @elseif($questionType == 'checkboxes')
        const checkGroup{{ $index }} = document.querySelectorAll('input[name="question_{{ $index }}[]"]');
        let isChecked{{ $index }} = false;
        checkGroup{{ $index }}.forEach(input => {
            if (input.checked) isChecked{{ $index }} = true;
        });
        if (isChecked{{ $index }}) answeredCount++;
        
    @else
        const input{{ $index }} = document.querySelector('input[name="question_{{ $index }}"], textarea[name="question_{{ $index }}"], select[name="question_{{ $index }}"]');
        if (input{{ $index }} && input{{ $index }}.value && input{{ $index }}.value.trim() !== '') {
            answeredCount++;
        }
    @endif
    @endforeach
    
    const percentage = totalQuestions > 0 ? Math.round((answeredCount / totalQuestions) * 100) : 0;
    progressBar.style.width = percentage + '%';
    progressPercent.textContent = percentage + '%';
}

// ==================== AUTO-SAVE FUNCTIONALITY ====================
function autoSaveForm() {
    if (isSubmitting) return;
    
    const formData = {};
    @foreach($questions as $index => $question)
    @php $questionType = $question['type'] ?? 'short_answer'; @endphp
    
    @if($questionType == 'multiple_choice' || $questionType == 'linear_scale' || $questionType == 'rating' || $questionType == 'dropdown')
        const radio{{ $index }} = document.querySelector('input[name="question_{{ $index }}"]:checked');
        formData['question_{{ $index }}'] = radio{{ $index }} ? radio{{ $index }}.value : null;
    @elseif($questionType == 'checkboxes')
        const checkboxes{{ $index }} = document.querySelectorAll('input[name="question_{{ $index }}[]"]:checked');
        formData['question_{{ $index }}'] = Array.from(checkboxes{{ $index }}).map(cb => cb.value);
    @else
        const input{{ $index }} = document.querySelector('input[name="question_{{ $index }}"], textarea[name="question_{{ $index }}"], select[name="question_{{ $index }}"]');
        formData['question_{{ $index }}'] = input{{ $index }} ? input{{ $index }}.value : '';
    @endif
    @endforeach
    
    formData['form_id'] = {{ $form->id }};
    formData['timestamp'] = new Date().toISOString();
    
    localStorage.setItem(`form_auto_save_{{ $form->id }}`, JSON.stringify(formData));
    showAutoSaveIndicator();
}

function showAutoSaveIndicator() {
    const indicator = document.getElementById('autoSaveIndicator');
    indicator.classList.remove('hidden');
    setTimeout(() => {
        indicator.classList.add('hidden');
    }, 2000);
}

function loadAutoSavedData() {
    const saved = localStorage.getItem(`form_auto_save_{{ $form->id }}`);
    if (saved) {
        const formData = JSON.parse(saved);
        const confirmLoad = confirm('You have a saved draft. Do you want to continue where you left off?');
        
        if (confirmLoad) {
            @foreach($questions as $index => $question)
            @php $questionType = $question['type'] ?? 'short_answer'; @endphp
            
            @if($questionType == 'multiple_choice' || $questionType == 'linear_scale' || $questionType == 'rating' || $questionType == 'dropdown')
                if (formData['question_{{ $index }}']) {
                    const radio = document.querySelector(`input[name="question_{{ $index }}"][value="${formData['question_{{ $index }}']}"]`);
                    if (radio) radio.checked = true;
                }
            @elseif($questionType == 'checkboxes')
                if (formData['question_{{ $index }}'] && Array.isArray(formData['question_{{ $index }}'])) {
                    document.querySelectorAll(`input[name="question_{{ $index }}[]"]`).forEach(cb => {
                        cb.checked = formData['question_{{ $index }}'].includes(cb.value);
                    });
                }
            @else
                const input = document.querySelector(`input[name="question_{{ $index }}"], textarea[name="question_{{ $index }}"], select[name="question_{{ $index }}"]`);
                if (input && formData['question_{{ $index }}']) {
                    input.value = formData['question_{{ $index }}'];
                }
            @endif
            @endforeach
            
            updateProgress();
            showNotification('Draft loaded successfully!', 'success');
        }
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg text-white z-50 transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    notification.innerHTML = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

// Attach event listeners for auto-save
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

// Initial progress update and load saved data
document.addEventListener('DOMContentLoaded', function() {
    updateProgress();
    loadAutoSavedData();
});

// Star rating interaction
document.querySelectorAll('.star-icon').forEach(star => {
    star.addEventListener('click', function() {
        const value = this.dataset.value;
        const radio = this.parentElement.querySelector('.star-radio');
        if (radio) radio.checked = true;
        
        const container = this.closest('.flex');
        const allStars = container.querySelectorAll('.star-icon');
        allStars.forEach((s, idx) => {
            if (idx < value) {
                s.classList.remove('far');
                s.classList.add('fas');
            } else {
                s.classList.remove('fas');
                s.classList.add('far');
            }
        });
        updateProgress();
        if (autoSaveTimer) clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(autoSaveForm, 1000);
    });
});

// Clear auto-save after successful submission
function clearAutoSave() {
    localStorage.removeItem(`form_auto_save_{{ $form->id }}`);
}

// Form submission with loading state and redirect
document.getElementById('formSubmission').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
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
            alert('Form submitted successfully! Your score: ' + data.score + '%\n\nClick OK to view your results.');
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
.question-card {
    transition: all 0.3s ease;
}

.question-card:hover {
    transform: translateX(5px);
}

.question-input:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.star-icon {
    cursor: pointer;
    transition: transform 0.2s ease;
}

.star-icon:hover {
    transform: scale(1.2);
}

input[type="radio"]:checked + span {
    color: #4f46e5;
    font-weight: 500;
}

#progressBar {
    transition: width 0.3s ease-in-out;
}

#autoSaveIndicator {
    transition: all 0.3s ease;
}
</style>
@endsection
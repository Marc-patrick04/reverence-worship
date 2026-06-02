@extends('layouts.app')

@section('title', 'Form Settings')

@section('content')
<div class="max-w-4xl mx-auto py-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Form Settings</h1>
            <p class="text-gray-500 text-sm">{{ $form->title }}</p>
        </div>
        <a href="{{ route('forms.manage.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Back to Forms
        </a>
    </div>

    <!-- Settings Tabs -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6">
                <button onclick="showTab('quiz')" id="tab-quiz" class="py-3 px-1 border-b-2 font-medium text-sm transition quiz-tab active">
                    <i class="fas fa-check-circle mr-2"></i>Quiz
                </button>
                <button onclick="showTab('responses')" id="tab-responses" class="py-3 px-1 border-b-2 font-medium text-sm transition responses-tab">
                    <i class="fas fa-envelope mr-2"></i>Responses
                </button>
                <button onclick="showTab('presentation')" id="tab-presentation" class="py-3 px-1 border-b-2 font-medium text-sm transition presentation-tab">
                    <i class="fas fa-tv mr-2"></i>Presentation
                </button>
                <button onclick="showTab('defaults')" id="tab-defaults" class="py-3 px-1 border-b-2 font-medium text-sm transition defaults-tab">
                    <i class="fas fa-sliders-h mr-2"></i>Defaults
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- Quiz Tab -->
            <div id="quiz-tab" class="tab-content">
                <div class="flex justify-between items-start py-4 border-b">
                    <div>
                        <h3 class="font-medium text-gray-800">Make this a quiz</h3>
                        <p class="text-sm text-gray-500">Assign point values, set answers, and automatically provide feedback</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="isQuiz" class="sr-only peer" {{ ($form->settings['is_quiz'] ?? false) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-indigo-600"></div>
                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all peer-checked:translate-x-5"></div>
                    </label>
                </div>

                <div id="quizSettings" class="{{ ($form->settings['is_quiz'] ?? false) ? '' : 'hidden' }}">
                    <div class="flex justify-between items-start py-4 border-b">
                        <div>
                            <h3 class="font-medium text-gray-800">Release grade</h3>
                            <p class="text-sm text-gray-500">Choose when respondents see their grade</p>
                        </div>
                        <select id="releaseGrade" class="border rounded-lg px-3 py-1 text-sm">
                            <option value="immediately" {{ ($form->settings['release_grade'] ?? 'immediately') == 'immediately' ? 'selected' : '' }}>Immediately after submission</option>
                            <option value="later" {{ ($form->settings['release_grade'] ?? '') == 'later' ? 'selected' : '' }}>Later (after manual review)</option>
                        </select>
                    </div>

                    <div class="flex justify-between items-start py-4 border-b">
                        <div>
                            <h3 class="font-medium text-gray-800">Respondent can see</h3>
                            <p class="text-sm text-gray-500">What respondents can see after submitting</p>
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" id="showScore" {{ ($form->settings['show_score'] ?? true) ? 'checked' : '' }} class="rounded">
                                <span class="text-sm">Score</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" id="showCorrectAnswers" {{ ($form->settings['show_correct_answers'] ?? false) ? 'checked' : '' }} class="rounded">
                                <span class="text-sm">Correct answers</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" id="showPointValues" {{ ($form->settings['show_point_values'] ?? true) ? 'checked' : '' }} class="rounded">
                                <span class="text-sm">Point values</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Responses Tab -->
            <div id="responses-tab" class="tab-content hidden">
                <div class="flex justify-between items-start py-4 border-b">
                    <div>
                        <h3 class="font-medium text-gray-800">Collect email addresses</h3>
                        <p class="text-sm text-gray-500">Automatically collect respondent emails</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="collectEmails" class="sr-only peer" {{ ($form->settings['collect_emails'] ?? true) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-indigo-600"></div>
                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all peer-checked:translate-x-5"></div>
                    </label>
                </div>

                <div class="flex justify-between items-start py-4 border-b">
                    <div>
                        <h3 class="font-medium text-gray-800">Limit to 1 response</h3>
                        <p class="text-sm text-gray-500">Allow only one response per person</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="limitOneResponse" class="sr-only peer" {{ ($form->settings['limit_one_response'] ?? false) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-indigo-600"></div>
                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all peer-checked:translate-x-5"></div>
                    </label>
                </div>

                <div class="flex justify-between items-start py-4 border-b">
                    <div>
                        <h3 class="font-medium text-gray-800">Allow response editing</h3>
                        <p class="text-sm text-gray-500">Allow respondents to edit their responses after submission</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="allowEditing" class="sr-only peer" {{ ($form->settings['allow_editing'] ?? false) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-indigo-600"></div>
                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all peer-checked:translate-x-5"></div>
                    </label>
                </div>

                <div class="py-4">
                    <h3 class="font-medium text-gray-800 mb-2">Response destination</h3>
                    <select id="responseDestination" class="border rounded-lg px-3 py-2 w-64">
                        <option value="internal" selected>Save in this form</option>
                        <option value="spreadsheet">Link to Google Sheets</option>
                    </select>
                </div>
            </div>

            <!-- Presentation Tab -->
            <div id="presentation-tab" class="tab-content hidden">
                <div class="flex justify-between items-start py-4 border-b">
                    <div>
                        <h3 class="font-medium text-gray-800">Show progress bar</h3>
                        <p class="text-sm text-gray-500">Show respondents their progress through the form</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="showProgressBar" class="sr-only peer" {{ ($form->settings['show_progress_bar'] ?? false) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-indigo-600"></div>
                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all peer-checked:translate-x-5"></div>
                    </label>
                </div>

                <div class="flex justify-between items-start py-4 border-b">
                    <div>
                        <h3 class="font-medium text-gray-800">Shuffle question order</h3>
                        <p class="text-sm text-gray-500">Randomize the order of questions for each respondent</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="shuffleQuestions" class="sr-only peer" {{ ($form->settings['shuffle_questions'] ?? false) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-indigo-600"></div>
                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all peer-checked:translate-x-5"></div>
                    </label>
                </div>

                <div class="py-4">
                    <h3 class="font-medium text-gray-800 mb-2">Confirmation message</h3>
                    <textarea id="confirmationMessage" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Your response has been recorded.">{{ $form->settings['confirmation_message'] ?? 'Your response has been recorded.' }}</textarea>
                </div>
            </div>

            <!-- Defaults Tab -->
            <div id="defaults-tab" class="tab-content hidden">
                <div class="flex justify-between items-start py-4 border-b">
                    <div>
                        <h3 class="font-medium text-gray-800">Make questions required by default</h3>
                        <p class="text-sm text-gray-500">All new questions will be set as required</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="defaultRequired" class="sr-only peer" {{ ($form->settings['default_required'] ?? false) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-indigo-600"></div>
                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all peer-checked:translate-x-5"></div>
                    </label>
                </div>

                <div class="py-4">
                    <h3 class="font-medium text-gray-800 mb-2">Default question type</h3>
                    <select id="defaultQuestionType" class="border rounded-lg px-3 py-2 w-64">
                        <option value="multiple_choice" {{ ($form->settings['default_question_type'] ?? 'multiple_choice') == 'multiple_choice' ? 'selected' : '' }}>Multiple choice</option>
                        <option value="checkbox" {{ ($form->settings['default_question_type'] ?? '') == 'checkbox' ? 'selected' : '' }}>Checkboxes</option>
                        <option value="text" {{ ($form->settings['default_question_type'] ?? '') == 'text' ? 'selected' : '' }}>Short answer</option>
                        <option value="textarea" {{ ($form->settings['default_question_type'] ?? '') == 'textarea' ? 'selected' : '' }}>Paragraph</option>
                        <option value="dropdown" {{ ($form->settings['default_question_type'] ?? '') == 'dropdown' ? 'selected' : '' }}>Dropdown</option>
                        <option value="scale" {{ ($form->settings['default_question_type'] ?? '') == 'scale' ? 'selected' : '' }}>Linear scale</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="border-t px-6 py-4 flex justify-end">
            <button onclick="saveSettings()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i> Save Settings
            </button>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    document.getElementById(`${tabName}-tab`).classList.remove('hidden');

    document.querySelectorAll('nav button').forEach(btn => {
        btn.classList.remove('border-indigo-600', 'text-indigo-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    document.querySelector(`.${tabName}-tab`).classList.remove('border-transparent', 'text-gray-500');
    document.querySelector(`.${tabName}-tab`).classList.add('border-indigo-600', 'text-indigo-600');
}

// Toggle quiz settings visibility
document.getElementById('isQuiz').addEventListener('change', function() {
    const quizSettings = document.getElementById('quizSettings');
    if (this.checked) {
        quizSettings.classList.remove('hidden');
    } else {
        quizSettings.classList.add('hidden');
    }
});

function saveSettings() {
    const settings = {
        is_quiz: document.getElementById('isQuiz').checked,
        release_grade: document.getElementById('releaseGrade').value,
        show_score: document.getElementById('showScore').checked,
        show_correct_answers: document.getElementById('showCorrectAnswers').checked,
        show_point_values: document.getElementById('showPointValues').checked,
        collect_emails: document.getElementById('collectEmails').checked,
        limit_one_response: document.getElementById('limitOneResponse').checked,
        allow_editing: document.getElementById('allowEditing').checked,
        show_progress_bar: document.getElementById('showProgressBar').checked,
        shuffle_questions: document.getElementById('shuffleQuestions').checked,
        confirmation_message: document.getElementById('confirmationMessage').value,
        default_required: document.getElementById('defaultRequired').checked,
        default_question_type: document.getElementById('defaultQuestionType').value
    };

    fetch('{{ route("forms.manage.settings.update", $form->id) }}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ settings: settings })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Settings saved successfully!');
        } else {
            alert('Error saving settings');
        }
    });
}
</script>
@endsection
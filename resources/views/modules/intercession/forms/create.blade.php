@extends('layouts.app')

@section('title', 'Create Form')
@section('page-title', 'Create Form')

@section('content')

<div class="min-h-screen bg-gray-50 py-6">

    <!-- Top Navigation -->
    <div class="max-w-5xl mx-auto mb-4">
        <div class="flex justify-center gap-6 text-sm font-medium text-gray-600">
            <button id="questionsNav" class="nav-tab text-indigo-600 border-b-2 border-indigo-600 pb-2">
                Questions
            </button>
            <button id="responsesNav" class="nav-tab hover:text-indigo-600">
                Responses
            </button>
            <button id="settingsNav" class="nav-tab hover:text-indigo-600">
                Settings
            </button>
        </div>
    </div>

    <!-- Header with Action Buttons -->
    <div class="max-w-5xl mx-auto mb-3 flex justify-between items-center">
        <a href="/intercession" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Back to Manage Forms
        </a>
        <div class="flex gap-2">
            <button onclick="saveForm()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-sm">
                <i class="fas fa-check mr-1"></i> Done
            </button>
        </div>
    </div>

    <!-- Auto-save indicator -->
    <div class="fixed bottom-4 right-4 z-50 bg-green-500 text-white px-2 py-1 rounded-full text-xs hidden" id="autoSaveIndicator">
        <i class="fas fa-check-circle mr-1"></i> Auto-saved
    </div>

    <!-- ==================== QUESTIONS SECTION ==================== -->
    <div id="questionsSection">
        <!-- Form Header -->
        <div class="max-w-5xl mx-auto bg-white rounded-xl border border-gray-200 overflow-hidden mb-4">
            <div class="h-2 bg-indigo-600"></div>
            <div class="p-5">
                <input type="text" id="formTitle" placeholder="Untitled form"
                    class="w-full text-2xl font-normal border-none focus:ring-0 outline-none mb-2"
                    onchange="autoSave()">
                <input type="text" id="formDescription" placeholder="Form description"
                    class="w-full text-sm text-gray-500 border-none focus:ring-0 outline-none"
                    onchange="autoSave()">
            </div>
        </div>

        <!-- Questions Container (Sortable) -->
        <div id="questionsContainer" class="max-w-5xl mx-auto space-y-4 sortable-container"></div>

        <!-- Floating Toolbar -->
        <div class="fixed right-6 top-1/2 -translate-y-1/2 bg-white rounded-lg shadow-md p-3 flex flex-col gap-3 text-lg text-gray-500 z-10 border">
            <button onclick="addQuestion()" class="hover:text-indigo-600" title="Add question">
                <i class="fas fa-plus-circle"></i>
            </button>
            <button onclick="addTitleSection()" class="hover:text-indigo-600" title="Add title and description">
                <i class="fas fa-heading"></i>
            </button>
            <button onclick="addImage()" class="hover:text-indigo-600" title="Add image">
                <i class="fas fa-image"></i>
            </button>
            <button onclick="addSection()" class="hover:text-indigo-600" title="Add section">
                <i class="fas fa-layer-group"></i>
            </button>
        </div>
    </div>

    <!-- ==================== RESPONSES SECTION ==================== -->
    <div id="responsesSection" class="max-w-5xl mx-auto" style="display: none;">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-bold mb-4">Responses</h3>
            <div class="text-center py-10 text-gray-500">
                <i class="fas fa-chart-bar text-4xl mb-3 text-gray-300"></i>
                <p>No responses yet</p>
                <p class="text-xs mt-1">Share your form to receive responses</p>
            </div>
        </div>
    </div>

    <!-- ==================== SETTINGS SECTION ==================== -->
    <div id="settingsSection" class="max-w-5xl mx-auto" style="display: none;">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 px-5">
                <nav class="flex space-x-6">
                    <button onclick="showSettingsTab('quiz')" id="quizSettingsNav" class="settings-nav py-2 px-1 border-b-2 font-medium text-sm border-indigo-600 text-indigo-600">
                        Quiz
                    </button>
                    <button onclick="showSettingsTab('responses')" id="responsesSettingsNav" class="settings-nav py-2 px-1 border-b-2 font-medium text-sm">
                        Responses
                    </button>
                    <button onclick="showSettingsTab('presentation')" id="presentationSettingsNav" class="settings-nav py-2 px-1 border-b-2 font-medium text-sm">
                        Presentation
                    </button>
                    <button onclick="showSettingsTab('defaults')" id="defaultsSettingsNav" class="settings-nav py-2 px-1 border-b-2 font-medium text-sm">
                        Defaults
                    </button>
                </nav>
            </div>

            <div class="p-5 space-y-4">
                <!-- Quiz Settings -->
                <div id="quiz-settings-content" class="settings-content">
                    <div class="flex justify-between items-start py-3 border-b">
                        <div><h3 class="font-medium text-gray-800 text-sm">Make this a quiz</h3><p class="text-xs text-gray-500">Assign point values, set answers</p></div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="isQuiz" class="sr-only peer" checked onchange="autoSaveSettings()">
                            <div class="w-10 h-5 bg-indigo-600 rounded-full peer peer-checked:bg-indigo-600"></div>
                            <div class="absolute left-0.5 top-0.5 bg-white w-4 h-4 rounded-full transition-all translate-x-5"></div>
                        </label>
                    </div>
                    <div id="quizDetails" class="space-y-3">
                        <div class="py-3 border-b">
                            <h3 class="font-medium text-gray-800 text-sm mb-1">Release grade</h3>
                            <label class="flex items-center gap-2"><input type="radio" name="release_grade" value="immediately" checked class="text-indigo-600 text-sm"><span class="text-xs">Immediately after submission</span></label>
                            <label class="flex items-center gap-2"><input type="radio" name="release_grade" value="later" class="text-indigo-600 text-sm"><span class="text-xs">Later, after manual review</span></label>
                        </div>
                        <div class="py-3">
                            <h3 class="font-medium text-gray-800 text-sm mb-1">Default points</h3>
                            <input type="number" id="defaultPoints" value="1" min="1" class="w-16 px-2 py-1 border rounded-md text-sm text-center" onchange="autoSaveSettings()"><span class="text-xs ml-2">points per question</span>
                        </div>
                    </div>
                </div>

                <!-- Responses Settings -->
                <div id="responses-settings-content" class="settings-content hidden space-y-4">
                    <div class="flex justify-between items-start py-3 border-b">
                        <div><h3 class="font-medium text-gray-800 text-sm">User can view their responses</h3></div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="allowViewResponse" checked class="sr-only peer" onchange="autoSaveSettings()">
                            <div class="w-10 h-5 bg-gray-300 rounded-full peer peer-checked:bg-indigo-600"></div>
                            <div class="absolute left-0.5 top-0.5 bg-white w-4 h-4 rounded-full transition-all peer-checked:translate-x-5"></div>
                        </label>
                    </div>
                    <div class="flex justify-between items-start py-3 border-b">
                        <div><h3 class="font-medium text-gray-800 text-sm">Allow response editing</h3></div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="allowEditing" class="sr-only peer" onchange="autoSaveSettings()">
                            <div class="w-10 h-5 bg-gray-300 rounded-full peer peer-checked:bg-indigo-600"></div>
                            <div class="absolute left-0.5 top-0.5 bg-white w-4 h-4 rounded-full transition-all peer-checked:translate-x-5"></div>
                        </label>
                    </div>
                    <div class="flex justify-between items-start py-3 border-b">
                        <div><h3 class="font-medium text-gray-800 text-sm">Limit to 1 response</h3></div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="limitOneResponse" class="sr-only peer" checked onchange="autoSaveSettings()">
                            <div class="w-10 h-5 bg-indigo-600 rounded-full peer peer-checked:bg-indigo-600"></div>
                            <div class="absolute left-0.5 top-0.5 bg-white w-4 h-4 rounded-full transition-all translate-x-5"></div>
                        </label>
                    </div>
                </div>

                <!-- Presentation Settings -->
                <div id="presentation-settings-content" class="settings-content hidden space-y-3">
                    <div class="py-2 border-b"><label class="flex items-center gap-2"><input type="checkbox" id="showProgressBar" class="rounded text-indigo-600 text-sm" onchange="autoSaveSettings()"><span class="text-sm">Show progress bar</span></label></div>
                    <div class="py-2 border-b"><label class="flex items-center gap-2"><input type="checkbox" id="shuffleQuestions" class="rounded text-indigo-600 text-sm" onchange="autoSaveSettings()"><span class="text-sm">Shuffle question order</span></label></div>
                    <div class="py-2"><label class="block text-sm text-gray-700 mb-1">Confirmation message</label>
                        <textarea id="confirmationMessage" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm" onchange="autoSaveSettings()">Your response has been recorded.</textarea>
                    </div>
                </div>

                <!-- Defaults Settings -->
                <div id="defaults-settings-content" class="settings-content hidden space-y-3">
                    <div class="py-2 border-b"><label class="flex items-center gap-2"><input type="checkbox" id="defaultRequired" class="rounded text-indigo-600 text-sm" onchange="autoSaveSettings()"><span class="text-sm">Make questions required by default</span></label></div>
                    <div class="py-2"><label class="flex items-center gap-2"><input type="checkbox" id="publishByDefault" class="rounded text-indigo-600 text-sm" onchange="autoSaveSettings()"><span class="text-sm">Publish form by default</span></label></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
let questionCount = 0;
let questions = [];
let isQuizMode = true; // Changed to true by default
let autoSaveTimer = null;
let sortable = null;
let selectedQuestionId = null;
let limitOneResponse = true; // Changed to true by default

const questionTypes = [
    { value: 'short_answer', label: 'Short answer', icon: 'fa-font' },
    { value: 'paragraph', label: 'Paragraph', icon: 'fa-paragraph' },
    { value: 'multiple_choice', label: 'Multiple choice', icon: 'fa-circle' },
    { value: 'checkboxes', label: 'Checkboxes', icon: 'fa-check-square' },
    { value: 'dropdown', label: 'Dropdown', icon: 'fa-caret-down' },
    { value: 'linear_scale', label: 'Linear scale', icon: 'fa-sliders-h' },
    { value: 'rating', label: 'Rating', icon: 'fa-star' },
    { value: 'multiple_choice_grid', label: 'Multiple choice grid', icon: 'fa-table' },
    { value: 'checkbox_grid', label: 'Checkbox grid', icon: 'fa-table' },
    { value: 'date', label: 'Date', icon: 'fa-calendar' },
    { value: 'time', label: 'Time', icon: 'fa-clock' }
];

// Navigation
document.getElementById('questionsNav').addEventListener('click', () => {
    document.getElementById('questionsSection').style.display = 'block';
    document.getElementById('responsesSection').style.display = 'none';
    document.getElementById('settingsSection').style.display = 'none';
    updateNavActive('questionsNav');
});
document.getElementById('responsesNav').addEventListener('click', () => {
    document.getElementById('questionsSection').style.display = 'none';
    document.getElementById('responsesSection').style.display = 'block';
    document.getElementById('settingsSection').style.display = 'none';
    updateNavActive('responsesNav');
});
document.getElementById('settingsNav').addEventListener('click', () => {
    document.getElementById('questionsSection').style.display = 'none';
    document.getElementById('responsesSection').style.display = 'none';
    document.getElementById('settingsSection').style.display = 'block';
    updateNavActive('settingsNav');
});

function updateNavActive(activeId) {
    document.querySelectorAll('.nav-tab').forEach(tab => {
        tab.classList.remove('text-indigo-700', 'border-b-2', 'border-indigo-700');
        tab.classList.add('text-gray-500');
    });
    document.getElementById(activeId).classList.remove('text-gray-500');
    document.getElementById(activeId).classList.add('text-indigo-700', 'border-b-2', 'border-indigo-700');
}

function showSettingsTab(tabName) {
    document.querySelectorAll('.settings-content').forEach(c => c.classList.add('hidden'));
    document.getElementById(`${tabName}-settings-content`).classList.remove('hidden');
    document.querySelectorAll('.settings-nav').forEach(btn => {
        btn.classList.remove('border-indigo-600', 'text-indigo-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    document.getElementById(`${tabName}SettingsNav`).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById(`${tabName}SettingsNav`).classList.add('border-indigo-600', 'text-indigo-600');
}

function autoSaveSettings() {
    saveSettingsToLocal();
    autoSave();
}

function saveSettingsToLocal() {
    let releaseGrade = document.querySelector('input[name="release_grade"]:checked')?.value || 'immediately';
    const settings = {
        is_quiz: document.getElementById('isQuiz')?.checked || true,
        release_grade: releaseGrade,
        default_points: document.getElementById('defaultPoints')?.value || 1,
        allow_view_response: document.getElementById('allowViewResponse')?.checked || true,
        allow_editing: document.getElementById('allowEditing')?.checked || false,
        limit_one_response: document.getElementById('limitOneResponse')?.checked || true,
        show_progress_bar: document.getElementById('showProgressBar')?.checked || false,
        shuffle_questions: document.getElementById('shuffleQuestions')?.checked || false,
        confirmation_message: document.getElementById('confirmationMessage')?.value || 'Your response has been recorded.',
        default_required: document.getElementById('defaultRequired')?.checked || false,
        publish_by_default: document.getElementById('publishByDefault')?.checked || false
    };
    localStorage.setItem('form_settings', JSON.stringify(settings));
}

function loadSettings() {
    if (document.getElementById('allowViewResponse')) document.getElementById('allowViewResponse').checked = true;
    if (document.getElementById('allowEditing')) document.getElementById('allowEditing').checked = false;
    if (document.getElementById('limitOneResponse')) document.getElementById('limitOneResponse').checked = true;
    if (document.getElementById('shuffleQuestions')) document.getElementById('shuffleQuestions').checked = true;
    if (document.getElementById('showProgressBar')) document.getElementById('showProgressBar').checked = false;
    if (document.getElementById('isQuiz')) document.getElementById('isQuiz').checked = true;
    
    const saved = localStorage.getItem('form_settings');
    if (saved) {
        const s = JSON.parse(saved);
        if (document.getElementById('isQuiz')) document.getElementById('isQuiz').checked = s.is_quiz !== false;
        if (s.is_quiz !== false) document.getElementById('quizDetails')?.classList.remove('hidden');
        const radio = document.querySelector(`input[name="release_grade"][value="${s.release_grade}"]`);
        if (radio) radio.checked = true;
        if (document.getElementById('defaultPoints')) document.getElementById('defaultPoints').value = s.default_points || 1;
        if (document.getElementById('allowViewResponse')) document.getElementById('allowViewResponse').checked = s.allow_view_response !== false;
        if (document.getElementById('allowEditing')) document.getElementById('allowEditing').checked = s.allow_editing || false;
        if (document.getElementById('limitOneResponse')) document.getElementById('limitOneResponse').checked = s.limit_one_response !== false;
        if (document.getElementById('showProgressBar')) document.getElementById('showProgressBar').checked = s.show_progress_bar || false;
        if (document.getElementById('shuffleQuestions')) document.getElementById('shuffleQuestions').checked = s.shuffle_questions !== false;
        if (document.getElementById('confirmationMessage')) document.getElementById('confirmationMessage').value = s.confirmation_message || 'Your response has been recorded.';
        if (document.getElementById('defaultRequired')) document.getElementById('defaultRequired').checked = s.default_required || false;
        if (document.getElementById('publishByDefault')) document.getElementById('publishByDefault').checked = s.publish_by_default || false;
    }
}

function showAutoSaveIndicator() {
    const indicator = document.getElementById('autoSaveIndicator');
    indicator.classList.remove('hidden');
    setTimeout(() => indicator.classList.add('hidden'), 1500);
}

function autoSave() {
    if (autoSaveTimer) clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => saveForm(true), 2000);
}

function addTitleSection() {
    let insertIndex = questions.length;
    if (selectedQuestionId !== null) {
        const selectedIndex = questions.findIndex(q => q.id === selectedQuestionId);
        if (selectedIndex !== -1) {
            insertIndex = selectedIndex;
        }
    }
    const id = questionCount++;
    const newSection = { id, type: 'title_section', title: '', description: '' };
    questions.splice(insertIndex, 0, newSection);
    renderAllQuestions();
    autoSave();
    selectedQuestionId = null;
}

function addImage() {
    const url = prompt('Enter image URL:', 'https://via.placeholder.com/800x200?text=Image');
    if (url) {
        let insertIndex = questions.length;
        if (selectedQuestionId !== null) {
            const selectedIndex = questions.findIndex(q => q.id === selectedQuestionId);
            if (selectedIndex !== -1) {
                insertIndex = selectedIndex;
            }
        }
        const id = questionCount++;
        const newImage = { id, type: 'image', imageUrl: url, altText: 'Image' };
        questions.splice(insertIndex, 0, newImage);
        renderAllQuestions();
        autoSave();
        selectedQuestionId = null;
    }
}

function addSection() {
    let insertIndex = questions.length;
    if (selectedQuestionId !== null) {
        const selectedIndex = questions.findIndex(q => q.id === selectedQuestionId);
        if (selectedIndex !== -1) {
            insertIndex = selectedIndex;
        }
    }
    const id = questionCount++;
    const newSection = { id, type: 'section_break', title: 'New Section', description: '' };
    questions.splice(insertIndex, 0, newSection);
    renderAllQuestions();
    autoSave();
    selectedQuestionId = null;
}

function addQuestion() {
    let insertIndex = questions.length;
    if (selectedQuestionId !== null) {
        const selectedIndex = questions.findIndex(q => q.id === selectedQuestionId);
        if (selectedIndex !== -1) {
            insertIndex = selectedIndex;
        }
    }
    const id = questionCount++;
    const defaultPoints = document.getElementById('defaultPoints')?.value || 1;
    const newQuestion = {
        id, text: '', type: 'short_answer', required: document.getElementById('defaultRequired')?.checked || false,
        points: defaultPoints, correctAnswer: '', correctAnswers: [], options: ['Option 1'], rows: ['Row 1'], columns: ['Column 1'],
        requireOnePerRow: false, min: 1, max: 5, minLabel: '', maxLabel: ''
    };
    questions.splice(insertIndex, 0, newQuestion);
    renderAllQuestions();
    autoSave();
    selectedQuestionId = null;
}

function renderAllQuestions() {
    const container = document.getElementById('questionsContainer');
    container.innerHTML = '';
    questions.forEach(q => renderQuestion(q));
    if (sortable) sortable.destroy();
    sortable = new Sortable(container, {
        handle: '.drag-handle',
        animation: 150,
        onEnd: function() {
            const newOrder = [];
            document.querySelectorAll('.sortable-item').forEach(el => {
                const id = parseInt(el.getAttribute('data-id'));
                const question = questions.find(q => q.id === id);
                if (question) newOrder.push(question);
            });
            questions = newOrder;
            autoSave();
        }
    });
}

function selectQuestion(questionId) {
    selectedQuestionId = questionId;
    document.querySelectorAll('.sortable-item').forEach(el => {
        el.classList.remove('ring-2', 'ring-indigo-500');
    });
    const selectedEl = document.querySelector(`.sortable-item[data-id="${questionId}"]`);
    if (selectedEl) {
        selectedEl.classList.add('ring-2', 'ring-indigo-500');
    }
}

function renderQuestion(q) {
    const container = document.getElementById('questionsContainer');
    const div = document.createElement('div');
    div.setAttribute('data-id', q.id);
    div.className = 'sortable-item cursor-pointer';
    div.onclick = (e) => {
        e.stopPropagation();
        selectQuestion(q.id);
    };
    
    if (q.type === 'title_section') {
        div.className += ' bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden';
        div.innerHTML = `<div class="drag-handle cursor-move bg-gray-50 py-1 text-center border-b"><i class="fas fa-grip-horizontal text-gray-400 text-sm"></i></div>
            <div class="p-5"><div class="flex justify-end mb-2"><i class="fas fa-trash text-sm text-gray-400 cursor-pointer hover:text-red-600" onclick="event.stopPropagation(); deleteQuestion(${q.id})"></i></div>
            <input type="text" value="${escapeHtml(q.title)}" placeholder="Section title" onchange="updateAndAutoSave('titleSection', ${q.id}, 'title', this.value)" class="w-full text-xl font-medium border-0 focus:ring-0 outline-none mb-2">
            <input type="text" value="${escapeHtml(q.description)}" placeholder="Section description" onchange="updateAndAutoSave('titleSection', ${q.id}, 'description', this.value)" class="w-full text-sm text-gray-500 border-0 focus:ring-0 outline-none"></div>`;
    } else if (q.type === 'image') {
        div.className += ' bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden';
        div.innerHTML = `<div class="drag-handle cursor-move bg-gray-50 py-1 text-center border-b"><i class="fas fa-grip-horizontal text-gray-400 text-sm"></i></div>
            <div class="relative"><div class="absolute top-2 right-2 z-10"><i class="fas fa-trash text-sm text-gray-400 cursor-pointer hover:text-red-600" onclick="event.stopPropagation(); deleteQuestion(${q.id})"></i></div>
            <img src="${escapeHtml(q.imageUrl)}" alt="${escapeHtml(q.altText)}" class="w-full h-auto max-h-48 object-cover">
            <div class="p-3 text-center"><input type="text" value="${escapeHtml(q.altText)}" placeholder="Alt text" onchange="updateAndAutoSave('imageAlt', ${q.id}, null, this.value)" class="text-xs text-gray-500 border-0 focus:ring-0 outline-none text-center w-full"></div></div>`;
    } else if (q.type === 'section_break') {
        div.className += ' relative';
        div.innerHTML = `<div class="drag-handle cursor-move absolute left-1/2 -translate-x-1/2 -top-2 z-10 bg-white px-3 rounded-full shadow text-xs"><i class="fas fa-grip-horizontal text-gray-400"></i></div>
            <div class="bg-gray-50 py-5 px-6 rounded-xl border border-gray-200 text-center"><div class="flex justify-end mb-2"><i class="fas fa-trash text-sm text-gray-400 cursor-pointer hover:text-red-600" onclick="event.stopPropagation(); deleteQuestion(${q.id})"></i></div>
            <i class="fas fa-layer-group text-2xl text-gray-400 mb-2"></i>
            <input type="text" value="${escapeHtml(q.title)}" placeholder="Section title" onchange="updateAndAutoSave('sectionBreak', ${q.id}, 'title', this.value)" class="text-lg font-medium border-0 bg-transparent focus:ring-0 outline-none text-center w-full">
            <input type="text" value="${escapeHtml(q.description)}" placeholder="Section description" onchange="updateAndAutoSave('sectionBreak', ${q.id}, 'description', this.value)" class="text-sm text-gray-500 border-0 bg-transparent focus:ring-0 outline-none text-center w-full mt-1">
            <div class="border-t border-gray-300 my-3"></div><p class="text-xs text-gray-400">After section break</p></div>`;
    } else {
        div.className += ' bg-white border border-gray-200 rounded-xl shadow-sm relative overflow-hidden';
        div.id = `question-${q.id}`;
        div.innerHTML = `<div class="drag-handle cursor-move bg-gray-50 py-1 text-center border-b"><i class="fas fa-grip-horizontal text-gray-400 text-sm"></i><span class="text-xs text-gray-400 ml-1">Drag</span></div>
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500" style="top: 30px;"></div>
            <div class="p-5"><div class="grid grid-cols-12 gap-4 items-start">
                <div class="col-span-7"><input type="text" value="${escapeHtml(q.text)}" placeholder="Question" onchange="updateAndAutoSave('questionText', ${q.id}, null, this.value)" class="w-full text-xl border-0 border-b border-gray-300 focus:ring-0 focus:border-gray-500 bg-gray-50 px-3 py-2"></div>
                <div class="col-span-1 flex justify-center pt-2"><i class="fas fa-image text-gray-500 cursor-pointer hover:text-indigo-600" onclick="event.stopPropagation(); addImageToQuestion(${q.id})"></i></div>
                <div class="col-span-4"><select onchange="changeQuestionType(${q.id}, this.value)" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">${questionTypes.map(t => `<option value="${t.value}" ${q.type === t.value ? 'selected' : ''}>${t.label}</option>`).join('')}</select></div>
            </div>
            ${isQuizMode ? `<div class="mt-3 flex items-center gap-3 bg-gray-50 p-2 rounded-lg"><div class="flex items-center gap-2"><span class="text-xs text-gray-600">Points:</span><input type="number" value="${q.points || 1}" min="0" max="100" onchange="updateAndAutoSave('points', ${q.id}, null, this.value)" class="w-16 px-2 py-1 border rounded-md text-sm text-center"></div></div>` : ''}
            <div id="options-${q.id}" class="mt-5">${renderOptionsByType(q)}</div>
            <div class="border-t mt-5 pt-4 flex justify-end items-center gap-4">
                <i class="fas fa-copy text-gray-500 cursor-pointer hover:text-indigo-600" onclick="event.stopPropagation(); duplicateQuestion(${q.id})"></i>
                <i class="fas fa-trash text-gray-500 cursor-pointer hover:text-red-600" onclick="event.stopPropagation(); deleteQuestion(${q.id})"></i>
                <div class="h-6 w-px bg-gray-300"></div><span class="text-xs text-gray-600">Required</span>
                <label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" class="sr-only peer" ${q.required ? 'checked' : ''} onchange="updateAndAutoSave('required', ${q.id}, null, this.checked)"><div class="w-9 h-5 bg-gray-300 rounded-full peer peer-checked:bg-indigo-600"></div><div class="absolute left-0.5 top-0.5 bg-white w-4 h-4 rounded-full transition-all peer-checked:translate-x-4"></div></label>
                <i class="fas fa-ellipsis-v text-gray-400 cursor-pointer"></i>
            </div></div>`;
    }
    container.appendChild(div);
}

function renderOptionsByType(q) {
    switch(q.type) {
        case 'multiple_choice':
            let mcHtml = '';
            (q.options || ['Option 1']).forEach((opt, i) => {
                mcHtml += `<div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-circle text-gray-400 text-xs"></i>
                    <input type="text" value="${escapeHtml(opt)}" placeholder="Option ${i+1}" onchange="updateAndAutoSave('option', ${q.id}, ${i}, this.value)" class="flex-1 text-sm border-0 border-b border-gray-300 focus:border-indigo-500 focus:ring-0 py-1">
                    <label class="flex items-center gap-1 ml-2">
                        <input type="radio" name="correct_${q.id}" value="${escapeHtml(opt)}" ${q.correctAnswer === opt ? 'checked' : ''} onchange="updateAndAutoSave('correctAnswer', ${q.id}, null, '${escapeHtml(opt)}')" class="w-3 h-3 text-green-600">
                        <span class="text-xs text-gray-500">Correct</span>
                    </label>
                    <button onclick="event.stopPropagation(); removeOption(${q.id}, ${i})" class="text-gray-400 hover:text-red-500"><i class="fas fa-times text-xs"></i></button>
                </div>`;
            });
            mcHtml += `<div class="flex items-center gap-2 text-gray-400 mt-2"><i class="fas fa-circle text-xs"></i><span class="text-xs">Add option</span><button onclick="event.stopPropagation(); addOption(${q.id})" class="text-indigo-600 text-xs hover:underline">add</button></div>`;
            return mcHtml;
            
        case 'checkboxes':
            let cbHtml = '';
            (q.options || ['Option 1']).forEach((opt, i) => {
                cbHtml += `<div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-square text-gray-400 text-xs"></i>
                    <input type="text" value="${escapeHtml(opt)}" placeholder="Option ${i+1}" onchange="updateAndAutoSave('option', ${q.id}, ${i}, this.value)" class="flex-1 text-sm border-0 border-b border-gray-300 focus:border-indigo-500 focus:ring-0 py-1">
                    <label class="flex items-center gap-1 ml-2">
                        <input type="checkbox" value="${escapeHtml(opt)}" ${(q.correctAnswers && q.correctAnswers.includes(opt)) ? 'checked' : ''} onchange="updateAndAutoSave('correctAnswers', ${q.id}, null, '${escapeHtml(opt)}', this.checked)" class="w-3 h-3 text-green-600 rounded">
                        <span class="text-xs text-gray-500">Correct</span>
                    </label>
                    <button onclick="event.stopPropagation(); removeOption(${q.id}, ${i})" class="text-gray-400 hover:text-red-500"><i class="fas fa-times text-xs"></i></button>
                </div>`;
            });
            cbHtml += `<div class="flex items-center gap-2 text-gray-400 mt-2"><i class="fas fa-square text-xs"></i><span class="text-xs">Add option</span><button onclick="event.stopPropagation(); addOption(${q.id})" class="text-indigo-600 text-xs hover:underline">add</button></div>`;
            return cbHtml;
            
        case 'dropdown':
            let ddHtml = '';
            (q.options || ['Option 1']).forEach((opt, i) => {
                ddHtml += `<div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-bars text-gray-400 text-xs"></i>
                    <input type="text" value="${escapeHtml(opt)}" placeholder="Option ${i+1}" onchange="updateAndAutoSave('option', ${q.id}, ${i}, this.value)" class="flex-1 text-sm border-0 border-b border-gray-300 focus:border-indigo-500 focus:ring-0 py-1">
                    <label class="flex items-center gap-1 ml-2">
                        <input type="radio" name="correct_${q.id}" value="${escapeHtml(opt)}" ${q.correctAnswer === opt ? 'checked' : ''} onchange="updateAndAutoSave('correctAnswer', ${q.id}, null, '${escapeHtml(opt)}')" class="w-3 h-3 text-green-600">
                        <span class="text-xs text-gray-500">Correct</span>
                    </label>
                    <button onclick="event.stopPropagation(); removeOption(${q.id}, ${i})" class="text-gray-400 hover:text-red-500"><i class="fas fa-times text-xs"></i></button>
                </div>`;
            });
            ddHtml += `<div class="flex items-center gap-2 text-gray-400 mt-2"><i class="fas fa-bars text-xs"></i><span class="text-xs">Add option</span><button onclick="event.stopPropagation(); addOption(${q.id})" class="text-indigo-600 text-xs hover:underline">add</button></div>`;
            return ddHtml;
            
        case 'linear_scale':
            return `<div class="flex items-center gap-3"><span class="text-xs text-gray-500">Lowest</span><input type="number" value="${q.min || 1}" onchange="updateAndAutoSave('scaleMin', ${q.id}, null, this.value)" class="w-14 px-2 py-1 border rounded-md text-sm"><span class="text-gray-400">→</span><input type="number" value="${q.max || 5}" onchange="updateAndAutoSave('scaleMax', ${q.id}, null, this.value)" class="w-14 px-2 py-1 border rounded-md text-sm"><span class="text-xs text-gray-500">Highest</span></div>`;
            
        case 'rating':
            return `<div class="flex items-center gap-3"><span class="text-xs text-gray-500">Stars:</span><select onchange="updateAndAutoSave('ratingMax', ${q.id}, null, this.value)" class="border rounded px-2 py-1 text-sm">${[1,2,3,4,5,6,7,8,9,10].map(n => `<option value="${n}" ${(q.max || 5) === n ? 'selected' : ''}>${n} stars</option>`).join('')}</select></div>`;
            
        case 'multiple_choice_grid':
        case 'checkbox_grid':
            return `<div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Rows</label>${(q.rows || ['Row 1']).map((r,i)=>`<div class="flex items-center gap-1 mb-1"><span class="text-gray-500 w-5 text-xs">${i+1}.</span><input type="text" value="${escapeHtml(r)}" onchange="updateAndAutoSave('row', ${q.id}, ${i}, this.value)" class="flex-1 px-2 py-1 border rounded-lg text-xs"><button onclick="event.stopPropagation(); removeRow(${q.id}, ${i})" class="text-red-500"><i class="fas fa-times text-xs"></i></button></div>`).join('')}<button onclick="event.stopPropagation(); addRow(${q.id})" class="text-indigo-600 text-xs">+ Add row</button></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Columns</label>${(q.columns || ['Column 1']).map((c,i)=>`<div class="flex items-center gap-1 mb-1"><input type="text" value="${escapeHtml(c)}" onchange="updateAndAutoSave('column', ${q.id}, ${i}, this.value)" class="flex-1 px-2 py-1 border rounded-lg text-xs"><button onclick="event.stopPropagation(); removeColumn(${q.id}, ${i})" class="text-red-500"><i class="fas fa-times text-xs"></i></button></div>`).join('')}<button onclick="event.stopPropagation(); addColumn(${q.id})" class="text-indigo-600 text-xs">+ Add column</button></div>
            </div>`;
            
        default:
            return `<input type="text" class="w-full text-sm border-0 border-b border-gray-300" placeholder="Answer" disabled>`;
    }
}

function updateAndAutoSave(type, id, index, value, checked) {
    const q = questions.find(q => q.id === id);
    if (!q) return;
    switch(type) {
        case 'questionText': q.text = value; break;
        case 'titleSection': q[index] = value; break;
        case 'sectionBreak': q[index] = value; break;
        case 'imageAlt': q.altText = value; break;
        case 'points': q.points = parseInt(value) || 0; break;
        case 'required': q.required = value; break;
        case 'option': if (q.options) q.options[index] = value; break;
        case 'correctAnswer': q.correctAnswer = value; break;
        case 'correctAnswers': 
            if (!q.correctAnswers) q.correctAnswers = [];
            if (checked) { if (!q.correctAnswers.includes(value)) q.correctAnswers.push(value); }
            else { q.correctAnswers = q.correctAnswers.filter(v => v !== value); }
            break;
        case 'row': if (q.rows) q.rows[index] = value; break;
        case 'column': if (q.columns) q.columns[index] = value; break;
        case 'scaleMin': q.min = parseInt(value); break;
        case 'scaleMax': q.max = parseInt(value); break;
        case 'ratingMax': q.max = parseInt(value); break;
    }
    autoSave();
}

function addRow(id) { const q = questions.find(q => q.id === id); if (q) { if (!q.rows) q.rows = []; q.rows.push(`Row ${q.rows.length + 1}`); renderAllQuestions(); autoSave(); } }
function removeRow(id, i) { const q = questions.find(q => q.id === id); if (q && q.rows && q.rows.length > 1) { q.rows.splice(i, 1); renderAllQuestions(); autoSave(); } }
function addColumn(id) { const q = questions.find(q => q.id === id); if (q) { if (!q.columns) q.columns = []; q.columns.push(`Column ${q.columns.length + 1}`); renderAllQuestions(); autoSave(); } }
function removeColumn(id, i) { const q = questions.find(q => q.id === id); if (q && q.columns && q.columns.length > 1) { q.columns.splice(i, 1); renderAllQuestions(); autoSave(); } }
function addOption(id) { const q = questions.find(q => q.id === id); if (q && q.options) { q.options.push(`Option ${q.options.length + 1}`); renderAllQuestions(); autoSave(); } }
function removeOption(id, i) { const q = questions.find(q => q.id === id); if (q && q.options && q.options.length > 1) { q.options.splice(i, 1); renderAllQuestions(); autoSave(); } }
function addImageToQuestion(id) { const url = prompt('Enter image URL:'); if (url) { const q = questions.find(q => q.id === id); if (q) q.imageUrl = url; renderAllQuestions(); autoSave(); } }
function changeQuestionType(id, type) { const q = questions.find(q => q.id === id); if (q) { q.type = type; if (!q.options && (type === 'multiple_choice' || type === 'checkboxes' || type === 'dropdown')) q.options = ['Option 1']; if (type === 'multiple_choice_grid' || type === 'checkbox_grid') { if (!q.rows) q.rows = ['Row 1']; if (!q.columns) q.columns = ['Column 1']; } renderAllQuestions(); autoSave(); } }
function duplicateQuestion(id) { const o = questions.find(q => q.id === id); if (o) { const nid = questionCount++; const newQuestion = JSON.parse(JSON.stringify({...o, id: nid})); const index = questions.findIndex(q => q.id === id) + 1; questions.splice(index, 0, newQuestion); renderAllQuestions(); autoSave(); } }
function deleteQuestion(id) { const i = questions.findIndex(q => q.id === id); if (i !== -1) { questions.splice(i, 1); renderAllQuestions(); autoSave(); } }

let currentFormId = null;

function saveForm(isAutoSave = false) {
    const title = document.getElementById('formTitle').value || 'Untitled form';
    const description = document.getElementById('formDescription').value || '';
    const publishByDefault = document.getElementById('publishByDefault')?.checked || false;
    
    let settings = { 
        is_published: publishByDefault, 
        allow_retake: false, 
        show_score: true,
        allow_view_response: document.getElementById('allowViewResponse')?.checked || true,
        allow_editing: document.getElementById('allowEditing')?.checked || false,
        limit_one_response: document.getElementById('limitOneResponse')?.checked || true,
        show_progress_bar: document.getElementById('showProgressBar')?.checked || false,
        shuffle_questions: document.getElementById('shuffleQuestions')?.checked || false,
        confirmation_message: document.getElementById('confirmationMessage')?.value || 'Your response has been recorded.',
        default_required: document.getElementById('defaultRequired')?.checked || false,
        is_quiz: document.getElementById('isQuiz')?.checked || true,
        release_grade: document.querySelector('input[name="release_grade"]:checked')?.value || 'immediately',
        default_points: document.getElementById('defaultPoints')?.value || 1
    };
    
    const saved = localStorage.getItem('form_settings');
    if (saved) settings = { ...settings, ...JSON.parse(saved) };
    
    const data = { 
        title, 
        description, 
        questions: questions.map(q => ({ 
            type: q.type, text: q.text || null, title: q.title || null, description: q.description || null,
            imageUrl: q.imageUrl || null, altText: q.altText || null, options: q.options || null,
            required: q.required || false, min: q.min, max: q.max, rows: q.rows, columns: q.columns,
            points: q.points || 1, correctAnswer: q.correctAnswer || null, correctAnswers: q.correctAnswers || null
        })), 
        settings 
    };
    
    let url = '{{ route("forms.manage.store") }}';
    let method = 'POST';
    
    if (currentFormId) {
        url = `/forms/manage/${currentFormId}`;
        method = 'PUT';
    }
    
    fetch(url, { 
        method: method, 
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify(data) 
    })
    .then(res => res.json()).then(data => { 
        if (data.success) {
            if (data.form_id) {
                currentFormId = data.form_id;
            }
            if (isAutoSave) {
                showAutoSaveIndicator();
            } else {
                if (confirm('Form saved successfully! Click OK to go back to Manage Forms.')) {
                    window.location.href = '{{ route("forms.manage.index") }}';
                }
            }
        } else { 
            if (!isAutoSave) alert('Error: ' + data.message); 
        } 
    })
    .catch(err => { console.error(err); if (!isAutoSave) alert('Error saving form'); });
}
function escapeHtml(t) { if (!t) return ''; const d = document.createElement('div'); d.textContent = t; return d.innerHTML; }

document.getElementById('isQuiz')?.addEventListener('change', function() {
    isQuizMode = this.checked;
    document.getElementById('quizDetails').classList.toggle('hidden', !this.checked);
    renderAllQuestions();
    autoSaveSettings();
});

loadSettings();
addQuestion();
</script>
@endsection
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="border-b border-gray-200 px-5">
        <nav class="flex space-x-6 overflow-x-auto">
            <button onclick="showSettingsTab('quiz')" id="quizSettingsNav" class="settings-nav py-2 px-1 border-b-2 font-medium text-sm border-indigo-600 text-indigo-600 whitespace-nowrap">
                <i class="fas fa-question-circle mr-1"></i> Quiz
            </button>
            <button onclick="showSettingsTab('responses')" id="responsesSettingsNav" class="settings-nav py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                <i class="fas fa-reply-all mr-1"></i> Responses
            </button>
            <button onclick="showSettingsTab('presentation')" id="presentationSettingsNav" class="settings-nav py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                <i class="fas fa-desktop mr-1"></i> Presentation
            </button>
            <button onclick="showSettingsTab('defaults')" id="defaultsSettingsNav" class="settings-nav py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                <i class="fas fa-sliders-h mr-1"></i> Defaults
            </button>
            <button onclick="showSettingsTab('advanced')" id="advancedSettingsNav" class="settings-nav py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                <i class="fas fa-cog mr-1"></i> Advanced
            </button>
        </nav>
    </div>

    <div class="p-5 space-y-4">
        <!-- Quiz Settings -->
        <div id="quiz-settings-content" class="settings-content">
            <div class="flex justify-between items-start py-3 border-b">
                <div>
                    <h3 class="font-medium text-gray-800 text-sm">Make this a quiz</h3>
                    <p class="text-xs text-gray-500">Assign point values, set correct answers</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="isQuiz" checked onchange="toggleQuizSettings(); autoSaveSettings();">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div id="quizDetails" class="space-y-3">
                <div class="py-3 border-b">
                    <h3 class="font-medium text-gray-800 text-sm mb-1">Release grade</h3>
                    <div class="space-y-1.5 mt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="release_grade" value="immediately" checked onchange="autoSaveSettings()" class="text-indigo-600 text-sm">
                            <span class="text-xs">Immediately after submission</span>
                            <span class="text-xs text-gray-400">(Users see score right away)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="release_grade" value="later" onchange="autoSaveSettings()" class="text-indigo-600 text-sm">
                            <span class="text-xs">Later, after manual review</span>
                            <span class="text-xs text-gray-400">(Admin must review and release scores)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="release_grade" value="never" onchange="autoSaveSettings()" class="text-indigo-600 text-sm">
                            <span class="text-xs">Never show score</span>
                            <span class="text-xs text-gray-400">(Keep scores private)</span>
                        </label>
                    </div>
                </div>
                <div class="py-3">
                    <h3 class="font-medium text-gray-800 text-sm mb-1">Default points</h3>
                    <div class="flex items-center gap-2">
                        <input type="number" id="defaultPoints" value="1" min="1" max="100" class="w-20 px-2 py-1 border rounded-md text-sm text-center" onchange="autoSaveSettings()">
                        <span class="text-xs text-gray-500">points per question</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">This will be the default for new questions</p>
                </div>
            </div>
        </div>

        <!-- Responses Settings -->
        <div id="responses-settings-content" class="settings-content hidden space-y-4">
            <div class="flex justify-between items-start py-3 border-b">
                <div>
                    <h3 class="font-medium text-gray-800 text-sm">User can view their responses</h3>
                    <p class="text-xs text-gray-500">Allow users to see their submitted answers</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="allowViewResponse" checked onchange="autoSaveSettings();">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="flex justify-between items-start py-3 border-b">
                <div>
                    <h3 class="font-medium text-gray-800 text-sm">Allow response editing</h3>
                    <p class="text-xs text-gray-500">Users can edit their responses after submission</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="allowEditing" onchange="autoSaveSettings();">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="flex justify-between items-start py-3 border-b">
                <div>
                    <h3 class="font-medium text-gray-800 text-sm">Limit to 1 response</h3>
                    <p class="text-xs text-gray-500">Prevent users from submitting more than once</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="limitOneResponse" checked onchange="autoSaveSettings();">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="flex justify-between items-start py-3 border-b">
                <div>
                    <h3 class="font-medium text-gray-800 text-sm">Require login to submit</h3>
                    <p class="text-xs text-gray-500">Only authenticated users can submit responses</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="requireLogin" checked onchange="autoSaveSettings();">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="py-3">
                <h3 class="font-medium text-gray-800 text-sm mb-1">Submission message</h3>
                <p class="text-xs text-gray-500 mb-2">Custom message shown after form submission</p>
                <textarea id="confirmationMessage" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" onchange="autoSaveSettings()">Your response has been recorded. Thank you!</textarea>
            </div>
        </div>

        <!-- Presentation Settings -->
        <div id="presentation-settings-content" class="settings-content hidden space-y-3">
            <div class="py-2 border-b">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="showProgressBar" class="rounded text-indigo-600 text-sm" onchange="autoSaveSettings()">
                    <span class="text-sm">Show progress bar</span>
                    <span class="text-xs text-gray-400">(Display progress during form filling)</span>
                </label>
            </div>
            <div class="py-2 border-b">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="shuffleQuestions" class="rounded text-indigo-600 text-sm" onchange="autoSaveSettings()">
                    <span class="text-sm">Shuffle question order</span>
                    <span class="text-xs text-gray-400">(Randomize question order for each user)</span>
                </label>
            </div>
            <div class="py-2 border-b">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="showQuestionNumbers" class="rounded text-indigo-600 text-sm" checked onchange="autoSaveSettings()">
                    <span class="text-sm">Show question numbers</span>
                    <span class="text-xs text-gray-400">(Display numbering on questions)</span>
                </label>
            </div>
            <div class="py-2 border-b">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="onePageAtATime" class="rounded text-indigo-600 text-sm" onchange="autoSaveSettings()">
                    <span class="text-sm">Show one page at a time</span>
                    <span class="text-xs text-gray-400">(Break form into multiple pages)</span>
                </label>
            </div>
            <div class="py-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="showTimer" class="rounded text-indigo-600 text-sm" onchange="toggleTimerSettings(); autoSaveSettings();">
                    <span class="text-sm">Enable time limit</span>
                    <span class="text-xs text-gray-400">(Set a time limit for completing the form)</span>
                </label>
                <div id="timerSettings" class="mt-3 ml-6 hidden">
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-600">Time limit:</span>
                        <input type="number" id="timeLimit" value="30" min="1" max="180" class="w-20 px-2 py-1 border rounded-md text-sm text-center" onchange="autoSaveSettings()">
                        <span class="text-sm text-gray-600">minutes</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Users must complete the form within this time</p>
                </div>
            </div>
        </div>

        <!-- Defaults Settings -->
        <div id="defaults-settings-content" class="settings-content hidden space-y-3">
            <div class="py-2 border-b">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="defaultRequired" class="rounded text-indigo-600 text-sm" onchange="autoSaveSettings()">
                    <span class="text-sm">Make questions required by default</span>
                    <span class="text-xs text-gray-400">(Users must answer all questions)</span>
                </label>
            </div>
            <div class="py-2 border-b">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="publishByDefault" class="rounded text-indigo-600 text-sm" onchange="autoSaveSettings()">
                    <span class="text-sm">Publish form by default</span>
                    <span class="text-xs text-gray-400">(Form will be visible to users immediately)</span>
                </label>
            </div>
            <div class="py-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="allowPartialPoints" class="rounded text-indigo-600 text-sm" checked onchange="autoSaveSettings()">
                    <span class="text-sm">Allow partial points for checkboxes</span>
                    <span class="text-xs text-gray-400">(Give points for correct answers in checkbox questions)</span>
                </label>
            </div>
        </div>

        <!-- Advanced Settings -->
        <div id="advanced-settings-content" class="settings-content hidden space-y-3">
            <div class="py-2 border-b">
                <h3 class="font-medium text-gray-800 text-sm mb-2">Form Access Control</h3>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="restrictByDepartment" class="rounded text-indigo-600 text-sm" onchange="toggleDepartmentRestriction(); autoSaveSettings();">
                        <span class="text-sm">Restrict by department</span>
                        <span class="text-xs text-gray-400">(Only specific departments can view)</span>
                    </label>
                    <div id="departmentList" class="ml-6 hidden">
                        <select id="allowedDepartments" multiple class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="all">All Departments</option>
                            <option value="music">Music</option>
                            <option value="youth">Youth</option>
                            <option value="children">Children</option>
                            <option value="administration">Administration</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Hold Ctrl/Cmd to select multiple departments</p>
                    </div>
                </div>
            </div>
            <div class="py-2 border-b">
                <h3 class="font-medium text-gray-800 text-sm mb-2">Notifications</h3>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="notifyOnSubmit" class="rounded text-indigo-600 text-sm" onchange="autoSaveSettings()">
                        <span class="text-sm">Notify admin on submission</span>
                        <span class="text-xs text-gray-400">(Send email when someone submits)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="notifyUserOnReview" class="rounded text-indigo-600 text-sm" onchange="autoSaveSettings()">
                        <span class="text-sm">Notify user when reviewed</span>
                        <span class="text-xs text-gray-400">(Send email when score is released)</span>
                    </label>
                </div>
            </div>
            <div class="py-2">
                <h3 class="font-medium text-gray-800 text-sm mb-2">Export Settings</h3>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="allowExport" class="rounded text-indigo-600 text-sm" checked onchange="autoSaveSettings()">
                        <span class="text-sm">Allow CSV export</span>
                        <span class="text-xs text-gray-400">(Admin can export responses)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="includeTimestamps" class="rounded text-indigo-600 text-sm" checked onchange="autoSaveSettings()">
                        <span class="text-sm">Include timestamps in export</span>
                        <span class="text-xs text-gray-400">(Show submission time in CSV)</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    flex-shrink: 0;
    cursor: pointer;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #d1d5db;
    transition: .3s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.toggle-switch input:checked + .toggle-slider {
    background-color: #4f46e5;
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(20px);
}

.toggle-switch input:focus + .toggle-slider {
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.3);
}

.toggle-switch input:disabled + .toggle-slider {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Smooth transitions for all toggles */
.toggle-switch .toggle-slider,
.toggle-switch .toggle-slider:before {
    transition: all 0.3s ease;
}
</style>

<script>
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

function toggleQuizSettings() {
    const isChecked = document.getElementById('isQuiz').checked;
    const quizDetails = document.getElementById('quizDetails');
    if (quizDetails) {
        quizDetails.style.display = isChecked ? 'block' : 'none';
    }
}

function toggleTimerSettings() {
    const isChecked = document.getElementById('showTimer').checked;
    const timerSettings = document.getElementById('timerSettings');
    if (timerSettings) {
        timerSettings.style.display = isChecked ? 'block' : 'none';
    }
}

function toggleDepartmentRestriction() {
    const isChecked = document.getElementById('restrictByDepartment').checked;
    const departmentList = document.getElementById('departmentList');
    if (departmentList) {
        departmentList.style.display = isChecked ? 'block' : 'none';
    }
}

function autoSaveSettings() {
    // Get all settings values
    const settings = {
        is_quiz: document.getElementById('isQuiz')?.checked || false,
        release_grade: document.querySelector('input[name="release_grade"]:checked')?.value || 'immediately',
        default_points: document.getElementById('defaultPoints')?.value || 1,
        allow_view_response: document.getElementById('allowViewResponse')?.checked || false,
        allow_editing: document.getElementById('allowEditing')?.checked || false,
        limit_one_response: document.getElementById('limitOneResponse')?.checked || false,
        require_login: document.getElementById('requireLogin')?.checked || false,
        confirmation_message: document.getElementById('confirmationMessage')?.value || '',
        show_progress_bar: document.getElementById('showProgressBar')?.checked || false,
        shuffle_questions: document.getElementById('shuffleQuestions')?.checked || false,
        show_question_numbers: document.getElementById('showQuestionNumbers')?.checked || false,
        one_page_at_a_time: document.getElementById('onePageAtATime')?.checked || false,
        show_timer: document.getElementById('showTimer')?.checked || false,
        time_limit: document.getElementById('timeLimit')?.value || 30,
        default_required: document.getElementById('defaultRequired')?.checked || false,
        publish_by_default: document.getElementById('publishByDefault')?.checked || false,
        allow_partial_points: document.getElementById('allowPartialPoints')?.checked || false,
        restrict_by_department: document.getElementById('restrictByDepartment')?.checked || false,
        notify_on_submit: document.getElementById('notifyOnSubmit')?.checked || false,
        notify_user_on_review: document.getElementById('notifyUserOnReview')?.checked || false,
        allow_export: document.getElementById('allowExport')?.checked || false,
        include_timestamps: document.getElementById('includeTimestamps')?.checked || false
    };
    
    // Save to localStorage
    localStorage.setItem('form_settings', JSON.stringify(settings));
    
    // Show auto-save indicator if function exists
    if (typeof window.showAutoSaveIndicator === 'function') {
        window.showAutoSaveIndicator();
    }
    
    // Trigger main auto-save if function exists
    if (typeof window.autoSave === 'function') {
        window.autoSave();
    }
}

function loadSettings() {
    const saved = localStorage.getItem('form_settings');
    if (saved) {
        try {
            const s = JSON.parse(saved);
            
            // Quiz settings
            if (document.getElementById('isQuiz')) {
                document.getElementById('isQuiz').checked = s.is_quiz !== false;
                toggleQuizSettings();
            }
            
            // Release grade
            if (s.release_grade) {
                const radio = document.querySelector(`input[name="release_grade"][value="${s.release_grade}"]`);
                if (radio) radio.checked = true;
            }
            
            // Default points
            if (document.getElementById('defaultPoints')) {
                document.getElementById('defaultPoints').value = s.default_points || 1;
            }
            
            // Response settings
            if (document.getElementById('allowViewResponse')) {
                document.getElementById('allowViewResponse').checked = s.allow_view_response !== false;
            }
            if (document.getElementById('allowEditing')) {
                document.getElementById('allowEditing').checked = s.allow_editing || false;
            }
            if (document.getElementById('limitOneResponse')) {
                document.getElementById('limitOneResponse').checked = s.limit_one_response !== false;
            }
            if (document.getElementById('requireLogin')) {
                document.getElementById('requireLogin').checked = s.require_login !== false;
            }
            if (document.getElementById('confirmationMessage')) {
                document.getElementById('confirmationMessage').value = s.confirmation_message || 'Your response has been recorded. Thank you!';
            }
            
            // Presentation settings
            if (document.getElementById('showProgressBar')) {
                document.getElementById('showProgressBar').checked = s.show_progress_bar || false;
            }
            if (document.getElementById('shuffleQuestions')) {
                document.getElementById('shuffleQuestions').checked = s.shuffle_questions || false;
            }
            if (document.getElementById('showQuestionNumbers')) {
                document.getElementById('showQuestionNumbers').checked = s.show_question_numbers !== false;
            }
            if (document.getElementById('onePageAtATime')) {
                document.getElementById('onePageAtATime').checked = s.one_page_at_a_time || false;
            }
            if (document.getElementById('showTimer')) {
                document.getElementById('showTimer').checked = s.show_timer || false;
                toggleTimerSettings();
            }
            if (document.getElementById('timeLimit')) {
                document.getElementById('timeLimit').value = s.time_limit || 30;
            }
            
            // Defaults settings
            if (document.getElementById('defaultRequired')) {
                document.getElementById('defaultRequired').checked = s.default_required || false;
            }
            if (document.getElementById('publishByDefault')) {
                document.getElementById('publishByDefault').checked = s.publish_by_default || false;
            }
            if (document.getElementById('allowPartialPoints')) {
                document.getElementById('allowPartialPoints').checked = s.allow_partial_points !== false;
            }
            
            // Advanced settings
            if (document.getElementById('restrictByDepartment')) {
                document.getElementById('restrictByDepartment').checked = s.restrict_by_department || false;
                toggleDepartmentRestriction();
            }
            if (document.getElementById('notifyOnSubmit')) {
                document.getElementById('notifyOnSubmit').checked = s.notify_on_submit || false;
            }
            if (document.getElementById('notifyUserOnReview')) {
                document.getElementById('notifyUserOnReview').checked = s.notify_user_on_review || false;
            }
            if (document.getElementById('allowExport')) {
                document.getElementById('allowExport').checked = s.allow_export !== false;
            }
            if (document.getElementById('includeTimestamps')) {
                document.getElementById('includeTimestamps').checked = s.include_timestamps !== false;
            }
            
        } catch (e) {
            console.log('Error loading settings:', e);
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load settings
    loadSettings();
    
    // Initialize quiz toggle
    toggleQuizSettings();
    toggleTimerSettings();
    toggleDepartmentRestriction();
});

// Expose functions globally
window.showSettingsTab = showSettingsTab;
window.toggleQuizSettings = toggleQuizSettings;
window.toggleTimerSettings = toggleTimerSettings;
window.toggleDepartmentRestriction = toggleDepartmentRestriction;
window.autoSaveSettings = autoSaveSettings;
window.loadSettings = loadSettings;
</script>
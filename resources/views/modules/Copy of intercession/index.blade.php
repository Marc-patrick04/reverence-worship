@extends('layouts.app')

@section('title', 'Intercession & Spiritual Growth')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- HEADER SECTION --}}
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Intercession and Spiritual Growth Department</h1>
        <p class="text-gray-600 mt-1">Manage spiritual growth activities and biblical content for the church community.</p>
    </div>

    {{-- NAVIGATION TABS --}}
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8 overflow-x-auto">
            <button onclick="showMainTab('form')" id="main-tab-form" class="main-tab-btn py-2 px-1 border-b-2 font-medium text-sm transition">
                <i class="fas fa-file-alt mr-2"></i>Form
            </button>
            <button onclick="showMainTab('devotion')" id="main-tab-devotion" class="main-tab-btn py-2 px-1 border-b-2 font-medium text-sm transition">
                <i class="fas fa-praying-hands mr-2"></i>Daily Devotion
            </button>
            <button onclick="showMainTab('action')" id="main-tab-action" class="main-tab-btn py-2 px-1 border-b-2 font-medium text-sm transition">
                <i class="fas fa-tasks mr-2"></i>Action Plans
            </button>
            <button onclick="showMainTab('archives')" id="main-tab-archives" class="main-tab-btn py-2 px-1 border-b-2 font-medium text-sm transition">
                <i class="fas fa-archive mr-2"></i>Archives
            </button>
        </nav>
    </div>

    {{-- ==================== FORM TAB (FULL WORKING) ==================== --}}
    <div id="main-form-tab" class="main-tab-content">
        <div class="bg-white rounded-xl shadow-md p-6">
            
            {{-- Stats Cards --}}
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <p class="text-3xl font-bold text-blue-600">{{ $activeForms ?? 0 }}</p>
                    <p class="text-xs text-gray-600">ACTIVE FORMS</p>
                </div>
                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $myAttempts ?? 0 }}</p>
                    <p class="text-xs text-gray-600">MY ATTEMPTS</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-4 text-center">
                    <p class="text-3xl font-bold text-purple-600">{{ number_format($bestAvg ?? 0, 1) }}%</p>
                    <p class="text-xs text-gray-600">BEST AVG</p>
                </div>
            </div>
            
            {{-- Form Sub Navigation --}}
            <div class="flex flex-wrap gap-3 mb-6 border-b pb-3">
                <button onclick="showFormTab('available')" id="form-tab-available" class="form-sub-tab px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white">
                    <i class="fas fa-list mr-1"></i> Available Forms
                </button>
                <button onclick="showFormTab('results')" id="form-tab-results" class="form-sub-tab px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300">
                    <i class="fas fa-chart-bar mr-1"></i> My Results
                </button>
                <button onclick="showFormTab('participants')" id="form-tab-participants" class="form-sub-tab px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300">
                    <i class="fas fa-users mr-1"></i> Participants & Scores
                </button>
                @if(auth()->user()->isSuperAdmin() || auth()->user()->canAccess('intercession', 'view-forms'))
                <button onclick="showFormTab('manage')" id="form-tab-manage" class="form-sub-tab px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300">
                    <i class="fas fa-cog mr-1"></i> Manage Forms
                </button>
                @endif
                @if(auth()->user()->isSuperAdmin() || auth()->user()->canAccess('intercession', 'create-forms'))
                <a href="{{ route('forms.manage.create') }}" class="px-4 py-2 rounded-lg text-sm font-medium bg-green-600 text-white hover:bg-green-700 flex items-center gap-2">
                    <i class="fas fa-plus mr-1"></i> Create Form
                </a>
                @endif
            </div>
            
            {{-- Available Forms Content --}}
            <div id="form-available-content" class="form-sub-content">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Available Forms</h3>
                <div id="available-forms-list">
                    @forelse($availableForms ?? [] as $form)
                    @php
                        $formQuestions = is_string($form->questions) ? json_decode($form->questions, true) : ($form->questions ?? []);
                        $questionsCount = is_array($formQuestions) ? count($formQuestions) : 0;
                        $formSettings = is_string($form->settings) ? json_decode($form->settings, true) : ($form->settings ?? []);
                        $isPublished = $formSettings['is_published'] ?? false;
                    @endphp
                    @if($isPublished)
                    <div class="border rounded-lg p-4 mb-3 hover:shadow-md transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $form->title }}</h4>
                                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($form->description, 100) }}</p>
                                <p class="text-xs text-gray-400 mt-2">
                                    <i class="fas fa-question-circle mr-1"></i> {{ $questionsCount }} questions
                                </p>
                            </div>
                            <a href="{{ route('forms.take', $form->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                Take Form
                            </a>
                        </div>
                    </div>
                    @endif
                    @empty
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-file-alt text-4xl text-gray-300 mb-3"></i>
                        <p>No forms available</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            {{-- My Results Content --}}
            <div id="form-results-content" class="form-sub-content hidden">
                <h3 class="text-lg font-bold text-gray-800 mb-4">My Results</h3>
                <div id="my-results-list">
                    @forelse($mySubmissions ?? [] as $submission)
                    <div class="border rounded-lg p-4 mb-3 hover:shadow-md transition">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $submission->form->title }}</h4>
                                <p class="text-xs text-gray-500">Submitted: {{ \Carbon\Carbon::parse($submission->submitted_at)->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-blue-600">{{ number_format($submission->score, 1) }}%</p>
                                <button onclick="viewFormResult({{ $submission->form_id }})" class="text-sm text-blue-600 hover:underline">View Details</button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-chart-line text-4xl text-gray-300 mb-3"></i>
                        <p>No results yet</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Participants & Scores Content --}}
            <div id="form-participants-content" class="form-sub-content hidden">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Participants & Scores</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Participant</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Form</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $allSubmissions = App\Models\Intercession\FormSubmission::with(['user', 'form'])->orderBy('score', 'desc')->get();
                            @endphp
                            @forelse($allSubmissions as $submission)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $submission->user->name ?? 'Unknown' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $submission->form->title ?? 'Unknown' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                        {{ number_format($submission->score, 1) }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ \Carbon\Carbon::parse($submission->submitted_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">No submissions yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Manage Forms Content --}}
            <div id="form-manage-content" class="form-sub-content hidden">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Manage Forms</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Questions</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submissions</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="manage-forms-table-body" class="bg-white divide-y divide-gray-200">
                            @forelse($allForms ?? [] as $form)
                            <tr id="form-row-{{ $form->id }}" class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $form->title }}</p>
                                        <p class="text-xs text-gray-500">{{ Str::limit($form->description, 50) }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    @php
                                        $manageQuestions = is_string($form->questions) ? json_decode($form->questions, true) : ($form->questions ?? []);
                                    @endphp
                                    {{ is_array($manageQuestions) ? count($manageQuestions) : 0 }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $form->submissions()->count() }}
                                </td>
                                @php
                                    $formSettings = $form->settings;
                                    if (is_string($formSettings)) {
                                        $formSettings = json_decode($formSettings, true);
                                    }
                                    if (!is_array($formSettings)) {
                                        $formSettings = [];
                                    }
                                    $isPublished = $formSettings['is_published'] ?? false;
                                @endphp
                                <td class="px-4 py-3">
                                    <span id="status-badge-{{ $form->id }}" class="px-2 py-1 text-xs rounded-full 
                                        {{ $isPublished ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $isPublished ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2 flex-wrap">
                                        <button onclick="togglePublish({{ $form->id }})" 
                                            id="publish-btn-{{ $form->id }}"
                                            class="px-2 py-1 text-xs rounded 
                                                {{ $isPublished ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                            {{ $isPublished ? 'Unpublish' : 'Publish' }}
                                        </button>
                                        <button onclick="editForm({{ $form->id }})" class="text-blue-600 hover:text-blue-800" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="viewSubmissions({{ $form->id }})" class="text-green-600 hover:text-green-800" title="Responses">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="deleteForm({{ $form->id }})" class="text-red-600 hover:text-red-800" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="no-forms-row">
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">No forms created yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== DAILY DEVOTION TAB ==================== --}}
    <div id="main-devotion-tab" class="main-tab-content hidden">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div id="devotion-content">
                @if(isset($todayDevotion) && $todayDevotion)
                <div class="border-l-4 border-blue-500 pl-4 mb-6">
                    <h3 class="text-lg font-bold text-gray-800">{{ $todayDevotion->title }}</h3>
                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($todayDevotion->date)->format('d/m/Y') }}</p>
                </div>
                <div class="prose max-w-none mb-6">
                    <p class="text-gray-700">{{ $todayDevotion->content }}</p>
                    @if($todayDevotion->bible_verse)
                    <p class="text-blue-600 italic mt-3">— {{ $todayDevotion->bible_verse }}</p>
                    @endif
                </div>
                @if($hasCompletedToday ?? false)
                    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg text-center">
                        <i class="fas fa-check-circle mr-2"></i> Already Taken
                    </div>
                @else
                    <form action="{{ route('intercession.devotion.complete', $todayDevotion->id) }}" method="POST" id="devotion-complete-form">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-medium">Mark as Read</button>
                    </form>
                @endif
                @else
                <div class="text-center py-8">
                    <i class="fas fa-praying-hands text-5xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">No devotion for today. Check back later.</p>
                </div>
                @endif
                
                @if(isset($recentDevotions) && count($recentDevotions) > 0)
                <div class="mt-6 pt-4 border-t">
                    <h4 class="font-semibold text-gray-700 mb-3">Recent Devotions</h4>
                    <div id="recent-devotions-list" class="space-y-2">
                        @foreach($recentDevotions as $dev)
                        <a href="{{ route('intercession.devotion.show', $dev->id) }}" class="block p-3 border rounded-lg hover:bg-gray-50">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $dev->title }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($dev->date)->format('d/m/Y') }}</p>
                                </div>
                                @if(isset($dev->completed_by_user) && $dev->completed_by_user)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @endif
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ==================== ACTION PLANS TAB ==================== --}}
    <div id="main-action-tab" class="main-tab-content hidden">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">My Action Plans</h3>
                <button onclick="openCreateActionPlanModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-plus mr-2"></i> New Plan
                </button>
            </div>
            <div id="action-plans-list" class="space-y-3">
                @forelse($actionPlans ?? [] as $plan)
                <div id="action-plan-{{ $plan->id }}" class="border rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $plan->title }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $plan->description ?? 'No description' }}</p>
                            <div class="flex gap-3 mt-2 text-xs text-gray-500">
                                @if($plan->due_date)
                                <span><i class="fas fa-calendar mr-1"></i> Due: {{ \Carbon\Carbon::parse($plan->due_date)->format('d/m/Y') }}</span>
                                @endif
                                @if($plan->assignedUser)
                                <span><i class="fas fa-user mr-1"></i> Assigned to: {{ $plan->assignedUser->name }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $plan->status == 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $plan->status == 'in-progress' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $plan->status == 'pending' ? 'bg-gray-100 text-gray-600' : '' }}">
                                {{ ucfirst($plan->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div id="no-action-plans" class="text-center text-gray-500 py-8">
                    <i class="fas fa-tasks text-4xl text-gray-300 mb-3"></i>
                    <p>No action plans yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ==================== ARCHIVES TAB ==================== --}}
    <div id="main-archives-tab" class="main-tab-content hidden">
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Spiritual Archives</h3>
            <div class="text-center text-gray-500 py-8">
                <i class="fas fa-archive text-4xl text-gray-300 mb-3"></i>
                <p>Archives coming soon</p>
            </div>
        </div>
    </div>
</div>

{{-- Create Action Plan Modal --}}
<div id="createActionPlanModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-bold text-gray-800">Create Action Plan</h3>
            <button onclick="closeModal('createActionPlanModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="create-action-plan-form" method="POST" action="{{ route('intercession.action-plans.store') }}">
            @csrf
            <div class="mt-4 space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="date" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Assign To</label>
                    <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Myself</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-5 pt-3 border-t">
                <button type="button" onclick="closeModal('createActionPlanModal')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Create</button>
            </div>
        </form>
    </div>
</div>

<script>
// Main Tab Switching
function showMainTab(tabName) {
    document.querySelectorAll('.main-tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    document.querySelectorAll('.main-tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-600', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    document.getElementById(`main-${tabName}-tab`).classList.remove('hidden');
    document.getElementById(`main-tab-${tabName}`).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById(`main-tab-${tabName}`).classList.add('border-blue-600', 'text-blue-600');
    
    // Refresh data when switching to certain tabs
    if (tabName === 'action') {
        refreshActionPlans();
    }
}

// Form Sub-tab Switching
function showFormTab(tabName) {
    document.querySelectorAll('.form-sub-content').forEach(content => {
        content.classList.add('hidden');
    });
    document.querySelectorAll('.form-sub-tab').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    document.getElementById(`form-${tabName}-content`).classList.remove('hidden');
    const activeBtn = document.getElementById(`form-tab-${tabName}`);
    if (activeBtn) {
        activeBtn.classList.remove('bg-gray-200', 'text-gray-700');
        activeBtn.classList.add('bg-blue-600', 'text-white');
    }
    
    // Refresh data when switching to manage tab
    if (tabName === 'manage') {
        refreshManageForms();
    }
}

// Helper function to reload page content
function refreshPageContent() {
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Update stats
        const activeForms = doc.querySelector('.bg-blue-50 .text-3xl')?.textContent;
        const myAttempts = doc.querySelector('.bg-green-50 .text-3xl')?.textContent;
        const bestAvg = doc.querySelector('.bg-purple-50 .text-3xl')?.textContent;
        
        if (activeForms) document.querySelector('.bg-blue-50 .text-3xl').textContent = activeForms;
        if (myAttempts) document.querySelector('.bg-green-50 .text-3xl').textContent = myAttempts;
        if (bestAvg) document.querySelector('.bg-purple-50 .text-3xl').textContent = bestAvg;
        
        // Update available forms
        const availableForms = doc.querySelector('#available-forms-list')?.innerHTML;
        if (availableForms) document.querySelector('#available-forms-list').innerHTML = availableForms;
        
        // Update my results
        const myResults = doc.querySelector('#my-results-list')?.innerHTML;
        if (myResults) document.querySelector('#my-results-list').innerHTML = myResults;
        
        // Update devotion content
        const devotionContent = doc.querySelector('#devotion-content')?.innerHTML;
        if (devotionContent && document.querySelector('#devotion-content')) {
            document.querySelector('#devotion-content').innerHTML = devotionContent;
        }
        
        // Update action plans
        const actionPlans = doc.querySelector('#action-plans-list')?.innerHTML;
        if (actionPlans) document.querySelector('#action-plans-list').innerHTML = actionPlans;
    })
    .catch(error => console.error('Error refreshing page:', error));
}

// Refresh manage forms table
function refreshManageForms() {
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newTableBody = doc.querySelector('#manage-forms-table-body')?.innerHTML;
        if (newTableBody) {
            document.querySelector('#manage-forms-table-body').innerHTML = newTableBody;
        }
    })
    .catch(error => console.error('Error refreshing manage forms:', error));
}

// Refresh action plans
function refreshActionPlans() {
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newActionPlans = doc.querySelector('#action-plans-list')?.innerHTML;
        if (newActionPlans) {
            document.querySelector('#action-plans-list').innerHTML = newActionPlans;
        }
    })
    .catch(error => console.error('Error refreshing action plans:', error));
}

// Form Actions
function viewFormResult(formId) {
    window.location.href = `/forms/${formId}/results`;
}

function editForm(formId) {
    window.location.href = `/forms/manage/${formId}/edit`;
}

function viewSubmissions(formId) {
    window.location.href = `/forms/manage/${formId}/submissions`;
}

function togglePublish(formId) {
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    fetch(`/forms/manage/${formId}/toggle-publish`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const publishBtn = document.getElementById(`publish-btn-${formId}`);
            const statusBadge = document.getElementById(`status-badge-${formId}`);
            
            if (data.is_published) {
                publishBtn.textContent = 'Unpublish';
                publishBtn.classList.remove('bg-green-100', 'text-green-700');
                publishBtn.classList.add('bg-yellow-100', 'text-yellow-700');
                statusBadge.textContent = 'Published';
                statusBadge.classList.remove('bg-gray-100', 'text-gray-500');
                statusBadge.classList.add('bg-green-100', 'text-green-700');
            } else {
                publishBtn.textContent = 'Publish';
                publishBtn.classList.remove('bg-yellow-100', 'text-yellow-700');
                publishBtn.classList.add('bg-green-100', 'text-green-700');
                statusBadge.textContent = 'Draft';
                statusBadge.classList.remove('bg-green-100', 'text-green-700');
                statusBadge.classList.add('bg-gray-100', 'text-gray-500');
            }
            refreshPageContent();
        } else {
            alert('Error toggling publish status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error toggling publish status');
    });
}

function deleteForm(formId) {
    if (confirm('Delete this form? All responses will be lost.')) {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        fetch(`/forms/manage/${formId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById(`form-row-${formId}`);
                if (row) {
                    row.remove();
                }
                const tbody = document.querySelector('#manage-forms-table-body');
                if (tbody && tbody.children.length === 0) {
                    tbody.innerHTML = '<tr id="no-forms-row"><td colspan="5" class="px-4 py-8 text-center text-gray-500">No forms created yet</td></tr>';
                }
                refreshPageContent();
                showNotification('Form deleted successfully!', 'success');
            } else {
                alert('Error deleting form: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting form: ' + error.message);
        });
    }
}

// Devotion form submission
const devotionForm = document.getElementById('devotion-complete-form');
if (devotionForm) {
    devotionForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: new FormData(this)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                refreshPageContent();
                showNotification('Devotion marked as read!', 'success');
            } else {
                alert('Error marking devotion as read');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error marking devotion as read');
        });
    });
}

// Action Plan form submission
const actionPlanForm = document.getElementById('create-action-plan-form');
if (actionPlanForm) {
    actionPlanForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: new FormData(this)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('createActionPlanModal');
                this.reset();
                refreshActionPlans();
                showNotification('Action plan created successfully!', 'success');
            } else {
                alert('Error creating action plan: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating action plan');
        });
    });
}

// Notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg text-white z-50 ${
        type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    notification.innerHTML = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

// Modal functions
function openCreateActionPlanModal() {
    document.getElementById('createActionPlanModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
</script>

<style>
.modal { display: none; }
.modal:not(.hidden) { display: block !important; }
.main-tab-btn, .form-sub-tab { transition: all 0.3s ease; }
.main-tab-btn:hover, .form-sub-tab:hover { opacity: 0.8; }
</style>
@endsection
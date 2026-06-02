<div class="bg-white rounded-xl shadow-md p-6">
    
    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-blue-600">{{ $stats['total_forms'] ?? 0 }}</p>
            <p class="text-xs text-gray-600">TOTAL FORMS</p>
        </div>
        <div class="bg-green-50 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-green-600">{{ $stats['my_attempts'] ?? 0 }}</p>
            <p class="text-xs text-gray-600">MY ATTEMPTS</p>
        </div>
        <div class="bg-purple-50 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['best_avg'] ?? 0, 1) }}%</p>
            <p class="text-xs text-gray-600">BEST AVG</p>
        </div>
    </div>

    {{-- Form Actions --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex gap-2 flex-wrap">
            <button onclick="showFormSection('available')" id="form-section-available" class="section-btn px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white">
                Available Forms
            </button>
            <button onclick="showFormSection('results')" id="form-section-results" class="section-btn px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700">
                My Results
            </button>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->canAccess('intercession', 'view-forms'))
            <button onclick="showFormSection('manage')" id="form-section-manage" class="section-btn px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700">
                Manage Forms
            </button>
            @endif
        </div>
        
        {{-- Create Form Button - Always Visible --}}
        @if(auth()->user()->isSuperAdmin() || auth()->user()->canAccess('intercession', 'create-forms'))
        <a href="{{ route('forms.manage.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
            <i class="fas fa-plus mr-1"></i> Create Form
        </a>
        @endif
    </div>

    {{-- Available Forms Section --}}
    <div id="available-forms-section" class="form-section">
        <h3 class="text-lg font-bold mb-4">Available Forms</h3>
        @forelse($availableForms ?? [] as $form)
        @php
            $formSettings = is_string($form->settings) ? json_decode($form->settings, true) : ($form->settings ?? []);
            $isPublished = $formSettings['is_published'] ?? false;
            $questions = is_string($form->questions) ? json_decode($form->questions, true) : ($form->questions ?? []);
            $questionsCount = is_array($questions) ? count($questions) : 0;
            $createdDate = isset($form->created_at) ? \Carbon\Carbon::parse($form->created_at)->format('F j, Y') : 'Date unknown';
            $hasTaken = isset($mySubmissions) && $mySubmissions->contains('form_id', $form->id);
        @endphp
        @if($isPublished)
        <div class="border rounded-lg p-4 mb-4 hover:shadow-lg transition-all duration-300">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <h4 class="font-semibold text-gray-800 text-lg">{{ $form->title }}</h4>
                        @if($hasTaken)
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                            <i class="fas fa-check-circle"></i> Completed
                        </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mb-2">{{ Str::limit($form->description ?? 'No description', 150) }}</p>
                    <div class="flex flex-wrap gap-4 text-xs text-gray-500">
                        <span class="flex items-center gap-1">
                            <i class="fas fa-question-circle text-blue-500"></i>
                            {{ $questionsCount }} {{ Str::plural('question', $questionsCount) }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fas fa-calendar-alt text-gray-400"></i>
                            Created: {{ $createdDate }}
                        </span>
                        @if(isset($form->updated_at) && $form->updated_at != $form->created_at)
                        <span class="flex items-center gap-1">
                            <i class="fas fa-edit text-gray-400"></i>
                            Updated: {{ \Carbon\Carbon::parse($form->updated_at)->format('F j, Y') }}
                        </span>
                        @endif
                    </div>
                </div>
                <div class="ml-4">
                    <a href="{{ route('forms.take', $form->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm transition flex items-center gap-2">
                        <i class="fas fa-pen-alt"></i> Take Form
                    </a>
                </div>
            </div>
        </div>
        @endif
        @empty
        <div class="text-center py-12">
            <i class="fas fa-file-alt text-5xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">No forms available</p>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->canAccess('intercession', 'create-forms'))
            <a href="{{ route('forms.manage.create') }}" class="inline-block mt-3 text-blue-600 hover:text-blue-800 text-sm">
                <i class="fas fa-plus"></i> Create your first form
            </a>
            @endif
        </div>
        @endforelse
    </div>

    {{-- My Results Section --}}
    <div id="results-section" class="form-section hidden">
        <h3 class="text-lg font-bold mb-4">My Results</h3>
        @forelse($mySubmissions ?? [] as $submission)
        @php
            $questions = is_string($submission->form->questions) ? json_decode($submission->form->questions, true) : ($submission->form->questions ?? []);
            $questionsCount = is_array($questions) ? count($questions) : 0;
        @endphp
        <div class="border rounded-lg p-4 mb-3 hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <h4 class="font-semibold text-gray-800">{{ $submission->form->title }}</h4>
                    <div class="flex gap-3 mt-1 text-xs text-gray-500">
                        <span><i class="fas fa-question-circle"></i> {{ $questionsCount }} questions</span>
                        <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($submission->submitted_at)->format('F j, Y') }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($submission->score, 1) }}%</p>
                    <button onclick="viewFormResult({{ $submission->form_id }})" class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                        View Details <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <i class="fas fa-chart-line text-5xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">No results yet</p>
            <p class="text-xs text-gray-400 mt-1">Complete a form to see your results here</p>
        </div>
        @endforelse
    </div>

    {{-- Manage Forms Section --}}
    @if(auth()->user()->isSuperAdmin() || auth()->user()->canAccess('intercession', 'view-forms'))
    <div id="manage-section" class="form-section hidden">
        <h3 class="text-lg font-bold mb-4">Manage Forms</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Questions</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submissions</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allForms ?? [] as $form)
                    @php
                        $formSettings = is_string($form->settings) ? json_decode($form->settings, true) : ($form->settings ?? []);
                        $isPublished = $formSettings['is_published'] ?? false;
                        $questions = is_string($form->questions) ? json_decode($form->questions, true) : ($form->questions ?? []);
                        $questionsCount = is_array($questions) ? count($questions) : 0;
                        $submissionsCount = DB::table('form_submissions')->where('form_id', $form->id)->count();
                    @endphp
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div>
                                <p class="font-medium text-gray-800">{{ $form->title }}</p>
                                <p class="text-xs text-gray-500">{{ Str::limit($form->description ?? '', 50) }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 text-center">{{ $questionsCount }}</td>
                        <td class="px-4 py-3">
                            <span id="status-badge-{{ $form->id }}" class="px-2 py-1 text-xs rounded-full 
                                {{ $isPublished ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $isPublished ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 text-center">{{ $submissionsCount }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2 flex-wrap">
                                <button onclick="viewForm({{ $form->id }})" class="text-green-600 hover:text-green-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="togglePublish({{ $form->id }})" 
                                    id="publish-btn-{{ $form->id }}"
                                    class="px-2 py-1 text-xs rounded transition
                                        {{ $isPublished ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                    {{ $isPublished ? 'Unpublish' : 'Publish' }}
                                </button>
                                <button onclick="editForm({{ $form->id }})" class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="viewSubmissions({{ $form->id }})" class="text-purple-600 hover:text-purple-800" title="Submissions">
                                    <i class="fas fa-users"></i>
                                </button>
                                <button onclick="deleteForm({{ $form->id }})" class="text-red-600 hover:text-red-800" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
<script>
// Function to show form section - NO REFRESH NEEDED
function showFormSection(section) {
    console.log('=== showFormSection called ===');
    console.log('Section requested:', section);
    
    // Map section names to actual element IDs
    let elementId = '';
    if (section === 'available') {
        elementId = 'available-forms-section';
    } else if (section === 'results') {
        elementId = 'results-section';
    } else if (section === 'manage') {
        elementId = 'manage-section';
    }
    
    console.log('Looking for element with ID:', elementId);
    
    // Get all sections
    const availableSection = document.getElementById('available-forms-section');
    const resultsSection = document.getElementById('results-section');
    const manageSection = document.getElementById('manage-section');
    
    // Hide all sections
    if (availableSection) availableSection.classList.add('hidden');
    if (resultsSection) resultsSection.classList.add('hidden');
    if (manageSection) manageSection.classList.add('hidden');
    
    // Show selected section
    const activeSection = document.getElementById(elementId);
    if (activeSection) {
        activeSection.classList.remove('hidden');
        console.log('Showing section:', elementId);
    } else {
        console.error('Section not found! Make sure there is an element with id:', elementId);
        return;
    }
    
    // Update button styles
    const availableBtn = document.getElementById('form-section-available');
    const resultsBtn = document.getElementById('form-section-results');
    const manageBtn = document.getElementById('form-section-manage');
    
    // Reset all buttons to gray
    if (availableBtn) {
        availableBtn.classList.remove('bg-blue-600', 'text-white');
        availableBtn.classList.add('bg-gray-200', 'text-gray-700');
    }
    if (resultsBtn) {
        resultsBtn.classList.remove('bg-blue-600', 'text-white');
        resultsBtn.classList.add('bg-gray-200', 'text-gray-700');
    }
    if (manageBtn) {
        manageBtn.classList.remove('bg-blue-600', 'text-white');
        manageBtn.classList.add('bg-gray-200', 'text-gray-700');
    }
    
    // Highlight the clicked button
    const activeButton = document.getElementById(`form-section-${section}`);
    if (activeButton) {
        activeButton.classList.remove('bg-gray-200', 'text-gray-700');
        activeButton.classList.add('bg-blue-600', 'text-white');
        console.log('Active button set for:', section);
    }
    
    // Save to localStorage
    localStorage.setItem('activeFormSection', section);
    console.log('=== showFormSection completed ===');
}

// Make functions globally available
window.showFormSection = showFormSection;

window.viewForm = function(id) {
    window.location.href = `/forms/${id}/take`;
};

window.editForm = function(id) {
    window.location.href = `/forms/manage/${id}/edit`;
};

window.viewSubmissions = function(id) {
    window.location.href = `/forms/manage/${id}/submissions`;
};

window.viewFormResult = function(formId) {
    window.location.href = `/forms/${formId}/results`;
};

window.togglePublish = function(formId) {
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
                showNotification('Form published successfully!', 'success');
            } else {
                publishBtn.textContent = 'Publish';
                publishBtn.classList.remove('bg-yellow-100', 'text-yellow-700');
                publishBtn.classList.add('bg-green-100', 'text-green-700');
                statusBadge.textContent = 'Draft';
                statusBadge.classList.remove('bg-green-100', 'text-green-700');
                statusBadge.classList.add('bg-gray-100', 'text-gray-500');
                showNotification('Form unpublished', 'info');
            }
        } else {
            showNotification('Error toggling publish status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error toggling publish status', 'error');
    });
};

window.deleteForm = function(id) {
    if(confirm('Delete this form? All responses will be lost forever.')) {
        fetch(`/forms/manage/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Form deleted successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Error deleting form', 'error');
            }
        });
    }
};

window.showNotification = function(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg text-white z-50 transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    notification.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>${message}`;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
};

// On page load, restore the last active section
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOMContentLoaded fired ===');
    
    const savedSection = localStorage.getItem('activeFormSection');
    console.log('Saved section from localStorage:', savedSection);
    
    // Map section names to element IDs
    let elementToShow = '';
    if (savedSection === 'results') {
        elementToShow = 'results-section';
    } else if (savedSection === 'manage') {
        elementToShow = 'manage-section';
    } else {
        elementToShow = 'available-forms-section';
    }
    
    console.log('Will show element with ID:', elementToShow);
    
    // Hide all sections
    const availableSection = document.getElementById('available-forms-section');
    const resultsSection = document.getElementById('results-section');
    const manageSection = document.getElementById('manage-section');
    
    if (availableSection) availableSection.classList.add('hidden');
    if (resultsSection) resultsSection.classList.add('hidden');
    if (manageSection) manageSection.classList.add('hidden');
    
    // Show the saved section
    const sectionToShow = document.getElementById(elementToShow);
    if (sectionToShow) {
        sectionToShow.classList.remove('hidden');
        console.log('Showing section:', elementToShow);
    }
    
    // Set button styles based on active section
    const activeSection = savedSection === 'results' ? 'results' : (savedSection === 'manage' ? 'manage' : 'available');
    
    const availableBtn = document.getElementById('form-section-available');
    const resultsBtn = document.getElementById('form-section-results');
    const manageBtn = document.getElementById('form-section-manage');
    
    if (availableBtn) {
        availableBtn.classList.remove('bg-blue-600', 'text-white');
        availableBtn.classList.add('bg-gray-200', 'text-gray-700');
    }
    if (resultsBtn) {
        resultsBtn.classList.remove('bg-blue-600', 'text-white');
        resultsBtn.classList.add('bg-gray-200', 'text-gray-700');
    }
    if (manageBtn) {
        manageBtn.classList.remove('bg-blue-600', 'text-white');
        manageBtn.classList.add('bg-gray-200', 'text-gray-700');
    }
    
    const activeButton = document.getElementById(`form-section-${activeSection}`);
    if (activeButton) {
        activeButton.classList.remove('bg-gray-200', 'text-gray-700');
        activeButton.classList.add('bg-blue-600', 'text-white');
        console.log('Active button set to:', activeSection);
    }
});
</script>
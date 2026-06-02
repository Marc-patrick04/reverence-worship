@extends('layouts.app')

@section('title', 'Page & Feature Assignment')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Page & Feature Assignment</h1>
            <p class="text-gray-600 mt-1">Assign pages and features to roles</p>
        </div>
    </div>
    
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Role Selection -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Select Role</h3>
            <select id="roleSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Select a role --</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                @endforeach
            </select>
            
            <div class="mt-6 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Select a role to assign which pages and features they can access.
                </p>
            </div>
        </div>
        
        <!-- Pages & Features Assignment -->
        <div class="lg:col-span-2">
            <div id="assignmentArea" class="bg-white rounded-lg shadow-lg p-6 hidden">
                <h3 id="selectedRoleName" class="text-xl font-bold text-gray-800 mb-4"></h3>
                
                <div id="pagesContainer" class="space-y-4 max-h-96 overflow-y-auto">
                    <!-- Pages will be loaded here dynamically -->
                </div>
                
                <div class="flex justify-end mt-6 pt-4 border-t">
                    <button id="saveButton" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        <i class="fas fa-save mr-2"></i>
                        Save Assignments
                    </button>
                </div>
            </div>
            
            <div id="noRoleSelected" class="bg-white rounded-lg shadow-lg p-6 text-center text-gray-500">
                <i class="fas fa-hand-pointer fa-3x mb-3 text-gray-300"></i>
                <p>Select a role from the left to start assigning pages and features</p>
            </div>
        </div>
    </div>
</div>

<script>
let currentRoleId = null;
let allPages = @json($pages);
let allFeatures = @json($allFeatures);
let allAssignments = @json($allAssignments);

// Group features by page
let featuresByPage = {};
allFeatures.forEach(feature => {
    if (!featuresByPage[feature.page_id]) {
        featuresByPage[feature.page_id] = [];
    }
    featuresByPage[feature.page_id].push(feature);
});

document.getElementById('roleSelect').addEventListener('change', function() {
    const roleId = parseInt(this.value);
    if (!roleId) {
        document.getElementById('assignmentArea').classList.add('hidden');
        document.getElementById('noRoleSelected').classList.remove('hidden');
        return;
    }
    
    currentRoleId = roleId;
    const roleName = this.options[this.selectedIndex].text;
    document.getElementById('selectedRoleName').innerHTML = `<i class="fas fa-tag mr-2"></i>Assigning for: ${roleName}`;
    document.getElementById('assignmentArea').classList.remove('hidden');
    document.getElementById('noRoleSelected').classList.add('hidden');
    
    // Render pages with existing assignments
    renderPages(roleId);
});

function renderPages(roleId) {
    const container = document.getElementById('pagesContainer');
    container.innerHTML = '';
    
    // Get assigned features for this role
    const roleAssignments = allAssignments[roleId] || [];
    const assignedFeatureIds = roleAssignments.map(a => a.feature_id);
    const assignedPageIds = [...new Set(roleAssignments.map(a => a.page_id))];
    
    allPages.forEach(page => {
        // Get features for this page
        const pageFeatures = featuresByPage[page.id] || [];
        
        // Check if page has any assigned features
        const hasAnyFeature = assignedPageIds.includes(page.id);
        
        // Create page card
        const pageCard = document.createElement('div');
        pageCard.className = 'border rounded-lg p-4 hover:shadow-md transition';
        pageCard.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" class="page-checkbox rounded border-gray-300 text-blue-600" 
                           data-page-id="${page.id}" ${hasAnyFeature ? 'checked' : ''}>
                    <i class="fas ${page.icon} text-blue-600 mr-2"></i>
                    <span class="font-semibold text-gray-800">${page.display_name}</span>
                </label>
                <span class="text-xs text-gray-400">${page.name}</span>
            </div>
            <div class="features-container ml-6 pl-4 border-l-2 border-gray-200 ${hasAnyFeature ? '' : 'hidden'}" 
                 data-page-id="${page.id}">
                <div class="text-sm text-gray-500 mb-2">Features:</div>
                <div class="grid grid-cols-2 gap-2" id="features-${page.id}">
                    ${renderFeatures(pageFeatures, assignedFeatureIds, page.id)}
                </div>
            </div>
        `;
        container.appendChild(pageCard);
        
        // Add event listener to page checkbox
        const pageCheckbox = pageCard.querySelector('.page-checkbox');
        const featuresContainer = pageCard.querySelector('.features-container');
        
        pageCheckbox.addEventListener('change', function() {
            if (this.checked) {
                featuresContainer.classList.remove('hidden');
                // Check all features
                document.querySelectorAll(`#features-${page.id} input[type="checkbox"]`).forEach(cb => {
                    cb.checked = true;
                });
            } else {
                featuresContainer.classList.add('hidden');
                // Uncheck all features
                document.querySelectorAll(`#features-${page.id} input[type="checkbox"]`).forEach(cb => {
                    cb.checked = false;
                });
            }
        });
    });
}

function renderFeatures(features, assignedFeatureIds, pageId) {
    if (features.length === 0) {
        return '<div class="text-gray-400 text-sm">No features available for this page.</div>';
    }
    
    return features.map(feature => `
        <label class="flex items-center space-x-2 p-1 hover:bg-gray-50 rounded cursor-pointer">
            <input type="checkbox" class="feature-checkbox rounded border-gray-300 text-blue-600" 
                   data-page-id="${feature.page_id}" 
                   data-feature-id="${feature.id}"
                   ${assignedFeatureIds.includes(feature.id) ? 'checked' : ''}>
            <span class="text-sm text-gray-700">${feature.display_name}</span>
        </label>
    `).join('');
}

// Save button
document.getElementById('saveButton').addEventListener('click', function() {
    if (!currentRoleId) {
        alert('Please select a role first');
        return;
    }
    
    const assignments = [];
    
    // Collect all checked features
    document.querySelectorAll('.feature-checkbox:checked').forEach(checkbox => {
        assignments.push({
            page_id: parseInt(checkbox.dataset.pageId),
            feature_id: parseInt(checkbox.dataset.featureId)
        });
    });
    
    fetch('/page-assignment/save', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            role_id: currentRoleId,
            assignments: assignments
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Assignments saved successfully!');
            // Reload the page to refresh
            location.reload();
        } else {
            alert('Error saving assignments: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving assignments: ' + error.message);
    });
});
</script>
@endsection
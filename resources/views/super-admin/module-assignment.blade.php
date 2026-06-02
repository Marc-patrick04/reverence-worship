@extends('layouts.app')

@section('title', 'Module Assignment')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Module Assignment</h1>
            <p class="text-gray-600 mt-1">Assign worship team modules to administrators</p>
        </div>
    </div>
    
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- User Selection -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Select User</h3>
            <select id="userSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select a user</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
            
            <div id="userModules" class="mt-6 hidden">
                <h4 class="font-semibold text-gray-700 mb-2">Currently Assigned Modules:</h4>
                <div id="assignedModulesList" class="flex flex-wrap gap-2 mb-4"></div>
            </div>
        </div>
        
        <!-- Module Assignment Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Assign Modules</h3>
            <form id="moduleForm" method="POST" action="{{ route('module-assignment.assign') }}">
                @csrf
                <input type="hidden" name="user_id" id="userId" value="">
                
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <label class="flex items-center space-x-2 p-2 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="modules[]" value="music-view" class="module-checkbox rounded">
                        <span><i class="fas fa-music text-blue-600 mr-1"></i> Music & Evangelism</span>
                    </label>
                    
                    <label class="flex items-center space-x-2 p-2 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="modules[]" value="intercession-view" class="module-checkbox rounded">
                        <span><i class="fas fa-pray text-blue-600 mr-1"></i> Intercession & Growth</span>
                    </label>
                    
                    <label class="flex items-center space-x-2 p-2 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="modules[]" value="fellowship-view" class="module-checkbox rounded">
                        <span><i class="fas fa-users text-blue-600 mr-1"></i> Social Fellowship</span>
                    </label>
                    
                    <label class="flex items-center space-x-2 p-2 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="modules[]" value="discipline-view" class="module-checkbox rounded">
                        <span><i class="fas fa-gavel text-blue-600 mr-1"></i> Discipline</span>
                    </label>
                    
                    <label class="flex items-center space-x-2 p-2 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="modules[]" value="finance-view" class="module-checkbox rounded">
                        <span><i class="fas fa-coins text-blue-600 mr-1"></i> Financial</span>
                    </label>
                    
                    <label class="flex items-center space-x-2 p-2 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="modules[]" value="announcements-view" class="module-checkbox rounded">
                        <span><i class="fas fa-bullhorn text-blue-600 mr-1"></i> Announcements</span>
                    </label>
                    
                    <label class="flex items-center space-x-2 p-2 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="modules[]" value="reports-view" class="module-checkbox rounded">
                        <span><i class="fas fa-chart-bar text-blue-600 mr-1"></i> Reports</span>
                    </label>
                    
                    <label class="flex items-center space-x-2 p-2 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="modules[]" value="chats-view" class="module-checkbox rounded">
                        <span><i class="fas fa-comments text-blue-600 mr-1"></i> Chats</span>
                    </label>
                </div>
                
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>
                    Save Module Assignments
                </button>
            </form>
        </div>
    </div>
</div>

<script>
const userSelect = document.getElementById('userSelect');
const userIdInput = document.getElementById('userId');
const userModulesDiv = document.getElementById('userModules');
const assignedModulesList = document.getElementById('assignedModulesList');
const checkboxes = document.querySelectorAll('.module-checkbox');

// Load user's assigned modules when selected
userSelect.addEventListener('change', function() {
    const userId = this.value;
    if (userId) {
        userIdInput.value = userId;
        userModulesDiv.classList.remove('hidden');
        
        // Fetch assigned modules
        fetch(`/module-assignment/user/${userId}/modules`)
            .then(response => response.json())
            .then(data => {
                // Clear previous
                assignedModulesList.innerHTML = '';
                
                if (data.length === 0) {
                    assignedModulesList.innerHTML = '<span class="text-gray-500">No modules assigned</span>';
                } else {
                    data.forEach(module => {
                        let moduleName = module.replace('-view', '').replace(/-/g, ' ');
                        moduleName = moduleName.charAt(0).toUpperCase() + moduleName.slice(1);
                        let icon = 'fa-tag';
                        if (module.includes('music')) icon = 'fa-music';
                        else if (module.includes('intercession')) icon = 'fa-pray';
                        else if (module.includes('fellowship')) icon = 'fa-users';
                        else if (module.includes('discipline')) icon = 'fa-gavel';
                        else if (module.includes('finance')) icon = 'fa-coins';
                        else if (module.includes('announcements')) icon = 'fa-bullhorn';
                        else if (module.includes('reports')) icon = 'fa-chart-bar';
                        else if (module.includes('chats')) icon = 'fa-comments';
                        
                        assignedModulesList.innerHTML += `
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs flex items-center">
                                <i class="fas ${icon} mr-1"></i> ${moduleName}
                            </span>
                        `;
                    });
                }
                
                // Check checkboxes based on assigned modules
                checkboxes.forEach(checkbox => {
                    checkbox.checked = data.includes(checkbox.value);
                });
            })
            .catch(error => {
                console.error('Error:', error);
            });
    } else {
        userModulesDiv.classList.add('hidden');
        userIdInput.value = '';
    }
});
</script>
@endsection
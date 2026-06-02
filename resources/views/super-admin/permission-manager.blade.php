@extends('layouts.app')

@section('title', 'Permission Manager')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Permission Manager</h1>
            <p class="text-gray-600 mt-1">Create and manage pages and their features (permissions)</p>
        </div>
        <button onclick="openCreatePageModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition flex items-center">
            <i class="fas fa-plus-circle mr-2"></i>
            Create New Page
        </button>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pages List -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Pages (Modules)</h3>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach($pages as $page)
                <div class="border rounded-lg p-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center space-x-2">
                                <i class="fas {{ $page->icon }} text-blue-600"></i>
                                <h4 class="font-bold text-gray-800">{{ $page->display_name }}</h4>
                                @if(!$page->is_active)
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 mt-1">{{ $page->name }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="fas fa-tag mr-1"></i> {{ $page->features->count() }} features
                                <span class="mx-2">|</span>
                                <i class="fas fa-sort-numeric-down mr-1"></i> Order: {{ $page->sort_order }}
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="openEditPageModal({{ $page->id }}, '{{ $page->name }}', '{{ $page->display_name }}', '{{ $page->icon }}', '{{ $page->route }}', {{ $page->sort_order }}, {{ $page->is_active ? 'true' : 'false' }})" 
                                    class="text-blue-600 hover:text-blue-800" title="Edit Page">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="openCreateFeatureModal({{ $page->id }}, '{{ $page->display_name }}')" 
                                    class="text-green-600 hover:text-green-800" title="Add Feature">
                                <i class="fas fa-plus-circle"></i>
                            </button>
                            <button onclick="deletePage({{ $page->id }})" 
                                    class="text-red-600 hover:text-red-800" title="Delete Page">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Features under this page -->
                    <div class="mt-3 ml-6 pl-3 border-l-2 border-gray-200">
                        <p class="text-xs font-semibold text-gray-500 mb-2">FEATURES:</p>
                        <div class="space-y-1">
                            @foreach($features->where('page_id', $page->id) as $feature)
                            <div class="flex justify-between items-center text-sm">
                                <div>
                                    <span class="text-gray-700">{{ $feature->display_name }}</span>
                                    <span class="text-xs text-gray-400 ml-2">({{ $feature->name }})</span>
                                    @if($feature->description)
                                        <p class="text-xs text-gray-400">{{ $feature->description }}</p>
                                    @endif
                                </div>
                                <div class="flex space-x-1">
                                    <button onclick="openEditFeatureModal({{ $feature->id }}, {{ $feature->page_id }}, '{{ $feature->name }}', '{{ $feature->display_name }}', '{{ addslashes($feature->description) }}')" 
                                            class="text-blue-600 hover:text-blue-800 text-xs" title="Edit Feature">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteFeature({{ $feature->id }})" 
                                            class="text-red-600 hover:text-red-800 text-xs" title="Delete Feature">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Quick Guide -->
        <div class="bg-gradient-to-r from-blue-900 to-black rounded-lg shadow-lg p-6 text-white">
            <h3 class="text-lg font-bold mb-4">How Permissions Work</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <i class="fas fa-file-alt mr-2 text-blue-300"></i>
                    <strong>Pages</strong> - Main modules (e.g., User Management, Music Ministry)
                </div>
                <div>
                    <i class="fas fa-tag mr-2 text-green-300"></i>
                    <strong>Features</strong> - Specific actions within a page (e.g., View, Create, Edit, Delete)
                </div>
                <div class="border-t border-blue-700 my-3 pt-3">
                    <i class="fas fa-lightbulb mr-2 text-yellow-300"></i>
                    <strong>Naming Convention:</strong>
                </div>
                <ul class="list-disc list-inside space-y-1 ml-2 text-gray-300">
                    <li>View features: <code class="bg-blue-800 px-1 rounded">view-users</code>, <code class="bg-blue-800 px-1 rounded">view-songs</code></li>
                    <li>Create features: <code class="bg-blue-800 px-1 rounded">create-users</code>, <code class="bg-blue-800 px-1 rounded">create-playlists</code></li>
                    <li>Edit features: <code class="bg-blue-800 px-1 rounded">edit-users</code>, <code class="bg-blue-800 px-1 rounded">edit-songs</code></li>
                    <li>Delete features: <code class="bg-blue-800 px-1 rounded">delete-users</code>, <code class="bg-blue-800 px-1 rounded">delete-gallery</code></li>
                    <li>Manage features: <code class="bg-blue-800 px-1 rounded">manage-playlists</code>, <code class="bg-blue-800 px-1 rounded">manage-groups</code></li>
                </ul>
                <div class="border-t border-blue-700 my-3 pt-3">
                    <i class="fas fa-arrow-right mr-2 text-blue-300"></i>
                    Then go to <strong>Page Assignment</strong> to assign these permissions to roles!
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Page Modal -->
<div id="createPageModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800">Create New Page</h3>
            <button onclick="closeModal('createPageModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('permission-manager.page.store') }}">
            @csrf
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Page Name (slug) *</label>
                    <input type="text" name="name" required placeholder="user-management" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Use lowercase with hyphens</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Name *</label>
                    <input type="text" name="display_name" required placeholder="User Management" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Font Awesome) *</label>
                    <input type="text" name="icon" required placeholder="fa-users" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">e.g., fa-users, fa-music, fa-cog</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Route Name</label>
                    <input type="text" name="route" placeholder="admin.users.index" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="999" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('createPageModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Create Page</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Page Modal -->
<div id="editPageModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800">Edit Page</h3>
            <button onclick="closeModal('editPageModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="" id="editPageForm">
            @csrf
            @method('PUT')
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Page Name (slug) *</label>
                    <input type="text" name="name" id="edit_page_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Name *</label>
                    <input type="text" name="display_name" id="edit_page_display_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Font Awesome) *</label>
                    <input type="text" name="icon" id="edit_page_icon" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Route Name</label>
                    <input type="text" name="route" id="edit_page_route" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="edit_page_sort_order" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="is_active" id="edit_page_is_active" value="1" class="rounded">
                        <span class="text-sm text-gray-700">Active (visible in menu)</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('editPageModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Update Page</button>
            </div>
        </form>
    </div>
</div>

<!-- Create Feature Modal -->
<div id="createFeatureModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800">Add Feature to <span id="featurePageName"></span></h3>
            <button onclick="closeModal('createFeatureModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('permission-manager.feature.store') }}">
            @csrf
            <input type="hidden" name="page_id" id="feature_page_id">
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Feature Name (slug) *</label>
                    <input type="text" name="name" required placeholder="view-users" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Use: view-, create-, edit-, delete-, manage-</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Name *</label>
                    <input type="text" name="display_name" required placeholder="View Users" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="What this permission allows"></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('createFeatureModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Add Feature</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Feature Modal -->
<div id="editFeatureModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800">Edit Feature</h3>
            <button onclick="closeModal('editFeatureModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="" id="editFeatureForm">
            @csrf
            @method('PUT')
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Feature Name (slug) *</label>
                    <input type="text" name="name" id="edit_feature_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Name *</label>
                    <input type="text" name="display_name" id="edit_feature_display_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="edit_feature_description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('editFeatureModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Update Feature</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreatePageModal() {
    document.getElementById('createPageModal').classList.remove('hidden');
}

function openEditPageModal(id, name, displayName, icon, route, sortOrder, isActive) {
    document.getElementById('editPageForm').action = `/permission-manager/page/${id}`;
    document.getElementById('edit_page_name').value = name;
    document.getElementById('edit_page_display_name').value = displayName;
    document.getElementById('edit_page_icon').value = icon;
    document.getElementById('edit_page_route').value = route || '';
    document.getElementById('edit_page_sort_order').value = sortOrder;
    document.getElementById('edit_page_is_active').checked = isActive;
    document.getElementById('editPageModal').classList.remove('hidden');
}

function openCreateFeatureModal(pageId, pageName) {
    document.getElementById('feature_page_id').value = pageId;
    document.getElementById('featurePageName').innerText = pageName;
    document.getElementById('createFeatureModal').classList.remove('hidden');
}

function openEditFeatureModal(id, pageId, name, displayName, description) {
    document.getElementById('editFeatureForm').action = `/permission-manager/feature/${id}`;
    document.getElementById('edit_feature_name').value = name;
    document.getElementById('edit_feature_display_name').value = displayName;
    document.getElementById('edit_feature_description').value = description;
    document.getElementById('editFeatureModal').classList.remove('hidden');
}

function deletePage(id) {
    if (confirm('Delete this page? All features under it will also need to be deleted first.')) {
        window.location.href = `/permission-manager/page/${id}/delete`;
    }
}

function deleteFeature(id) {
    if (confirm('Delete this feature?')) {
        window.location.href = `/permission-manager/feature/${id}/delete`;
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
}
</script>

<style>
.modal { display: none; }
.modal:not(.hidden) { display: block !important; }
</style>
@endsection
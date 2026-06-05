<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Landing Page Content Manager</h3>
        <button onclick="openCreatePostModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus-circle mr-2"></i> Add New Item
        </button>
    </div>
    
    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 mb-4">
        <nav class="flex space-x-4">
            <button onclick="switchTab('youtube')" id="tabYoutube" class="py-2 px-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                <i class="fab fa-youtube mr-1"></i> YouTube Videos
            </button>
            <button onclick="switchTab('featured')" id="tabFeatured" class="py-2 px-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                <i class="fas fa-image mr-1"></i> Featured Images
            </button>
        </nav>
    </div>
    
    <!-- YouTube Videos Tab -->
    <div id="youtubeTab" class="tab-content">
        <div class="flex justify-between items-center mb-3">
            <h4 class="font-semibold text-gray-700">YouTube Videos</h4>
            <button onclick="openYouTubeModal()" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-xs">
                <i class="fab fa-youtube mr-1"></i> Add YouTube Video
            </button>
        </div>
        
        <div id="youtubeList" class="space-y-2 max-h-96 overflow-y-auto">
            @forelse($youtubeVideos ?? [] as $video)
            <div class="youtube-item border rounded-lg p-3 hover:bg-gray-50 transition cursor-move" data-id="{{ $video->id }}" data-order="{{ $video->sort_order }}">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-grip-vertical text-gray-300 cursor-move"></i>
                            <h5 class="font-medium text-gray-800">{{ $video->title }}</h5>
                            @if($video->is_published)
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Published</span>
                            @else
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Draft</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 mt-1 text-xs text-gray-500">
                            <span><i class="fab fa-youtube"></i> YouTube ID: {{ $video->youtube_id }}</span>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="togglePublish({{ $video->id }}, 'youtube')" class="text-green-600 hover:text-green-800" title="Publish/Unpublish">
                            <i class="fas fa-globe"></i>
                        </button>
                        <button onclick="editYouTube({{ $video->id }})" class="text-blue-600 hover:text-blue-800" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteItem({{ $video->id }}, 'youtube')" class="text-red-600 hover:text-red-800" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-gray-500 py-8">
                <i class="fab fa-youtube fa-3x mb-2 text-gray-300"></i>
                <p>No YouTube videos added yet</p>
                <button onclick="openYouTubeModal()" class="mt-2 text-red-600 text-sm hover:underline">
                    Add your first video
                </button>
            </div>
            @endforelse
        </div>
    </div>
    
    <!-- Featured Images Tab -->
    <div id="featuredTab" class="tab-content hidden">
        <div class="flex justify-between items-center mb-3">
            <h4 class="font-semibold text-gray-700">Featured Images</h4>
            <button onclick="openFeaturedImageModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded-lg text-xs">
                <i class="fas fa-upload mr-1"></i> Upload Image
            </button>
        </div>
        
        <div id="featuredList" class="space-y-2 max-h-96 overflow-y-auto">
            @forelse($featuredImages ?? [] as $image)
            <div class="featured-item border rounded-lg p-3 hover:bg-gray-50 transition cursor-move" data-id="{{ $image->id }}" data-order="{{ $image->sort_order }}">
                <div class="flex justify-between items-start">
                    <div class="flex-1 flex items-center gap-3">
                        <i class="fas fa-grip-vertical text-gray-300 cursor-move"></i>
                        <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                            <img src="{{ asset($image->image_path) }}" alt="{{ $image->title }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h5 class="font-medium text-gray-800">{{ $image->title }}</h5>
                                @if($image->is_published)
                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Published</span>
                                @else
                                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Draft</span>
                                @endif
                            </div>
                            @if($image->description)
                                <p class="text-xs text-gray-500 mt-1">{{ Str::limit($image->description, 60) }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="togglePublish({{ $image->id }}, 'featured')" class="text-green-600 hover:text-green-800" title="Publish/Unpublish">
                            <i class="fas fa-globe"></i>
                        </button>
                        <button onclick="editFeaturedImage({{ $image->id }})" class="text-blue-600 hover:text-blue-800" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteItem({{ $image->id }}, 'featured')" class="text-red-600 hover:text-red-800" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-gray-500 py-8">
                <i class="fas fa-image fa-3x mb-2 text-gray-300"></i>
                <p>No featured images added yet</p>
                <button onclick="openFeaturedImageModal()" class="mt-2 text-purple-600 text-sm hover:underline">
                    Upload your first image
                </button>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Add/Edit YouTube Modal -->
<div id="youTubeModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="youTubeModalTitle" class="text-xl font-bold text-gray-800">Add YouTube Video</h3>
            <button onclick="closeModal('youTubeModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="youTubeForm" method="POST">
            @csrf
            <input type="hidden" id="youtube_id" name="id">
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" id="youtube_title" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">YouTube Video ID *</label>
                    <input type="text" id="youtube_video_id" name="youtube_id" required placeholder="e.g., dQw4w9WgXcQ" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">The part after v= in the YouTube URL</p>
                </div>
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" id="youtube_published" name="is_published" value="1" class="rounded border-gray-300 text-blue-600">
                        <span class="text-sm text-gray-700">Publish on landing page</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('youTubeModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg">Save Video</button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Featured Image Modal -->
<div id="featuredImageModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="featuredImageModalTitle" class="text-xl font-bold text-gray-800">Add Featured Image</h3>
            <button onclick="closeModal('featuredImageModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="featuredImageForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="featured_id" name="id">
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" id="featured_title" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image *</label>
                    <input type="file" id="featured_image" name="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <div id="currentImagePreview" class="mt-2 hidden">
                        <img id="imagePreview" src="" class="w-32 h-32 object-cover rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="featured_description" name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" id="featured_published" name="is_published" value="1" class="rounded border-gray-300 text-blue-600">
                        <span class="text-sm text-gray-700">Publish on landing page</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('featuredImageModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg">Save Image</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script>
let currentTab = 'youtube';
let youtubeSortable, featuredSortable;

// Tab switching
function switchTab(tab) {
    currentTab = tab;
    
    const youtubeTab = document.getElementById('youtubeTab');
    const featuredTab = document.getElementById('featuredTab');
    const tabYoutube = document.getElementById('tabYoutube');
    const tabFeatured = document.getElementById('tabFeatured');
    
    if (tab === 'youtube') {
        youtubeTab.classList.remove('hidden');
        featuredTab.classList.add('hidden');
        tabYoutube.classList.add('text-blue-600', 'border-blue-600');
        tabYoutube.classList.remove('text-gray-500', 'border-transparent');
        tabFeatured.classList.remove('text-blue-600', 'border-blue-600');
        tabFeatured.classList.add('text-gray-500', 'border-transparent');
    } else {
        youtubeTab.classList.add('hidden');
        featuredTab.classList.remove('hidden');
        tabFeatured.classList.add('text-blue-600', 'border-blue-600');
        tabFeatured.classList.remove('text-gray-500', 'border-transparent');
        tabYoutube.classList.remove('text-blue-600', 'border-blue-600');
        tabYoutube.classList.add('text-gray-500', 'border-transparent');
    }
}

// Initialize drag and drop
function initDragDrop() {
    const youtubeList = document.getElementById('youtubeList');
    const featuredList = document.getElementById('featuredList');
    
    if (youtubeList) {
        youtubeSortable = new Sortable(youtubeList, {
            animation: 150,
            handle: '.cursor-move',
            onEnd: function() {
                updateOrder('youtube');
            }
        });
    }
    
    if (featuredList) {
        featuredSortable = new Sortable(featuredList, {
            animation: 150,
            handle: '.cursor-move',
            onEnd: function() {
                updateOrder('featured');
            }
        });
    }
}

// Update order after drag and drop
function updateOrder(type) {
    const items = document.querySelectorAll(`#${type}List .${type}-item`);
    const orders = [];
    
    items.forEach((item, index) => {
        const id = item.dataset.id;
        orders.push({
            id: id,
            sort_order: index + 1
        });
    });
    
    fetch(`/music/landing/update-order`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            type: type,
            orders: orders
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Error updating order:', data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

// Toggle publish status
function togglePublish(id, type) {
    fetch(`/music/landing/${type}/${id}/toggle-publish`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating publish status');
    });
}

// Delete item
function deleteItem(id, type) {
    if (confirm('Are you sure you want to delete this item?')) {
        fetch(`/music/landing/${type}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting item');
        });
    }
}

// YouTube modal functions
function openYouTubeModal() {
    document.getElementById('youTubeModalTitle').textContent = 'Add YouTube Video';
    document.getElementById('youtube_id').value = '';
    document.getElementById('youtube_title').value = '';
    document.getElementById('youtube_video_id').value = '';
    document.getElementById('youtube_published').checked = true;
    document.getElementById('youTubeModal').classList.remove('hidden');
}

function editYouTube(id) {
    fetch(`/music/landing/youtube/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('youTubeModalTitle').textContent = 'Edit YouTube Video';
                document.getElementById('youtube_id').value = data.video.id;
                document.getElementById('youtube_title').value = data.video.title;
                document.getElementById('youtube_video_id').value = data.video.youtube_id;
                document.getElementById('youtube_published').checked = data.video.is_published == 1;
                document.getElementById('youTubeModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading video details');
        });
}

// Featured image modal functions
function openFeaturedImageModal() {
    document.getElementById('featuredImageModalTitle').textContent = 'Add Featured Image';
    document.getElementById('featured_id').value = '';
    document.getElementById('featured_title').value = '';
    document.getElementById('featured_description').value = '';
    document.getElementById('featured_published').checked = true;
    document.getElementById('featured_image').value = '';
    document.getElementById('currentImagePreview').classList.add('hidden');
    document.getElementById('featuredImageModal').classList.remove('hidden');
}

function editFeaturedImage(id) {
    fetch(`/music/landing/featured/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('featuredImageModalTitle').textContent = 'Edit Featured Image';
                document.getElementById('featured_id').value = data.image.id;
                document.getElementById('featured_title').value = data.image.title;
                document.getElementById('featured_description').value = data.image.description || '';
                document.getElementById('featured_published').checked = data.image.is_published == 1;
                
                if (data.image.image_path) {
                    document.getElementById('imagePreview').src = '/' + data.image.image_path;
                    document.getElementById('currentImagePreview').classList.remove('hidden');
                }
                
                document.getElementById('featuredImageModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading image details');
        });
}

// Form submissions
document.getElementById('youTubeForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const id = document.getElementById('youtube_id').value;
    const url = id ? `/music/landing/youtube/${id}` : '/music/landing/youtube';
    const method = id ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            title: formData.get('title'),
            youtube_id: formData.get('youtube_id'),
            is_published: formData.get('is_published') ? true : false
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('youTubeModal');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving video');
    });
});

document.getElementById('featuredImageForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const id = document.getElementById('featured_id').value;
    const url = id ? `/music/landing/featured/${id}` : '/music/landing/featured';
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('featuredImageModal');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving image');
    });
});

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    initDragDrop();
});
</script>
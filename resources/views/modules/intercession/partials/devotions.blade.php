<div class="bg-white rounded-xl shadow-md p-6">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Daily Devotion</h2>
            <p class="text-gray-500 text-sm mt-1">Manage daily bible verses and devotion content</p>
        </div>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->canAccess('intercession', 'create-devotions'))
        <button onclick="openDevotionModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-plus"></i> Add Devotion
        </button>
        @endif
    </div>

    <!-- Bible Verses Section -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-bible text-blue-600"></i>
                Bible Verses
            </h3>
            <span class="text-sm text-gray-400">{{ $allDevotions ? count($allDevotions) : 0 }} verses</span>
        </div>
        
        <!-- Comments Section -->
        <div class="bg-gray-50 rounded-lg p-3 mb-4 flex items-center justify-between cursor-pointer hover:bg-gray-100 transition" onclick="toggleComments()">
            <div class="flex items-center gap-2">
                <i class="fas fa-comments text-gray-400"></i>
                <span class="text-sm text-gray-600">Comments</span>
                <span class="text-xs bg-gray-200 px-2 py-0.5 rounded-full" id="commentsCount">0</span>
            </div>
            <i class="fas fa-chevron-down text-gray-400 text-sm transition-transform" id="commentsChevron"></i>
        </div>
        
        <!-- Comments Content (Hidden by default) -->
        <div id="commentsContent" class="hidden mt-3 space-y-3">
            <div class="bg-white border rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-blue-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-gray-800 text-sm">John Doe</p>
                                <p class="text-xs text-gray-400">2 hours ago</p>
                            </div>
                            <button class="text-gray-400 hover:text-blue-600 text-xs">
                                <i class="fas fa-reply"></i> Reply
                            </button>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">This is a beautiful verse! Really touched my heart today.</p>
                    </div>
                </div>
            </div>
            
            <!-- Add Comment Form -->
            <div class="bg-gray-50 rounded-lg p-4">
                <textarea id="commentInput" rows="2" placeholder="Share your thoughts about this devotion..." 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm"></textarea>
                <div class="flex justify-end mt-2">
                    <button onclick="submitComment()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm">
                        Post Comment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- All Devotions List -->
    <div class="space-y-3">
        @forelse($allDevotions ?? [] as $devotion)
        <div class="border rounded-lg p-4 hover:shadow-md transition-all duration-300">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <h4 class="font-semibold text-gray-800">{{ $devotion->title }}</h4>
                        @if(isset($devotion->completed_by_user) && $devotion->completed_by_user)
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                <i class="fas fa-check-circle"></i> Read
                            </span>
                        @endif
                        @if(\Carbon\Carbon::parse($devotion->date)->isToday())
                            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">
                                <i class="fas fa-star"></i> Today
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mb-2">
                        <i class="fas fa-calendar mr-1"></i> 
                        {{ \Carbon\Carbon::parse($devotion->date)->format('l, F j, Y') }}
                    </p>
                    @if($devotion->bible_verse)
                        <p class="text-sm text-blue-600 italic">"{{ Str::limit($devotion->bible_verse, 100) }}"</p>
                    @endif
                    <p class="text-sm text-gray-600 mt-2">{{ Str::limit($devotion->content, 150) }}</p>
                </div>
                <div class="ml-4 flex gap-2">
                    <a href="{{ route('intercession.devotion.show', $devotion->id) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm">
                        Read More <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->canAccess('intercession', 'edit-devotions'))
                    <button onclick="editDevotion({{ $devotion->id }})" class="text-gray-400 hover:text-blue-600">
                        <i class="fas fa-edit"></i>
                    </button>
                    @endif
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->canAccess('intercession', 'delete-devotions'))
                    <button onclick="deleteDevotion({{ $devotion->id }})" class="text-gray-400 hover:text-red-600">
                        <i class="fas fa-trash"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-bible text-3xl text-gray-400"></i>
            </div>
            <p class="text-gray-500">No bible verses added yet.</p>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->canAccess('intercession', 'create-devotions'))
            <button onclick="openDevotionModal()" class="mt-3 text-blue-600 hover:text-blue-800 text-sm">
                <i class="fas fa-plus"></i> Add your first devotion
            </button>
            @endif
        </div>
        @endforelse
    </div>
</div>

<!-- Devotion Modal for Add/Edit -->
<div id="devotionModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white mb-10">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="devotionModalTitle" class="text-xl font-bold text-gray-800">Add Devotion Content</h3>
            <button onclick="closeModal('devotionModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="devotion-form" method="POST">
            @csrf
            <input type="hidden" id="devotion_id" name="devotion_id">
            <input type="hidden" id="form_method" name="_method" value="POST">
            
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" id="devotion_title" name="title" required 
                           placeholder="e.g., Walking in Faith"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                    <input type="date" id="devotion_date" name="date" required 
                           value="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bible Verse</label>
                    <input type="text" id="devotion_verse" name="bible_verse" 
                           placeholder="e.g., John 3:16 - For God so loved the world..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Text (English) <span class="text-red-500">*</span></label>
                    <textarea id="devotion_content" name="content" rows="6" required 
                              placeholder="Write the devotion content in English..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Text (Kinyarwanda)</label>
                    <textarea id="devotion_content_rw" name="content_rw" rows="6" 
                              placeholder="Andika ibiri mu mutima wawe mu Kinyarwanda..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="devotion_active" name="is_active" value="1" class="w-4 h-4 text-blue-600 rounded" checked>
                    <label class="text-sm text-gray-700">Active</label>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-6 pt-3 border-t">
                <button type="button" onclick="closeModal('devotionModal')" class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                    Save Devotion
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let commentsOpen = false;

function toggleComments() {
    commentsOpen = !commentsOpen;
    const commentsContent = document.getElementById('commentsContent');
    const chevron = document.getElementById('commentsChevron');
    
    if (commentsOpen) {
        commentsContent.classList.remove('hidden');
        chevron.style.transform = 'rotate(180deg)';
    } else {
        commentsContent.classList.add('hidden');
        chevron.style.transform = 'rotate(0deg)';
    }
}

function submitComment() {
    const commentInput = document.getElementById('commentInput');
    const comment = commentInput.value.trim();
    
    if (!comment) {
        alert('Please enter a comment');
        return;
    }
    
    showNotification('Comment posted successfully!', 'success');
    commentInput.value = '';
}

function openDevotionModal() {
    document.getElementById('devotionModalTitle').textContent = 'Add Devotion Content';
    document.getElementById('devotion_id').value = '';
    document.getElementById('form_method').value = 'POST';
    document.getElementById('devotion_title').value = '';
    document.getElementById('devotion_date').value = '{{ date("Y-m-d") }}';
    document.getElementById('devotion_verse').value = '';
    document.getElementById('devotion_content').value = '';
    document.getElementById('devotion_content_rw').value = '';
    document.getElementById('devotion_active').checked = true;
    document.getElementById('devotionModal').classList.remove('hidden');
}

function editDevotion(id) {
    fetch(`/intercession/devotions/${id}/edit`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('devotionModalTitle').textContent = 'Edit Devotion Content';
            document.getElementById('devotion_id').value = data.devotion.id;
            document.getElementById('form_method').value = 'PUT';
            document.getElementById('devotion_title').value = data.devotion.title;
            document.getElementById('devotion_date').value = data.devotion.date;
            document.getElementById('devotion_verse').value = data.devotion.bible_verse || '';
            document.getElementById('devotion_content').value = data.devotion.content;
            document.getElementById('devotion_content_rw').value = data.devotion.content_rw || '';
            document.getElementById('devotion_active').checked = data.devotion.is_active == 1;
            document.getElementById('devotionModal').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading devotion data');
    });
}

function deleteDevotion(id) {
    if (confirm('Are you sure you want to delete this devotion?')) {
        fetch(`/intercession/devotions/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting devotion');
            }
        });
    }
}

document.getElementById('devotion-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const devotionId = document.getElementById('devotion_id').value;
    const method = document.getElementById('form_method').value;
    
    let url = '{{ route("intercession.devotions.store") }}';
    if (method === 'PUT' && devotionId) {
        url = `/intercession/devotions/${devotionId}`;
        formData.append('_method', 'PUT');
    }
    
    fetch(url, {
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
            closeModal('devotionModal');
            showNotification('Devotion saved successfully!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving devotion');
    });
});

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg text-white z-50 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.innerHTML = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}
</script>

<style>
.modal {
    display: none;
}
.modal:not(.hidden) {
    display: block !important;
}
#commentsChevron {
    transition: transform 0.3s ease;
}
</style>
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Public Board</h3>
        @if(auth()->user()->canAccess('music-ministry', 'manage-board'))
        <button onclick="openCreatePostModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus-circle mr-2"></i> New Post
        </button>
        @endif
    </div>
    
    <div class="space-y-4">
        @forelse($posts ?? [] as $post)
        <div class="border rounded-lg p-4 {{ $post->is_pinned ? 'bg-yellow-50 border-yellow-400' : 'hover:bg-gray-50' }}">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center space-x-2">
                        @if($post->is_pinned)
                            <i class="fas fa-thumbtack text-yellow-600"></i>
                        @endif
                        <h4 class="font-bold text-gray-800">{{ $post->title }}</h4>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">{{ $post->content }}</p>
                    <div class="flex items-center space-x-4 mt-3 text-xs text-gray-500">
                        <span><i class="fas fa-user mr-1"></i> {{ $post->creator->name ?? 'Unknown' }}</span>
                        <span><i class="fas fa-calendar mr-1"></i> {{ date('M d, Y', strtotime($post->created_at)) }}</span>
                    </div>
                </div>
                @if(auth()->user()->canAccess('music-ministry', 'manage-board'))
                <div class="flex space-x-2">
                    <button onclick="togglePin({{ $post->id }})" class="text-yellow-600 hover:text-yellow-800" title="Pin/Unpin">
                        <i class="fas fa-thumbtack"></i>
                    </button>
                    <button onclick="deletePost({{ $post->id }})" class="text-red-600 hover:text-red-800" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center text-gray-500 py-8">
            <i class="fas fa-bullhorn fa-3x mb-3 text-gray-300"></i>
            <p>No announcements yet</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Create Post Modal -->
<div id="createPostModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800">Create Announcement</h3>
            <button onclick="closeModal('createPostModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('music.board.store') }}">
            @csrf
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Content *</label>
                    <textarea name="content" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="is_pinned" value="1" class="rounded border-gray-300 text-blue-600">
                        <span class="text-sm text-gray-700">Pin this announcement (appears at top)</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('createPostModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Post</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreatePostModal() {
    document.getElementById('createPostModal').classList.remove('hidden');
}

function togglePin(id) {
    fetch(`/music/board/${id}/toggle-pin`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    }).then(() => location.reload());
}

function deletePost(id) {
    if (confirm('Delete this announcement?')) {
        fetch(`/music/board/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => location.reload());
    }
}
</script>
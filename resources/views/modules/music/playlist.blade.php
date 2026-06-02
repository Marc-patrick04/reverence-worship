@props([
    'canViewSongs' => false,
    'canAddSongs' => false,
    'canEditSongs' => false,
    'canDeleteSongs' => false,
    'canViewPlaylists' => false,
    'canAddPlaylists' => false,
    'canEditPlaylists' => false,
    'canDeletePlaylists' => false
])

<div class="bg-white rounded-lg shadow-lg p-6">
    <!-- Add to Playlist Section - MOVED TO TOP -->
    @if($canEditPlaylists && $canViewSongs)
    <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
            <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
            Add Song to Playlist
        </h4>
        <div class="flex flex-col sm:flex-row gap-3">
            <select id="playlistSelect" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select Playlist</option>
                @foreach($playlists ?? [] as $playlist)
                    <option value="{{ $playlist->id }}">{{ $playlist->title }} ({{ $playlist->songs->count() }} songs)</option>
                @endforeach
            </select>
            <select id="songSelect" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select Song</option>
                @foreach($songs ?? [] as $song)
                    <option value="{{ $song->id }}">{{ $song->title }} @if($song->artist)- {{ $song->artist }} @endif</option>
                @endforeach
            </select>
            <button onclick="addToPlaylist()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i> Add to Playlist
            </button>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- PLAYLISTS COLUMN -->
        <div>
            <div class="flex justify-between items-center mb-3 pb-2 border-b">
                <h4 class="font-semibold text-gray-700">
                    <i class="fas fa-list text-blue-600 mr-2"></i>Playlists
                </h4>
                @if($canAddPlaylists)
                <button onclick="openCreatePlaylistModal()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs transition">
                    <i class="fas fa-plus-circle mr-1"></i> New Playlist
                </button>
                @endif
            </div>
            
            <div class="space-y-2 max-h-96 overflow-y-auto">
                @forelse($playlists ?? [] as $playlist)
                    <div class="border rounded-lg p-3 hover:bg-gray-50 transition">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h5 class="font-medium text-gray-800">{{ $playlist->title }}</h5>
                                <p class="text-xs text-gray-500">{{ $playlist->songs->count() }} songs</p>
                                @if($playlist->description)
                                    <p class="text-xs text-gray-400 mt-1">{{ Str::limit($playlist->description, 50) }}</p>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                @if($canViewPlaylists)
                                <button onclick="viewPlaylistSongs({{ $playlist->id }})" class="text-green-600 hover:text-green-800 transition" title="View Songs">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @endif
                                @if($canEditPlaylists)
                                <a href="{{ route('music.playlist.edit', $playlist->id) }}" class="text-blue-600 hover:text-blue-800 transition" title="Edit Playlist">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                @if($canDeletePlaylists)
                                <form action="{{ route('music.playlist.delete', $playlist->id) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Delete playlist \"{{ $playlist->title }}\"?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Delete Playlist">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-list fa-3x mb-2 text-gray-300"></i>
                        <p>No playlists yet</p>
                        @if($canAddPlaylists)
                        <button onclick="openCreatePlaylistModal()" class="mt-2 text-blue-600 text-sm hover:underline">
                            Create your first playlist
                        </button>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- SONGS COLUMN -->
        <div>
            <div class="flex justify-between items-center mb-3 pb-2 border-b">
                <h4 class="font-semibold text-gray-700">
                    <i class="fas fa-music text-green-600 mr-2"></i>Songs
                </h4>
                @if($canAddSongs)
                <button onclick="openCreateSongModal()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-xs transition">
                    <i class="fas fa-plus-circle mr-1"></i> Add Song
                </button>
                @endif
            </div>
            
            <div class="space-y-2 max-h-96 overflow-y-auto">
                @forelse($songs ?? [] as $song)
                    <div class="border rounded-lg p-3 hover:bg-gray-50 transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <h5 class="font-medium text-gray-800">{{ $song->title }}</h5>
                                <div class="flex flex-wrap gap-2 text-xs text-gray-500 mt-1">
                                    @if($song->key_signature)
                                        <span><i class="fas fa-music"></i> Key: {{ $song->key_signature }}</span>
                                    @endif
                                    @if($song->tempo)
                                        <span><i class="fas fa-tachometer-alt"></i> {{ $song->tempo }} BPM</span>
                                    @endif
                                    @if($song->artist)
                                        <span><i class="fas fa-user"></i> {{ $song->artist }}</span>
                                    @endif
                                    @if($song->assigned_singer)
                                        <span><i class="fas fa-microphone"></i> {{ $song->assigned_singer }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                @if($canViewSongs)
                                <button onclick="viewLyrics({{ $song->id }})" class="text-green-600 hover:text-green-800 transition" title="View Lyrics">
                                    <i class="fas fa-file-alt"></i>
                                </button>
                                @endif
                                @if($canEditSongs)
                                <a href="{{ route('music.song.edit', $song->id) }}" class="text-blue-600 hover:text-blue-800 transition" title="Edit Song">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                @if($canDeleteSongs)
                                <form action="{{ route('music.song.delete', $song->id) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Delete song \"{{ $song->title }}\"?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Delete Song">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-music fa-3x mb-2 text-gray-300"></i>
                        <p>No songs yet</p>
                        @if($canAddSongs)
                        <button onclick="openCreateSongModal()" class="mt-2 text-green-600 text-sm hover:underline">
                            Add your first song
                        </button>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Create Playlist Modal -->
<div id="createPlaylistModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800">Create New Playlist</h3>
            <button onclick="closeModal('createPlaylistModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('music.playlist.store') }}" id="createPlaylistForm">
            @csrf
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Playlist Title *</label>
                    <input type="text" name="title" id="playlistTitle" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="playlistDescription" rows="2" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                
                <!-- Add Songs Section with Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Add Songs to Playlist</label>
                    
                    <!-- Search Bar -->
                    <div class="relative mb-3">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        <input type="text" id="songSearchInput" placeholder="Search songs by title, artist, or key..." 
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="border rounded-lg p-3 max-h-64 overflow-y-auto bg-gray-50">
                        <div class="mb-2 pb-2 border-b flex justify-between items-center">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" id="selectAllSongs" class="rounded border-gray-300 text-blue-600">
                                <span class="text-sm font-medium text-gray-700">Select All Songs</span>
                            </label>
                            <span id="selectedCount" class="text-xs text-gray-500">0 selected</span>
                        </div>
                        <div id="songsListContainer" class="space-y-1">
                            @foreach($songs ?? [] as $song)
                            <label class="song-item flex items-center space-x-2 p-2 hover:bg-white rounded cursor-pointer transition" 
                                   data-title="{{ strtolower($song->title) }}" 
                                   data-artist="{{ strtolower($song->artist ?? '') }}"
                                   data-key="{{ strtolower($song->key_signature ?? '') }}">
                                <input type="checkbox" name="songs[]" value="{{ $song->id }}" 
                                       class="song-checkbox rounded border-gray-300 text-blue-600">
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $song->title }}</span>
                                    @if($song->artist)
                                        <span class="text-xs text-gray-500 ml-2">by {{ $song->artist }}</span>
                                    @endif
                                    <div class="text-xs text-gray-400">
                                        @if($song->key_signature)
                                            <span class="mr-2">Key: {{ $song->key_signature }}</span>
                                        @endif
                                        @if($song->tempo)
                                            <span>Tempo: {{ $song->tempo }} BPM</span>
                                        @endif
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @if(($songs ?? [])->isEmpty())
                            <div class="text-center text-gray-500 py-4">
                                <i class="fas fa-music fa-2x mb-2 text-gray-300"></i>
                                <p>No songs available</p>
                                <button type="button" onclick="closeModal('createPlaylistModal'); openCreateSongModal();" 
                                        class="mt-2 text-blue-600 text-sm hover:underline">
                                    Create a song first
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('createPlaylistModal')" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i> Create Playlist
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Create Song Modal -->
<div id="createSongModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800">Add New Song</h3>
            <button onclick="closeModal('createSongModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('music.song.store') }}" id="createSongForm">
            @csrf
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Song Title *</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Artist</label>
                    <input type="text" name="artist" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Key Signature</label>
                    <input type="text" name="key_signature" placeholder="C, G, D, etc" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tempo (BPM)</label>
                    <input type="number" name="tempo" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Singer</label>
                    <input type="text" name="assigned_singer" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">YouTube Link</label>
                    <input type="url" name="youtube_link" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lyrics</label>
                    <textarea name="lyrics" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg font-mono text-sm"></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('createSongModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg">Save Song</button>
            </div>
        </form>
    </div>
</div>

<!-- View Playlist Songs Modal -->
<div id="viewPlaylistModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="viewPlaylistTitle" class="text-xl font-bold text-gray-800">Playlist Songs</h3>
            <button onclick="closeModal('viewPlaylistModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="viewPlaylistContent" class="mt-4 max-h-96 overflow-y-auto"></div>
        <div class="flex justify-end mt-4 pt-3 border-t">
            <button onclick="closeModal('viewPlaylistModal')" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Close</button>
        </div>
    </div>
</div>

<script>
// Song search and select all functionality
let searchInput, songItems, songCheckboxes, selectAllCheckbox, selectedCountSpan;

function initializeSearchAndSelect() {
    searchInput = document.getElementById('songSearchInput');
    songItems = document.querySelectorAll('.song-item');
    songCheckboxes = document.querySelectorAll('.song-checkbox');
    selectAllCheckbox = document.getElementById('selectAllSongs');
    selectedCountSpan = document.getElementById('selectedCount');
    
    // Update selected count
    function updateSelectedCount() {
        const checked = document.querySelectorAll('#songsListContainer .song-checkbox:checked').length;
        if (selectedCountSpan) {
            selectedCountSpan.textContent = checked + ' selected';
        }
        if (selectAllCheckbox) {
            const total = document.querySelectorAll('#songsListContainer .song-checkbox').length;
            if (total > 0) {
                selectAllCheckbox.checked = checked === total;
                selectAllCheckbox.indeterminate = checked > 0 && checked < total;
            }
        }
    }
    
    // Search songs
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            let visibleCount = 0;
            
            songItems.forEach(item => {
                const title = item.dataset.title || '';
                const artist = item.dataset.artist || '';
                const key = item.dataset.key || '';
                
                if (title.includes(searchTerm) || artist.includes(searchTerm) || key.includes(searchTerm)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            let noResultsMsg = document.getElementById('noSearchResults');
            if (visibleCount === 0 && searchTerm !== '') {
                if (!noResultsMsg) {
                    const container = document.getElementById('songsListContainer');
                    const msg = document.createElement('div');
                    msg.id = 'noSearchResults';
                    msg.className = 'text-center text-gray-500 py-4';
                    msg.innerHTML = '<i class="fas fa-search fa-2x mb-2 text-gray-300"></i><p>No songs match your search</p>';
                    container.appendChild(msg);
                }
            } else if (noResultsMsg) {
                noResultsMsg.remove();
            }
        });
    }
    
    // Select all
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const visibleCheckboxes = document.querySelectorAll('#songsListContainer .song-item:not([style*="display: none"]) .song-checkbox');
            visibleCheckboxes.forEach(cb => {
                cb.checked = selectAllCheckbox.checked;
            });
            updateSelectedCount();
        });
    }
    
    // Update count on checkbox change
    songCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectedCount);
    });
    
    updateSelectedCount();
}

// Open Create Playlist Modal
function openCreatePlaylistModal() {
    // Reset form
    const titleInput = document.getElementById('playlistTitle');
    const descInput = document.getElementById('playlistDescription');
    if (titleInput) titleInput.value = '';
    if (descInput) descInput.value = '';
    
    // Reset search
    if (searchInput) searchInput.value = '';
    
    // Reset song visibility
    if (songItems) {
        songItems.forEach(item => {
            item.style.display = '';
        });
    }
    
    // Uncheck all checkboxes
    if (songCheckboxes) {
        songCheckboxes.forEach(cb => {
            cb.checked = false;
        });
    }
    
    // Reset select all
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    }
    
    // Reset selected count
    if (selectedCountSpan) {
        selectedCountSpan.textContent = '0 selected';
    }
    
    // Remove no results message
    const noResultsMsg = document.getElementById('noSearchResults');
    if (noResultsMsg) noResultsMsg.remove();
    
    // Show modal
    const modal = document.getElementById('createPlaylistModal');
    if (modal) modal.classList.remove('hidden');
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.add('hidden');
}

function openCreateSongModal() {
    const modal = document.getElementById('createSongModal');
    if (modal) modal.classList.remove('hidden');
}

function viewLyrics(songId) {
    window.open(`/music/song/${songId}/lyrics`, '_blank', 'width=600,height=500');
}

function viewPlaylistSongs(playlistId) {
    fetch(`/music/playlist/${playlistId}/songs`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'Error loading songs');
                return;
            }
            
            const titleElement = document.getElementById('viewPlaylistTitle');
            const contentElement = document.getElementById('viewPlaylistContent');
            
            if (titleElement) {
                titleElement.innerHTML = `<i class="fas fa-list mr-2"></i>${data.playlist_title} - Songs`;
            }
            
            if (!contentElement) return;
            
            if (data.songs.length === 0) {
                contentElement.innerHTML = `
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-music fa-3x mb-3 text-gray-300"></i>
                        <p>No songs in this playlist yet.</p>
                        @if($canEditPlaylists)
                        <a href="/music/playlist/${playlistId}/edit" class="inline-block mt-3 text-blue-600">Add songs</a>
                        @endif
                    </div>
                `;
            } else {
                let html = `<div class="space-y-2">`;
                data.songs.forEach((song, index) => {
                    html += `
                        <div class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                            <div class="flex items-center space-x-3">
                                <span class="text-gray-400 text-sm w-6">${index + 1}</span>
                                <div>
                                    <p class="font-medium text-gray-800">${escapeHtml(song.title)}</p>
                                    <div class="flex flex-wrap gap-2 text-xs text-gray-500">
                                        ${song.key_signature ? `<span>Key: ${escapeHtml(song.key_signature)}</span>` : ''}
                                        ${song.tempo ? `<span>Tempo: ${escapeHtml(song.tempo)} BPM</span>` : ''}
                                        ${song.artist ? `<span>Artist: ${escapeHtml(song.artist)}</span>` : ''}
                                        ${song.assigned_singer ? `<span>Singer: ${escapeHtml(song.assigned_singer)}</span>` : ''}
                                    </div>
                                </div>
                            </div>
                            <button onclick="viewLyrics(${song.id})" class="text-green-600 hover:text-green-800" title="View Lyrics">
                                <i class="fas fa-file-alt"></i>
                            </button>
                        </div>
                    `;
                });
                html += `</div>`;
                contentElement.innerHTML = html;
            }
            
            const modal = document.getElementById('viewPlaylistModal');
            if (modal) modal.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Could not load playlist songs: ' + error.message);
        });
}

function addToPlaylist() {
    const playlistId = document.getElementById('playlistSelect')?.value;
    const songId = document.getElementById('songSelect')?.value;
    
    if (!playlistId || !songId) {
        alert('Please select both a playlist and a song');
        return;
    }
    
    fetch('/music/add-to-playlist', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            playlist_id: playlistId,
            song_id: songId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Song added to playlist!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding song to playlist');
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeSearchAndSelect();
    
    // Form submissions
    const createPlaylistForm = document.getElementById('createPlaylistForm');
    if (createPlaylistForm) {
        createPlaylistForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Playlist created with ' + (data.songs_added || 0) + ' songs!');
                    closeModal('createPlaylistModal');
                    location.reload();
                } else {
                    alert(data.message || 'Error creating playlist');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating playlist');
            });
        });
    }
    
    const createSongForm = document.getElementById('createSongForm');
    if (createSongForm) {
        createSongForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Song added successfully!');
                    closeModal('createSongModal');
                    location.reload();
                } else {
                    alert(data.message || 'Error creating song');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating song');
            });
        });
    }
});
</script>
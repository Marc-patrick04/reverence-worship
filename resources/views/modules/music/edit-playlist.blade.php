@extends('layouts.app')

@section('title', 'Edit Playlist')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Playlist</h1>
        <a href="{{ route('music.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form method="POST" action="{{ route('music.playlist.update', $playlist->id) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Playlist Title *</label>
                    <input type="text" name="title" required value="{{ $playlist->title }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ $playlist->description }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Songs in Playlist</label>
                    <div class="border rounded-lg p-3 max-h-64 overflow-y-auto">
                        <div class="mb-2">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" id="selectAllSongs" class="rounded border-gray-300 text-blue-600">
                                <span class="text-sm font-medium text-gray-700">Select All Songs</span>
                            </label>
                        </div>
                        <div class="border-t pt-2 space-y-1">
                            @foreach($songs as $song)
                            <label class="flex items-center space-x-2 p-1 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" name="songs[]" value="{{ $song->id }}" 
                                       {{ in_array($song->id, $playlistSongIds) ? 'checked' : '' }}
                                       class="song-checkbox rounded border-gray-300 text-blue-600">
                                <span class="text-sm text-gray-700">{{ $song->title }}</span>
                                <span class="text-xs text-gray-400 ml-auto">{{ $song->key_signature ?? '' }} {{ $song->tempo ? $song->tempo.' BPM' : '' }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                <a href="{{ route('music.index') }}" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i> Update Playlist
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('selectAllSongs')?.addEventListener('change', function() {
    document.querySelectorAll('.song-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
});
</script>
@endsection
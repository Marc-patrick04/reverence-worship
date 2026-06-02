<!DOCTYPE html>
<html>
<head>
    <title>{{ $song->title }} - Lyrics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-4 border-b pb-3">
            <h1 class="text-2xl font-bold text-gray-800">{{ $song->title }}</h1>
            <button onclick="window.close()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="space-y-2 mb-4">
            <p><strong>Artist:</strong> {{ $song->artist ?? 'Unknown' }}</p>
            <p><strong>Key:</strong> {{ $song->key_signature ?? '-' }}</p>
            <p><strong>Tempo:</strong> {{ $song->tempo ?? '-' }} BPM</p>
            <p><strong>Singer:</strong> {{ $song->assigned_singer ?? 'Not assigned' }}</p>
            @if($song->youtube_link)
                <p><strong>YouTube:</strong> <a href="{{ $song->youtube_link }}" target="_blank" class="text-blue-600">Watch Video</a></p>
            @endif
        </div>
        
        <div class="border-t pt-4">
            <h3 class="font-bold text-lg mb-2">Lyrics</h3>
            <div class="prose max-w-none">
                <pre class="whitespace-pre-wrap font-sans text-gray-700">{{ $song->lyrics ?? 'No lyrics available.' }}</pre>
            </div>
        </div>
    </div>
</body>
</html>
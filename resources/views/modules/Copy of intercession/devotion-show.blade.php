@extends('layouts.app')

@section('title', $devotion->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-md p-8">
        <div class="mb-6">
            <a href="{{ url()->previous() }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $devotion->title }}</h1>
        <p class="text-gray-500 mb-6">{{ \Carbon\Carbon::parse($devotion->date)->format('l, F j, Y') }}</p>
        
        @if($devotion->bible_verse)
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded">
            <p class="text-blue-800 italic text-lg">"{{ $devotion->bible_verse }}"</p>
        </div>
        @endif
        
        <div class="prose max-w-none mb-8">
            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $devotion->content }}</p>
        </div>
        
        @if(!$hasCompleted && auth()->check())
        <form action="{{ route('intercession.devotion.complete') }}" method="POST" id="devotion-complete-form-single">
            @csrf
            <input type="hidden" name="devotion_id" value="{{ $devotion->id }}">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                Mark as Read
            </button>
        </form>
        @elseif($hasCompleted)
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg inline-flex items-center gap-2">
            <i class="fas fa-check-circle"></i> You've completed this devotion
        </div>
        @endif
    </div>
</div>

<script>
document.getElementById('devotion-complete-form-single')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: new FormData(this)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Devotion marked as read!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error marking devotion as read');
    });
});
</script>
@endsection
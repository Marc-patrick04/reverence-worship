@extends('layouts.app')

@section('title', 'Spiritual Archives')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Spiritual Archives</h1>
            <p class="text-gray-600 mt-1">Access past sermons, teachings, and spiritual resources</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6">
        @forelse($archives as $archive)
        <div class="border-b last:border-0 py-4">
            <h3 class="font-semibold text-gray-800">{{ $archive->title }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ $archive->content ?? 'No description' }}</p>
            <div class="flex justify-between items-center mt-2">
                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($archive->created_at)->format('d/m/Y') }}</span>
                @if($archive->file_path)
                <a href="{{ asset($archive->file_path) }}" class="text-blue-600 hover:text-blue-800 text-sm" target="_blank">
                    <i class="fas fa-download mr-1"></i> Download
                </a>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center text-gray-500 py-12">
            <i class="fas fa-archive text-5xl text-gray-300 mb-3"></i>
            <p>No archives available yet</p>
        </div>
        @endforelse
        
        @if($archives->hasPages())
        <div class="mt-4">
            {{ $archives->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
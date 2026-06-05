@extends('layouts.app')

@section('title', $page->title . ' - Social Fellowship Archives')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="h-2 bg-gradient-to-r from-gray-700 to-gray-900"></div>
        
        <div class="p-8">
            <div class="mb-6">
                <a href="{{ route('social-fellowship.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back to Archives
                </a>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $page->title }}</h1>
            
            <div class="flex items-center gap-4 text-sm text-gray-500 mb-6 pb-4 border-b">
                <span><i class="fas fa-folder mr-1"></i> {{ $page->section->name ?? 'Uncategorized' }}</span>
                <span><i class="fas fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($page->created_at)->format('F j, Y') }}</span>
                @if($page->is_published)
                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs">Published</span>
                @else
                <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full text-xs">Draft</span>
                @endif
            </div>
            
            <div class="prose max-w-none">
                {!! nl2br(e($page->content)) !!}
            </div>
            
            <div class="mt-6 pt-4 border-t text-xs text-gray-400">
                Last updated: {{ \Carbon\Carbon::parse($page->updated_at)->format('F j, Y g:i A') }}
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', $page->title)

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="h-2 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
        
        <div class="p-8">
            <div class="mb-6 flex flex-wrap justify-between items-center gap-3">
                <a href="{{ route('intercession.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2 transition">
                    <i class="fas fa-arrow-left"></i> Back to Archives
                </a>
                @if($page->file_path)
                <a href="{{ route('intercession.archives.pages.download', $page->id) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition">
                    <i class="fas fa-download"></i> Download File
                </a>
                @endif
            </div>
            
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                <i class="fas fa-folder text-yellow-500"></i>
                <span>{{ $page->section->name ?? 'Uncategorized' }}</span>
                <span class="text-gray-300">/</span>
                <span class="text-gray-600">{{ $page->title }}</span>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $page->title }}</h1>
            
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-6 pb-4 border-b">
                <span><i class="fas fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($page->created_at)->format('F j, Y') }}</span>
                @if($page->is_published)
                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs">Published</span>
                @else
                <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full text-xs">Draft</span>
                @endif
                @if($page->file_name)
                <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs">
                    <i class="fas fa-paperclip mr-1"></i> {{ $page->file_name }}
                </span>
                @endif
                @if($page->file_size)
                <span class="text-xs text-gray-400">{{ number_format($page->file_size / 1024, 1) }} KB</span>
                @endif
            </div>
            
            @if($page->file_path && $page->is_pdf ?? false)
                <div class="mb-6">
                    <iframe src="{{ asset('storage/' . $page->file_path) }}" 
                            class="w-full h-96 rounded-lg border" 
                            frameborder="0"></iframe>
                </div>
            @endif
            
            <div class="prose max-w-none">
                {!! nl2br(e($page->content ?? '')) !!}
            </div>
            
            @if($page->file_path && !($page->is_pdf ?? false))
                <div class="mt-6 p-4 bg-gray-50 rounded-lg text-center">
                    <i class="fas fa-file text-3xl text-gray-400 mb-2"></i>
                    <p class="text-gray-600">{{ $page->file_name }}</p>
                    <a href="{{ asset('storage/' . $page->file_path) }}" target="_blank" 
                       class="inline-block mt-2 text-blue-600 hover:text-blue-800 font-medium">
                        <i class="fas fa-external-link-alt mr-1"></i> Open File
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Edit Feature')
@section('page-title', 'Page Management')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Edit Feature</h1>
            <p class="text-gray-600 mt-1">Page: <strong>{{ $page->display_name }}</strong></p>
        </div>
        <a href="{{ route('pages.features', $page->id) }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Back to Features
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form method="POST" action="{{ route('pages.features.update', [$page->id, $feature->id]) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Feature Name (slug) *</label>
                    <input type="text" name="name" required 
                           value="{{ old('name', $feature->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Use lowercase with hyphens (e.g., view-songs, manage-events)</p>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Name *</label>
                    <input type="text" name="display_name" required 
                           value="{{ old('display_name', $feature->display_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('display_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="What this feature allows the user to do...">{{ old('description', $feature->description) }}</textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                <a href="{{ route('pages.features', $page->id) }}" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i> Update Feature
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
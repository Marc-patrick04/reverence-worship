@extends('layouts.app')

@section('title', 'Edit Page')
@section('page-title', 'Page Management')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Page</h1>
        <a href="{{ route('pages.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Back to Pages
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form method="POST" action="{{ route('pages.update', $page->id) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Page Name (slug) *</label>
                    <input type="text" name="name" required 
                           value="{{ old('name', $page->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Use lowercase with hyphens (e.g., music-ministry)</p>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Name *</label>
                    <input type="text" name="display_name" required 
                           value="{{ old('display_name', $page->display_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('display_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Font Awesome) *</label>
                    <div class="flex">
                        <input type="text" name="icon" required 
                               value="{{ old('icon', $page->icon) }}"
                               placeholder="fa-users"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg">
                            <i class="fas {{ $page->icon }} text-blue-600"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Font Awesome icon class (e.g., fa-users, fa-music, fa-cog)</p>
                    @error('icon')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Route Name</label>
                    <input type="text" name="route" 
                           value="{{ old('route', $page->route) }}"
                           placeholder="admin.users.index"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Laravel route name for this page (optional)</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" 
                           value="{{ old('sort_order', $page->sort_order) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Lower number appears first in menu</p>
                </div>
                
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="is_active" value="1" 
                               {{ $page->is_active ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Active (visible in menu)</span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                <a href="{{ route('pages.index') }}" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i> Update Page
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
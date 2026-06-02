@extends('layouts.app')

@section('title', 'Create Page')
@section('page-title', 'Page Management')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Create New Page</h1>
        <a href="{{ route('pages.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form method="POST" action="{{ route('pages.store') }}">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Page Name (slug) *</label>
                    <input type="text" name="name" required 
                           placeholder="user-management"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Use lowercase with hyphens (e.g., music-ministry)</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Name *</label>
                    <input type="text" name="display_name" required 
                           placeholder="User Management"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Font Awesome) *</label>
                    <div class="flex">
                        <input type="text" name="icon" required 
                               placeholder="fa-users"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg">
                            <i class="fas fa-users text-blue-600"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Font Awesome icon class (e.g., fa-users, fa-music, fa-cog)</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Route Name</label>
                    <input type="text" name="route" 
                           placeholder="admin.users.index"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Laravel route name for this page (optional)</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="999"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Lower number appears first in menu</p>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                <a href="{{ route('pages.index') }}" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i> Create Page
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
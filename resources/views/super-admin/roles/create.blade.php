@extends('layouts.app')

@section('title', 'Create Role')

@section('page-title', 'Role Management')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-3xl font-bold text-gray-800">Create New Role</h3>
            <p class="text-gray-600 mt-1">Create a new role and assign permissions</p>
        </div>
        <a href="{{ route('roles.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Roles
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form method="POST" action="{{ route('roles.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role Name (slug) *</label>
                    <input type="text" name="name" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="music-leader"
                           value="{{ old('name') }}">
                    <p class="text-xs text-gray-500 mt-1">Use lowercase with hyphens (e.g., music-leader)</p>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Display Name *</label>
                    <input type="text" name="display_name" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Music Leader"
                           value="{{ old('display_name') }}">
                    @error('display_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Describe the purpose of this role">{{ old('description') }}</textarea>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Assign Page Features (Permissions)</label>
                    <div class="border rounded-lg p-4 bg-gray-50 max-h-96 overflow-y-auto">
                        @foreach($pages as $page)
                            <div class="mb-4 border-b pb-3">
                                <div class="flex items-center mb-2">
                                    <i class="fas {{ $page->icon }} mr-2 text-blue-600"></i>
                                    <h4 class="font-semibold text-gray-800">{{ $page->display_name }}</h4>
                                </div>
                                <div class="ml-6 grid grid-cols-2 gap-2">
                                    @foreach($features->where('page_id', $page->id) as $feature)
                                        <label class="flex items-center space-x-2 p-1 hover:bg-white rounded cursor-pointer">
                                            <input type="checkbox" name="features[]" value="{{ $feature->id }}" 
                                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="text-sm text-gray-700">{{ $feature->display_name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                <a href="{{ route('roles.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md">
                    <i class="fas fa-save mr-2"></i>
                    Create Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
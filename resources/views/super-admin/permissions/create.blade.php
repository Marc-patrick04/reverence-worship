@extends('layouts.app')

@section('title', 'Create Permission')
@section('page-title', 'Permission Management')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Create New Permission</h1>
            <p class="text-gray-600 mt-1">Add a new permission for access control</p>
        </div>
        <a href="{{ route('permissions.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Permissions
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form method="POST" action="{{ route('permissions.store') }}">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Permission Name (slug) *</label>
                    <input type="text" name="name" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                           placeholder="users-view"
                           value="{{ old('name') }}">
                    <p class="text-xs text-gray-500 mt-1">Use lowercase with hyphens (e.g., users-view, roles-create)</p>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Display Name *</label>
                    <input type="text" name="display_name" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="View Users"
                           value="{{ old('display_name') }}">
                    @error('display_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Module *</label>
                    <select name="module" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Module</option>
                        <option value="dashboard" {{ old('module') == 'dashboard' ? 'selected' : '' }}>Dashboard</option>
                        <option value="users" {{ old('module') == 'users' ? 'selected' : '' }}>Users</option>
                        <option value="roles" {{ old('module') == 'roles' ? 'selected' : '' }}>Roles</option>
                        <option value="permissions" {{ old('module') == 'permissions' ? 'selected' : '' }}>Permissions</option>
                        <option value="settings" {{ old('module') == 'settings' ? 'selected' : '' }}>Settings</option>
                        <option value="logs" {{ old('module') == 'logs' ? 'selected' : '' }}>Logs</option>
                        <option value="system" {{ old('module') == 'system' ? 'selected' : '' }}>System</option>
                        <option value="music" {{ old('module') == 'music' ? 'selected' : '' }}>Music</option>
                        <option value="media" {{ old('module') == 'media' ? 'selected' : '' }}>Media</option>
                    </select>
                    @error('module')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Describe what this permission allows">{{ old('description') }}</textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                <a href="{{ route('permissions.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md">
                    <i class="fas fa-save mr-2"></i>
                    Create Permission
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
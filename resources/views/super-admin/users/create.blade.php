@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Create New User</h1>
            <p class="text-gray-600 mt-1">Add a new user to the system</p>
        </div>
        <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Users
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                    <input type="text" name="name" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="{{ old('name') }}">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                    <input type="email" name="email" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="{{ old('email') }}">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                    <input type="password" name="password" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                    <input type="password" name="password_confirmation" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Assign Roles</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($roles as $role)
                            <label class="flex items-center space-x-2 p-2 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700">{{ $role->display_name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                <a href="{{ route('users.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md">
                    <i class="fas fa-save mr-2"></i>
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
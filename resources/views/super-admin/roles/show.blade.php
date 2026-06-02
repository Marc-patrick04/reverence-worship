@extends('layouts.app')

@section('title', 'Role Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Role Details</h1>
            <p class="text-gray-600 mt-1">View role information and assigned permissions</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('roles.edit', $role->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-edit mr-2"></i>
                Edit Role
            </a>
            <a href="{{ route('roles.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Role Info -->
        <div class="p-6 border-b">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 rounded-full {{ $role->name == 'super-admin' ? 'bg-red-600' : 'bg-blue-600' }} flex items-center justify-center">
                    <i class="fas {{ $role->name == 'super-admin' ? 'fa-crown' : 'fa-tag' }} text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $role->display_name }}</h2>
                    <p class="text-gray-500">Slug: {{ $role->name }}</p>
                </div>
            </div>
            
            @if($role->description)
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-700">{{ $role->description }}</p>
                </div>
            @endif
            
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div class="p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600">Users with this role</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $role->users->count() }}</p>
                </div>
                <div class="p-3 bg-green-50 rounded-lg">
                    <p class="text-sm text-gray-600">Permissions assigned</p>
                    <p class="text-2xl font-bold text-green-600">{{ $role->permissions->count() }}</p>
                </div>
            </div>
        </div>
        
        <!-- Users List -->
        <div class="p-6 border-b">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-users mr-2 text-blue-600"></i>
                Users with this role
            </h3>
            @if($role->users->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($role->users as $user)
                        <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-600 text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No users assigned to this role yet.</p>
            @endif
        </div>
        
        <!-- Permissions List -->
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-key mr-2 text-green-600"></i>
                Assigned Permissions
            </h3>
            @if($role->permissions->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    @foreach($role->permissions->groupBy('module') as $module => $permissions)
                        <div class="border rounded-lg p-3">
                            <h4 class="font-semibold text-gray-700 mb-2 capitalize">{{ $module }}</h4>
                            <div class="space-y-1">
                                @foreach($permissions as $permission)
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-check-circle text-green-500 mr-2 text-xs"></i>
                                        {{ $permission->display_name }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No permissions assigned to this role yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
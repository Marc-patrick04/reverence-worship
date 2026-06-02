@extends('layouts.app')

@section('title', 'Activity Log Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Activity Log Details</h1>
            <p class="text-gray-600 mt-1">View complete activity information</p>
        </div>
        <a href="{{ route('logs.activity') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Logs
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 border-b bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Log Information
            </h3>
        </div>
        
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500">Log ID</label>
                    <p class="text-lg font-semibold text-gray-900">#{{ $log->id }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500">Action Type</label>
                    <p class="text-lg">
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $log->action == 'login' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $log->action == 'logout' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ str_contains($log->action, 'create') ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ str_contains($log->action, 'update') ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ str_contains($log->action, 'delete') ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                        </span>
                    </p>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500">User</label>
                <div class="flex items-center mt-1">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $log->user ? $log->user->name : 'System' }}</p>
                        <p class="text-xs text-gray-500">{{ $log->user ? $log->user->email : 'N/A' }}</p>
                    </div>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500">Description</label>
                <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-700">{{ $log->description }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500">IP Address</label>
                    <p class="text-sm font-mono text-gray-700 mt-1">{{ $log->ip_address ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500">Timestamp</label>
                    <p class="text-sm text-gray-700 mt-1">{{ $log->created_at ? date('F d, Y H:i:s', strtotime($log->created_at)) : 'N/A' }}</p>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500">User Agent</label>
                <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs font-mono text-gray-600 break-all">{{ $log->user_agent ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
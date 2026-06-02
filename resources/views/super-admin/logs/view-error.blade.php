@extends('layouts.app')

@section('title', 'Error Log Details')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Error Log Details</h1>
            <p class="text-gray-600 mt-1">View complete error information and stack trace</p>
        </div>
        <a href="{{ route('logs.errors') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Logs
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 border-b bg-red-50">
            <h3 class="text-lg font-bold text-red-800">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Error Information
            </h3>
        </div>
        
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500">Error ID</label>
                    <p class="text-lg font-semibold text-gray-900">#{{ $log->id }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500">Error Type</label>
                    <p class="text-lg">
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                            {{ $log->error_type }}
                        </span>
                    </p>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500">Error Message</label>
                <div class="mt-1 p-3 bg-red-50 rounded-lg border border-red-200">
                    <p class="text-sm font-mono text-red-800">{{ $log->message }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500">File Path</label>
                    <p class="text-sm font-mono text-gray-700 mt-1 break-all">{{ $log->file_path ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500">Line Number</label>
                    <p class="text-sm font-mono text-gray-700 mt-1">{{ $log->line_number ?? 'N/A' }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500">User</label>
                    <div class="flex items-center mt-1">
                        <div class="w-6 h-6 bg-gray-400 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-xs"></i>
                        </div>
                        <span class="ml-2 text-sm text-gray-700">{{ $log->user ? $log->user->name : 'System' }}</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500">Timestamp</label>
                    <p class="text-sm text-gray-700 mt-1">{{ $log->created_at ? $log->created_at->format('F d, Y H:i:s') : 'N/A' }}</p>
                </div>
            </div>
            
            @if($log->stack_trace)
            <div>
                <label class="block text-xs font-medium text-gray-500">Stack Trace</label>
                <div class="mt-1 p-3 bg-gray-900 rounded-lg overflow-x-auto">
                    <pre class="text-xs text-green-400 font-mono whitespace-pre-wrap break-all">{{ $log->stack_trace }}</pre>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
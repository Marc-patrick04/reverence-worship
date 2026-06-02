@extends('layouts.app')

@section('title', $family->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    <div class="flex justify-between items-center">
        <div>
            <a href="{{ route('social-fellowship.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                <i class="fas fa-arrow-left"></i> Back to Families
            </a>
            <h1 class="text-3xl font-bold text-gray-800">{{ $family->name }}</h1>
            @if($family->motto)
            <p class="text-gray-500 italic mt-1">"{{ $family->motto }}"</p>
            @endif
        </div>
        <div class="flex gap-2">
            <button onclick="addMember()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-user-plus"></i> Add Member
            </button>
        </div>
    </div>
    
    <!-- Family Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-blue-50 rounded-xl p-4 text-center">
            <i class="fas fa-users text-2xl text-blue-600 mb-2"></i>
            <p class="text-2xl font-bold text-blue-600">{{ count($members) }}</p>
            <p class="text-xs text-gray-600">Total Members</p>
        </div>
        <div class="bg-green-50 rounded-xl p-4 text-center">
            <i class="fas fa-tasks text-2xl text-green-600 mb-2"></i>
            <p class="text-2xl font-bold text-green-600">{{ count($tasks) }}</p>
            <p class="text-xs text-gray-600">Active Tasks</p>
        </div>
        <div class="bg-purple-50 rounded-xl p-4 text-center">
            <i class="fas fa-clipboard-list text-2xl text-purple-600 mb-2"></i>
            <p class="text-2xl font-bold text-purple-600">{{ count($actionPlans) }}</p>
            <p class="text-xs text-gray-600">Action Plans</p>
        </div>
    </div>
    
    <!-- Members List -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Members</h2>
        <div class="space-y-3">
            @foreach($members as $member)
            <div class="flex justify-between items-center p-3 border rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">{{ $member->name }}</p>
                    <p class="text-sm text-gray-500">{{ $member->email }}</p>
                </div>
                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">{{ ucfirst($member->role) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Description -->
    @if($family->description)
    <div class="bg-white rounded-xl shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-2">About</h2>
        <p class="text-gray-600">{{ $family->description }}</p>
    </div>
    @endif
</div>

<script>
function addMember() {
    alert('Add member feature coming soon');
}
</script>
@endsection
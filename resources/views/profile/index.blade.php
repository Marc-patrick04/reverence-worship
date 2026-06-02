@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center space-x-4 mb-6">
            <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-gray-500 text-3xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ Auth::user()->name }}</h2>
                <p class="text-gray-500">{{ Auth::user()->email }}</p>
                <p class="text-sm text-gray-400 mt-1">Member since {{ Auth::user()->created_at->format('F j, Y') }}</p>
            </div>
        </div>
        
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Profile Information</h3>
            <div class="space-y-3">
                <div class="flex">
                    <div class="w-32 text-gray-500">Full Name</div>
                    <div class="text-gray-800">{{ Auth::user()->name }}</div>
                </div>
                <div class="flex">
                    <div class="w-32 text-gray-500">Email Address</div>
                    <div class="text-gray-800">{{ Auth::user()->email }}</div>
                </div>
                <div class="flex">
                    <div class="w-32 text-gray-500">Role</div>
                    <div class="text-gray-800">Administrator</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
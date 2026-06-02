@extends('layouts.app')

@section('title', $user->name . ' - User Details')
@section('page-title', 'User Details')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('users.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i> Back to Users
        </a>
    </div>
    
    <!-- User Profile Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        
        <!-- Header with Gradient -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-8">
            <div class="flex items-center gap-4">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center">
                    <span class="text-blue-600 text-2xl font-bold">{{ substr($user->name, 0, 2) }}</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $user->name }}</h1>
                    <p class="text-blue-100">{{ $user->email }}</p>
                    <div class="flex gap-2 mt-2">
                        @if($user->is_active)
                            <span class="px-2 py-1 text-xs rounded-full bg-green-500 text-white">
                                <i class="fas fa-circle text-xs mr-1"></i> Active
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-red-500 text-white">
                                <i class="fas fa-circle text-xs mr-1"></i> Inactive
                            </span>
                        @endif
                        
                        @foreach($user->roles as $role)
                            <span class="px-2 py-1 text-xs rounded-full bg-white/20 text-white">
                                {{ $role->display_name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <!-- User Information -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Personal Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 pb-2 border-b">
                        <i class="fas fa-user mr-2 text-blue-600"></i> Personal Information
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-gray-500 block">Full Name</label>
                            <p class="text-gray-800">{{ $user->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block">Email Address</label>
                            <p class="text-gray-800">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block">Phone Number</label>
                            <p class="text-gray-800">{{ $user->phone ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block">Gender</label>
                            <p class="text-gray-800">{{ $user->gender ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block">Date of Birth</label>
                            <p class="text-gray-800">{{ $user->date_of_birth ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block">Marital Status</label>
                            <p class="text-gray-800">{{ $user->marital_status ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Address Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 pb-2 border-b">
                        <i class="fas fa-map-marker-alt mr-2 text-green-600"></i> Address Information
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-gray-500 block">Province</label>
                            <p class="text-gray-800">{{ $user->province ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block">District</label>
                            <p class="text-gray-800">{{ $user->district ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block">Sector</label>
                            <p class="text-gray-800">{{ $user->sector ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block">Village</label>
                            <p class="text-gray-800">{{ $user->village ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Professional Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 pb-2 border-b">
                        <i class="fas fa-briefcase mr-2 text-purple-600"></i> Professional Information
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-gray-500 block">Occupation</label>
                            <p class="text-gray-800">{{ $user->occupation ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block">Ministry Role</label>
                            <p class="text-gray-800">{{ $user->ministry_role ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block">Membership Type</label>
                            <p class="text-gray-800">{{ $user->membership_type ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Singer Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 pb-2 border-b">
                        <i class="fas fa-microphone-alt mr-2 text-orange-600"></i> Singer Information
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-gray-500 block">Is Singer</label>
                            <p class="text-gray-800">{{ $user->is_singer ? 'Yes' : 'No' }}</p>
                        </div>
                        @if($user->is_singer)
                        <div>
                            <label class="text-xs text-gray-500 block">Voice Part</label>
                            <p class="text-gray-800">{{ $user->voice_part ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block">Singer Level</label>
                            <p class="text-gray-800">{{ $user->singer_level ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block">Singer Notes</label>
                            <p class="text-gray-800">{{ $user->singer_notes ?? 'None' }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Emergency Contact -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 pb-2 border-b">
                        <i class="fas fa-phone-alt mr-2 text-red-600"></i> Emergency Contact
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-gray-500 block">Contact Name</label>
                            <p class="text-gray-800">{{ $user->emergency_name ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block">Contact Phone</label>
                            <p class="text-gray-800">{{ $user->emergency_contact ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Skills & Notes -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 pb-2 border-b">
                        <i class="fas fa-star mr-2 text-yellow-600"></i> Skills & Notes
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-gray-500 block">Skills & Talents</label>
                            <p class="text-gray-800">{{ $user->skills ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block">Additional Notes</label>
                            <p class="text-gray-800">{{ $user->notes ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Account Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 pb-2 border-b">
                        <i class="fas fa-clock mr-2 text-gray-600"></i> Account Information
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-gray-500 block">Member Since</label>
                            <p class="text-gray-800">{{ $user->created_at ? date('F j, Y', strtotime($user->created_at)) : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block">Last Updated</label>
                            <p class="text-gray-800">{{ $user->updated_at ? date('F j, Y', strtotime($user->updated_at)) : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end gap-3">
            <a href="{{ route('users.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                Close
            </a>
            <a href="{{ route('users.edit', $user->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-edit mr-2"></i> Edit User
            </a>
        </div>
        
    </div>
</div>
@endsection
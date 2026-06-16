@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Profile Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-8 text-white">
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center border-2 border-white">
                    <i class="fas fa-user text-white text-4xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold">{{ Auth::user()->name }}</h2>
                    <p class="text-blue-100">{{ Auth::user()->email }}</p>
                    
                </div>
            </div>
        </div>
        
        <!-- Profile Information -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-circle text-blue-500"></i>
                        Personal Information
                    </h3>
                    <div class="space-y-3">
                        <div class="flex border-b pb-2">
                            <div class="w-32 text-sm text-gray-500">Full Name</div>
                            <div class="text-sm text-gray-800 font-medium">{{ Auth::user()->name }}</div>
                        </div>
                        <div class="flex border-b pb-2">
                            <div class="w-32 text-sm text-gray-500">Email Address</div>
                            <div class="text-sm text-gray-800 font-medium">{{ Auth::user()->email }}</div>
                        </div>
                        <div class="flex border-b pb-2">
                            <div class="w-32 text-sm text-gray-500">Phone</div>
                            <div class="text-sm text-gray-800 font-medium">{{ Auth::user()->phone ?? '-' }}</div>
                        </div>
                        <div class="flex border-b pb-2">
                            <div class="w-32 text-sm text-gray-500">Gender</div>
                            <div class="text-sm text-gray-800 font-medium">{{ ucfirst(Auth::user()->gender ?? 'Not specified') }}</div>
                        </div>
                        <div class="flex border-b pb-2">
                            <div class="w-32 text-sm text-gray-500">Date of Birth</div>
                            <div class="text-sm text-gray-800 font-medium">{{ Auth::user()->date_of_birth ? date('F j, Y', strtotime(Auth::user()->date_of_birth)) : '-' }}</div>
                        </div>
                        <div class="flex border-b pb-2">
                            <div class="w-32 text-sm text-gray-500">Marital Status</div>
                            <div class="text-sm text-gray-800 font-medium">{{ Auth::user()->marital_status ?? '-' }}</div>
                        </div>
                        <div class="flex border-b pb-2">
                            <div class="w-32 text-sm text-gray-500">Membership Type</div>
                            <div class="text-sm text-gray-800 font-medium">{{ Auth::user()->membership_type ?? '-' }}</div>
                        </div>
                        <div class="flex border-b pb-2">
                            <div class="w-32 text-sm text-gray-500">Occupation</div>
                            <div class="text-sm text-gray-800 font-medium">{{ Auth::user()->occupation ?? '-' }}</div>
                        </div>
                        
                    </div>
                </div>
                
                <!-- Address & Contact -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-address-card text-green-500"></i>
                        Address & Contact
                    </h3>
                    <div class="space-y-3">
                        <div class="flex border-b pb-2">
                            <div class="w-32 text-sm text-gray-500">Province</div>
                            <div class="text-sm text-gray-800 font-medium">{{ Auth::user()->province ?? '-' }}</div>
                        </div>
                        <div class="flex border-b pb-2">
                            <div class="w-32 text-sm text-gray-500">District</div>
                            <div class="text-sm text-gray-800 font-medium">{{ Auth::user()->district ?? '-' }}</div>
                        </div>
                        <div class="flex border-b pb-2">
                            <div class="w-32 text-sm text-gray-500">Sector</div>
                            <div class="text-sm text-gray-800 font-medium">{{ Auth::user()->sector ?? '-' }}</div>
                        </div>
                        <div class="flex border-b pb-2">
                            <div class="w-32 text-sm text-gray-500">Village</div>
                            <div class="text-sm text-gray-800 font-medium">{{ Auth::user()->village ?? '-' }}</div>
                        </div>
                        
                        
                        <div class="flex">
                            <div class="w-32 text-sm text-gray-500">Notes</div>
                            <div class="text-sm text-gray-800 font-medium">{{ Auth::user()->notes ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Spiritual Forms')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Spiritual Forms</h1>
            <p class="text-gray-600 mt-1">Take spiritual growth assessments and track your progress</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Available Forms -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Available Forms</h3>
                
                @forelse($forms as $form)
                <div class="border rounded-lg p-4 mb-3 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $form->title }}</h4>
                            <p class="text-sm text-gray-500 mt-1">{{ $form->description ?? 'No description' }}</p>
                        </div>
                        <a href="{{ route('intercession.form.show', $form->id) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                            Take Form
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-file-alt text-4xl text-gray-300 mb-3"></i>
                    <p>No forms available at the moment</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- My Results -->
        <div>
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">My Results</h3>
                
                @forelse($mySubmissions as $submission)
                <div class="border-b pb-3 mb-3 last:border-0">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-800">{{ $submission->form->title }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($submission->submitted_at)->format('d/m/Y') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-blue-600">{{ number_format($submission->score, 1) }}%</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-gray-500 py-4">
                    <p class="text-sm">No results yet</p>
                    <p class="text-xs">Take a form to see your results</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Form Results')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full 
                {{ $submission->score >= 70 ? 'bg-green-100' : ($submission->score >= 50 ? 'bg-yellow-100' : 'bg-red-100') }} mb-4">
                <i class="fas fa-chart-line text-3xl 
                    {{ $submission->score >= 70 ? 'text-green-600' : ($submission->score >= 50 ? 'text-yellow-600' : 'text-red-600') }}"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $form->title }}</h1>
            <p class="text-gray-500 mt-1">Submitted on {{ \Carbon\Carbon::parse($submission->submitted_at)->format('d/m/Y H:i') }}</p>
            
            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Your Score</p>
                <p class="text-4xl font-bold text-blue-600">{{ number_format($submission->score, 1) }}%</p>
            </div>
        </div>

        <div class="border-t pt-4">
            <h3 class="font-bold text-gray-800 mb-4">Your Answers</h3>
            <div class="space-y-4">
                @foreach($submission->answers ?? [] as $index => $answer)
                <div class="border rounded-lg p-3">
                    <p class="font-medium text-gray-800 text-sm">{{ $index + 1 }}. {{ $answer['question'] }}</p>
                    <p class="text-gray-600 mt-1 text-sm">{{ $answer['answer'] ?? 'No answer provided' }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end mt-6 pt-4 border-t">
            <a href="{{ route('intercession.forms') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Back to Forms
            </a>
        </div>
    </div>
</div>
@endsection
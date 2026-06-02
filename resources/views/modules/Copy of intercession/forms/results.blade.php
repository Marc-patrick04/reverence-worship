@extends('layouts.app')

@section('title', 'Form Results - ' . $form->title)

@section('content')
<div class="max-w-3xl mx-auto py-6">
    
    <!-- Results Header -->
    <div class="bg-white rounded-xl shadow-md p-8 mb-6 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full 
            {{ $submission->score >= 70 ? 'bg-green-100' : ($submission->score >= 50 ? 'bg-yellow-100' : 'bg-red-100') }} mb-4">
            <i class="fas fa-chart-line text-3xl 
                {{ $submission->score >= 70 ? 'text-green-600' : ($submission->score >= 50 ? 'text-yellow-600' : 'text-red-600') }}"></i>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-800">{{ $form->title }}</h1>
        <p class="text-gray-500 mt-1">Submitted on {{ \Carbon\Carbon::parse($submission->submitted_at)->format('l, d F Y H:i') }}</p>
        
        <div class="mt-6 p-6 bg-gray-50 rounded-lg max-w-md mx-auto">
            <p class="text-sm text-gray-600">Your Score</p>
            <p class="text-5xl font-bold text-blue-600">{{ number_format($submission->score, 1) }}%</p>
            <div class="w-full h-3 bg-gray-200 rounded-full mt-3">
                <div class="h-3 bg-blue-600 rounded-full" style="width: {{ $submission->score }}%"></div>
            </div>
        </div>
    </div>

    <!-- Your Answers -->
    <div class="bg-white rounded-xl shadow-md p-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Your Answers</h3>
        
        <div class="space-y-4">
            @foreach($submission->answers as $index => $answer)
            <div class="border rounded-lg p-4">
                <p class="font-semibold text-gray-800">{{ $index + 1 }}. {{ $answer['question'] }}</p>
                
                @if(is_array($answer['answer']))
                    <p class="text-gray-600 mt-2">
                        @foreach($answer['answer'] as $ans)
                            <span class="inline-block bg-gray-100 rounded-full px-3 py-1 text-sm mr-2 mb-1">{{ $ans }}</span>
                        @endforeach
                    </p>
                @else
                    <p class="text-gray-600 mt-2">{{ $answer['answer'] ?: 'No answer provided' }}</p>
                @endif
                
               @if(isset($answer['points']) && $answer['points'] > 0)
    <div class="mt-2 text-sm">
        <span class="text-green-600">
            <i class="fas fa-star mr-1"></i> 
            Score: {{ $answer['earned_points'] ?? 0 }}/{{ $answer['points'] }} points
        </span>
    </div>
@endif
            </div>
            @endforeach
        </div>
        
        <div class="flex justify-between items-center mt-6 pt-4 border-t">
            <a href="/intercession" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i> Back to Forms
            </a>
            @if(($form->settings['allow_retake'] ?? false))
            <a href="{{ route('forms.take', $form->id) }}" class="text-green-600 hover:text-green-800">
                <i class="fas fa-redo-alt mr-2"></i> Retake Form
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
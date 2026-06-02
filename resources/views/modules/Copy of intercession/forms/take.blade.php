@extends('layouts.app')

@section('title', $form->title)

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="bg-white rounded-xl shadow-md p-8">
        <div class="mb-6">
            <a href="{{ route('intercession.index') }}" class="text-indigo-600 hover:text-indigo-800">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $form->title }}</h1>
        <p class="text-gray-600 mb-8">{{ $form->description }}</p>
        
        <form method="POST" action="{{ route('forms.submit', $form->id) }}">
            @csrf
            
            @foreach($questions as $index => $question)
            <div class="mb-8 pb-4 border-b">
                <label class="block font-medium text-gray-800 mb-3">
                    {{ $index + 1 }}. {{ $question['text'] ?? $question['title'] ?? 'Question' }}
                    @if(isset($question['required']) && $question['required'])
                        <span class="text-red-500">*</span>
                    @endif
                </label>
                
                @if($question['type'] == 'short_answer')
                    <input type="text" name="question_{{ $index }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    
                @elseif($question['type'] == 'paragraph')
                    <textarea name="question_{{ $index }}" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    
                @elseif($question['type'] == 'multiple_choice')
                    @foreach($question['options'] ?? [] as $option)
                    <div class="mb-2">
                        <label class="flex items-center">
                            <input type="radio" name="question_{{ $index }}" value="{{ $option }}" class="mr-2">
                            {{ $option }}
                        </label>
                    </div>
                    @endforeach
                    
                @elseif($question['type'] == 'checkboxes')
                    @foreach($question['options'] ?? [] as $option)
                    <div class="mb-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="question_{{ $index }}[]" value="{{ $option }}" class="mr-2">
                            {{ $option }}
                        </label>
                    </div>
                    @endforeach
                    
                @elseif($question['type'] == 'dropdown')
                    <select name="question_{{ $index }}" class="px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select an option</option>
                        @foreach($question['options'] ?? [] as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                    
                @elseif($question['type'] == 'linear_scale')
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-500">{{ $question['minLabel'] ?? $question['min'] ?? 1 }}</span>
                        @for($i = ($question['min'] ?? 1); $i <= ($question['max'] ?? 5); $i++)
                        <label class="flex flex-col items-center">
                            <input type="radio" name="question_{{ $index }}" value="{{ $i }}" class="mb-1">
                            <span class="text-sm">{{ $i }}</span>
                        </label>
                        @endfor
                        <span class="text-sm text-gray-500">{{ $question['maxLabel'] ?? $question['max'] ?? 5 }}</span>
                    </div>
                    
                @elseif($question['type'] == 'rating')
                    <div class="flex gap-2">
                        @for($i = 1; $i <= ($question['max'] ?? 5); $i++)
                        <label class="flex flex-col items-center">
                            <input type="radio" name="question_{{ $index }}" value="{{ $i }}" class="mb-1">
                            <span class="text-lg">⭐</span>
                        </label>
                        @endfor
                    </div>
                    
                @elseif($question['type'] == 'date')
                    <input type="date" name="question_{{ $index }}" class="px-4 py-2 border border-gray-300 rounded-lg">
                    
                @elseif($question['type'] == 'time')
                    <input type="time" name="question_{{ $index }}" class="px-4 py-2 border border-gray-300 rounded-lg">
                @endif
            </div>
            @endforeach
            
            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium">
                    Submit Form
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
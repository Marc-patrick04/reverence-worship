@extends('layouts.app')

@section('title', $form->title)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">{{ $form->title }}</h1>
            @if($form->description)
                <p class="text-gray-600 mt-2">{{ $form->description }}</p>
            @endif
        </div>

        <form method="POST" action="{{ route('intercession.form.submit', $form->id) }}">
            @csrf
            
            <div class="space-y-6">
                @if($form->questions && is_array($form->questions))
                    @foreach($form->questions as $index => $question)
                    <div class="border rounded-lg p-4">
                        <label class="block font-medium text-gray-800 mb-2">
                            {{ $index + 1 }}. {{ $question['text'] ?? 'Question' }}
                        </label>
                        
                        @if(isset($question['type']) && $question['type'] == 'textarea')
                            <textarea name="question_{{ $index }}" rows="4" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Your answer..."></textarea>
                        @elseif(isset($question['options']) && is_array($question['options']))
                            <div class="space-y-2">
                                @foreach($question['options'] as $option)
                                <label class="flex items-center space-x-3">
                                    <input type="radio" name="question_{{ $index }}" value="{{ $option }}" 
                                           class="rounded-full border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-gray-700">{{ $option }}</span>
                                </label>
                                @endforeach
                            </div>
                        @else
                            <input type="text" name="question_{{ $index }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Your answer...">
                        @endif
                    </div>
                    @endforeach
                @else
                    <div class="text-center text-gray-500 py-8">
                        <p>No questions available for this form.</p>
                    </div>
                @endif
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <a href="{{ route('intercession.forms') }}" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Submit Form
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
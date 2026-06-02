@extends('layouts.app')

@section('title', $devotion->title)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $devotion->title }}</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::parse($devotion->date)->format('l, d F Y') }}</p>
                </div>
                @if($devotion->isCompletedByUser(auth()->id()))
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">
                        <i class="fas fa-check-circle mr-1"></i> Completed
                    </span>
                @endif
            </div>
        </div>

        <div class="prose max-w-none">
            <p class="text-gray-700 leading-relaxed">{{ $devotion->content }}</p>
            
            @if($devotion->bible_verse)
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-6">
                <p class="text-blue-800 italic">— {{ $devotion->bible_verse }}</p>
            </div>
            @endif
        </div>

        <div class="flex justify-between items-center mt-8 pt-4 border-t">
            <a href="{{ route('intercession.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
            
            @if(!$devotion->isCompletedByUser(auth()->id()))
            <form action="{{ route('intercession.devotion.complete', $devotion->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-check mr-2"></i> Mark as Read
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Form Submissions - ' . $form->title)

@section('content')
<div class="max-w-7xl mx-auto py-6">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $form->title }}</h1>
            <p class="text-gray-500 text-sm">Form Submissions ({{ $submissions->count() }} responses)</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('forms.manage.edit', $form->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-edit mr-2"></i> Edit Form
            </a>
            <a href="/intercession" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i> Back to Forms
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-md p-4 text-center border border-gray-200">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500">Total Responses</p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-4 text-center border border-gray-200">
            <p class="text-2xl font-bold text-green-600">{{ number_format($stats['avg_score'], 1) }}%</p>
            <p class="text-xs text-gray-500">Average Score</p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-4 text-center border border-gray-200">
            <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['highest'], 1) }}%</p>
            <p class="text-xs text-gray-500">Highest Score</p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-4 text-center border border-gray-200">
            <p class="text-2xl font-bold text-red-600">{{ number_format($stats['lowest'], 1) }}%</p>
            <p class="text-xs text-gray-500">Lowest Score</p>
        </div>
    </div>

    <!-- Submissions List -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">All Responses</h3>
            <button onclick="exportSubmissions()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-download mr-2"></i> Export CSV
            </button>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($submissions as $submission)
            <div class="hover:bg-gray-50 transition">
                <div class="px-6 py-4 flex justify-between items-center cursor-pointer" onclick="toggleSubmission({{ $submission->id }})">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-indigo-600 font-bold">{{ substr($submission->user->name ?? 'Anonymous', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $submission->user->name ?? 'Anonymous User' }}</p>
                            <p class="text-xs text-gray-500">{{ $submission->user->email ?? 'No email' }} • {{ \Carbon\Carbon::parse($submission->submitted_at)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        @if($submission->score > 0)
                        <div class="text-right">
                            <p class="text-lg font-bold {{ $submission->score >= 70 ? 'text-green-600' : ($submission->score >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ number_format($submission->score, 1) }}%
                            </p>
                            <div class="w-24 h-1.5 bg-gray-200 rounded-full mt-1">
                                <div class="h-1.5 rounded-full {{ $submission->score >= 70 ? 'bg-green-500' : ($submission->score >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $submission->score }}%"></div>
                            </div>
                        </div>
                        @endif
                        <i class="fas fa-chevron-down text-gray-400 transition-transform" id="icon-{{ $submission->id }}"></i>
                    </div>
                </div>
                
                <!-- Submission Details (Hidden by default) -->
                <div id="submission-{{ $submission->id }}" class="hidden px-6 pb-4 pt-2 bg-gray-50 border-t">
                    <div class="space-y-4">
                        @foreach($submission->answers as $index => $answer)
                        <div class="border-b border-gray-200 pb-3 last:border-0">
                            <p class="font-medium text-gray-800 text-sm">{{ $index + 1 }}. {{ $answer['question'] }}</p>
                            <div class="mt-2">
                                @if(is_array($answer['answer']))
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($answer['answer'] as $ans)
                                            <span class="px-2 py-1 bg-gray-100 rounded-md text-sm">{{ $ans }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-700 text-sm">{{ $answer['answer'] ?: '<em class="text-gray-400">No answer</em>' }}</p>
                                @endif
                                @if(isset($answer['earned_points']))
                                    <p class="text-xs text-green-600 mt-1">Points: {{ $answer['earned_points'] }}/{{ $answer['points'] }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-inbox text-5xl mb-3 text-gray-300"></i>
                <p>No submissions yet</p>
                <p class="text-sm mt-2">Share your form to receive responses</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
function toggleSubmission(id) {
    const details = document.getElementById(`submission-${id}`);
    const icon = document.getElementById(`icon-${id}`);
    
    if (details.classList.contains('hidden')) {
        details.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        details.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

function exportSubmissions() {
    window.location.href = '{{ route("forms.manage.submissions.export", $form->id) }}';
}
</script>
@endsection
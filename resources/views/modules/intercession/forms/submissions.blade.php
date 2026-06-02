@extends('layouts.app')

@section('title', 'Form Submissions')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="mb-6">
            <a href="{{ route('forms.manage.index') }}" class="text-indigo-600 hover:text-indigo-800">
                <i class="fas fa-arrow-left mr-2"></i> Back to Forms
            </a>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $form->title }} - Submissions</h1>
        <p class="text-gray-500 mb-6">Total responses: {{ count($submissions) }}</p>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $sub)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">User #{{ $sub->user_id }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if($sub->score)
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">{{ number_format($sub->score, 1) }}%</span>
                            @else
                                <span class="text-gray-400">Not graded</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($sub->submitted_at)->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('forms.results', $sub->form_id) }}" class="text-blue-600 hover:text-blue-800 text-sm">View Results</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">No submissions yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
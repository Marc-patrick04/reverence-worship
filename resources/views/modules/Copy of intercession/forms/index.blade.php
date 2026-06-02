@extends('layouts.app')

@section('title', 'Manage Forms')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Manage Forms</h1>
            <p class="text-gray-600 mt-1">Create and manage spiritual assessment forms</p>
        </div>
        <a href="{{ route('forms.manage.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i> Create New Form
        </a>
    </div>

    <!-- Back Button -->
<div class="max-w-7xl mx-auto mb-4">
    <a href="{{ route('intercession.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to Intercession & Growth
    </a>
</div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Questions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submissions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($forms as $form)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-medium text-gray-900">{{ $form->title }}</p>
                            <p class="text-xs text-gray-500">{{ Str::limit($form->description, 50) }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ is_array($form->questions) ? count($form->questions) : 0 }} questions</td>
                    <td class="px-6 py-4 text-sm">{{ $form->submissions->count() }}</td>
                    <td class="px-6 py-4">
                        @if($form->settings['is_published'] ?? false)
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Published</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Draft</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex space-x-2">
                            <a href="{{ route('forms.manage.edit', $form->id) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('forms.manage.submissions', $form->id) }}" class="text-green-600 hover:text-green-800">
                                <i class="fas fa-users"></i>
                            </a>
                            <form action="{{ route('forms.manage.delete', $form->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this form?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No forms created yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
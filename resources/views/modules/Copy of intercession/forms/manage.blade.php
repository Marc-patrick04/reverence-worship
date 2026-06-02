@extends('layouts.app')

@section('title', 'Manage Forms')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Manage Forms</h1>
            <p class="text-gray-600 mt-1">Create, edit, and manage spiritual assessment forms</p>
        </div>
        <a href="{{ route('forms.manage.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <i class="fas fa-plus"></i> Create Form
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Questions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submissions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
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
                    <td class="px-6 py-4 text-sm">
                        {{ is_array($form->questions) ? count($form->questions) : 0 }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        {{ $form->submissions()->count() }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($form->created_at)->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex space-x-3">
                            <a href="{{ route('forms.manage.edit', $form->id) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('forms.manage.submissions', $form->id) }}" class="text-green-600 hover:text-green-800" title="Submissions">
                                <i class="fas fa-users"></i>
                            </a>
                            <button onclick="deleteForm({{ $form->id }})" class="text-red-600 hover:text-red-800" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-file-alt text-4xl text-gray-300 mb-3"></i>
                        <p>No forms created yet</p>
                        <a href="{{ route('forms.manage.create') }}" class="inline-block mt-2 text-blue-600 hover:underline">
                            Create your first form
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function deleteForm(id) {
    if (confirm('Delete this form? All responses will be lost.')) {
        fetch(`/forms/manage/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => location.reload());
    }
}
</script>
@endsection
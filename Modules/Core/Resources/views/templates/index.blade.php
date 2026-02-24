@extends('core::layouts.app')

@section('title', 'Document templates')
@section('heading', 'Document templates')

@section('content')
<div class="mb-4">
    <a href="{{ route('templates.create') }}" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add template</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Slug</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($templates as $t)
            <tr>
                <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">{{ $t->name }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $t->slug }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('templates.preview', $t) }}" class="wise-link hover:underline text-sm">Preview</a>
                    <a href="{{ route('templates.edit', $t) }}" class="wise-link hover:underline text-sm ml-2">Edit</a>
                    <form method="POST" action="{{ route('templates.destroy', $t) }}" class="inline ml-2" onsubmit="return confirm('Delete this template?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 dark:text-red-400 text-sm">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No templates yet. Create one with placeholders like @{{employee_name}}, @{{date}}.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($templates->hasPages())
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">{{ $templates->links() }}</div>
    @endif
</div>
@endsection

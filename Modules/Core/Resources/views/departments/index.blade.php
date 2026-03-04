@extends('core::layouts.app')

@section('title', 'Departments')
@section('heading', 'Departments')

@section('content')
<div class="mb-4">
    <a href="{{ route('department.create') }}" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add Department</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Branch</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($departments as $dept)
            <tr>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100">{{ $dept->name }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $dept->branch->name ?? '—' }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('department.edit', $dept) }}" class="wise-link hover:underline">Edit</a>
                    <form method="POST" action="{{ route('department.destroy', $dept) }}" class="inline ml-2" onsubmit="return confirm('Delete this department?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No departments yet. Add one to assign employees.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($departments->hasPages())
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
        {{ $departments->links() }}
    </div>
    @endif
</div>
@endsection

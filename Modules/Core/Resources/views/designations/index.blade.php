@extends('core::layouts.app')

@section('title', 'Designations')
@section('heading', 'Designations')

@section('content')
<div class="mb-4">
    <a href="{{ route('designation.create') }}" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add Designation</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Level</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Employees</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($designations as $des)
            <tr>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100">{{ $des->name }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $des->level }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $des->employees_count }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('designation.edit', $des) }}" class="wise-link hover:underline">Edit</a>
                    <form method="POST" action="{{ route('designation.destroy', $des) }}" class="inline ml-2" onsubmit="return confirm('Delete this designation?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No designations yet. Add job titles (e.g. IT Manager, HR Officer).</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($designations->hasPages())
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
        {{ $designations->links() }}
    </div>
    @endif
</div>
@endsection

@extends('core::layouts.app')

@section('title', $project->name)
@section('heading', $project->name)

@section('content')
<div class="mb-4 flex flex-wrap gap-2">
    <a href="{{ route('projects.index') }}" class="wise-link hover:underline">← Projects</a>
    <a href="{{ route('projects.edit', $project) }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-sm">Edit</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl mb-6">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Code</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $project->code ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Branch</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $project->branch->name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Budget</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $project->budget ? number_format($project->budget, 2) : '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Status</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ ucfirst($project->status ?? 'active') }}</dd></div>
        @if($project->description)
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500 dark:text-slate-400">Description</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $project->description }}</dd></div>
        @endif
    </dl>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl">
    <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-4">Assigned employees</h3>
    <form method="POST" action="{{ route('projects.employees.attach', $project) }}" class="flex flex-wrap gap-2 mb-4">
        @csrf
        <select name="employee_id" required class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm min-w-52">
            <option value="">Select employee</option>
            @foreach($employees->whereNotIn('id', $project->employees->pluck('id')) as $e)
                <option value="{{ $e->id }}">{{ $e->full_name }}</option>
            @endforeach
        </select>
        <input type="text" name="role" placeholder="Role (optional)" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
        <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg text-sm">Assign</button>
    </form>
    @if($project->employees->isNotEmpty())
    <ul class="space-y-2">
        @foreach($project->employees as $e)
        <li class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700/50">
            <span><a href="{{ route('employee.show', $e) }}" class="wise-link">{{ $e->full_name }}</a>@if($e->pivot->role) <span class="text-slate-500 dark:text-slate-400 text-sm">({{ $e->pivot->role }})</span>@endif</span>
            <form method="POST" action="{{ route('projects.employees.detach', [$project, $e]) }}" class="inline" onsubmit="return confirm('Remove from project?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-600 dark:text-red-400">Remove</button>
            </form>
        </li>
        @endforeach
    </ul>
    @else
    <p class="text-slate-500 dark:text-slate-400 text-sm">No employees assigned. Use the form above to assign.</p>
    @endif
</div>
@endsection

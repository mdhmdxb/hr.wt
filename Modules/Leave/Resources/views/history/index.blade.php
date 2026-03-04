@extends('core::layouts.app')

@section('title', 'Leave history')
@section('heading', 'Leave history')

@section('content')
<p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Manually add past leave taken by employees (e.g. before using Wise HRM). This is for record-keeping only; it does not affect current leave balance.</p>
<div class="mb-4 flex flex-wrap items-center gap-4">
    <a href="{{ route('leave.history.create', request()->only('employee_id')) }}" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add leave history</a>
    <form method="GET" action="{{ route('leave.history.index') }}" class="flex flex-wrap gap-2 items-end">
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-0.5">Employee</label>
            <select name="employee_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                <option value="">All</option>
                @foreach($employees as $e)
                    <option value="{{ $e->id }}" {{ request('employee_id') == $e->id ? 'selected' : '' }}>{{ $e->full_name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm">Filter</button>
    </form>
</div>
@if(session('success'))
    <p class="mb-4 text-sm text-green-600 dark:text-green-400">{{ session('success') }}</p>
@endif
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Employee</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Type</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Start</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">End</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Days</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Notes</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($history as $h)
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                <td class="px-4 py-3 text-sm text-slate-800 dark:text-slate-200">{{ $h->employee->full_name ?? '—' }}</td>
                <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300">{{ $h->leaveType->name ?? '—' }}</td>
                <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300">{{ $h->start_date->format('Y-m-d') }}</td>
                <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300">{{ $h->end_date->format('Y-m-d') }}</td>
                <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300">{{ $h->days }}</td>
                <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400 max-w-xs truncate">{{ $h->notes ?? '—' }}</td>
                <td class="px-4 py-3 text-right">
                    <form method="POST" action="{{ route('leave.history.destroy', $h) }}" class="inline" onsubmit="return confirm('Remove this leave history entry?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 dark:text-red-400 text-sm hover:underline">Remove</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No leave history entries yet. Add one to record past leave.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($history->hasPages())
    <div class="mt-4">{{ $history->links() }}</div>
@endif
<div class="mt-4">
    <a href="{{ route('leave.index') }}" class="wise-link text-sm">Back to Leave</a>
</div>
@endsection

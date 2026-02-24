@extends('core::layouts.app')

@section('title', 'Leave report')
@section('heading', 'Leave report')

@section('content')
<div class="mb-4 flex flex-wrap items-center gap-4">
    <a href="{{ route('reports.index') }}" class="wise-link hover:underline">← Reports</a>
</div>
<form method="GET" action="{{ route('reports.leave') }}" class="mb-6 p-4 bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200 dark:border-slate-700 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-0.5">Employee</label>
        <select name="employee_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
            <option value="">All</option>
            @foreach($employees as $e)
                <option value="{{ $e->id }}" {{ request('employee_id') == $e->id ? 'selected' : '' }}>{{ $e->full_name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-0.5">Status</label>
        <select name="status" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
            <option value="">All</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </div>
    <div>
        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-0.5">From</label>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
    </div>
    <div>
        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-0.5">To</label>
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
    </div>
    <button type="submit" class="px-3 py-1.5 wise-btn text-white rounded-lg text-sm">Apply</button>
</form>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Employee</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Type</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Start</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">End</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Days</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($records as $r)
            <tr>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100">{{ $r->employee->full_name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $r->leaveType->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $r->start_date->format('Y-m-d') }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $r->end_date->format('Y-m-d') }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $r->days }}</td>
                <td class="px-4 py-3">
                    @if($r->status === 'pending')<span class="px-2 py-0.5 text-xs rounded bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Pending</span>
                    @elseif($r->status === 'approved')<span class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Approved</span>
                    @else<span class="px-2 py-0.5 text-xs rounded bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Rejected</span>@endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No records match the filters.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($records->hasPages())
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">{{ $records->links() }}</div>
    @endif
</div>
@endsection

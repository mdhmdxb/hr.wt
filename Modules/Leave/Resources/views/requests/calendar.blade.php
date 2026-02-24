@extends('core::layouts.app')

@section('title', 'Leave calendar')
@section('heading', 'Leave calendar')

@section('content')
<div class="mb-4 flex flex-wrap items-center gap-4">
    <a href="{{ route('leave.index') }}" class="wise-link hover:underline">← Back to Leave</a>
    <form method="GET" action="{{ route('leave.calendar') }}" class="flex flex-wrap items-center gap-2">
        <select name="month" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate(2000, $m, 1)->format('F') }}</option>
            @endfor
        </select>
        <select name="year" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
            @for($y = now()->year + 1; $y >= now()->year - 2; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <select name="employee_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
            <option value="">All employees</option>
            @foreach($employees as $e)
                <option value="{{ $e->id }}" {{ request('employee_id') == $e->id ? 'selected' : '' }}>{{ $e->full_name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-3 py-1.5 wise-btn text-white rounded-lg text-sm">Apply</button>
    </form>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <div class="p-4 border-b border-slate-200 dark:border-slate-700">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ $start->format('F Y') }}</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Employee</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Leave type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Start</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">End</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Days</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($leaveRequests as $lr)
                <tr>
                    <td class="px-4 py-3 text-slate-900 dark:text-slate-100">{{ $lr->employee->full_name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs rounded" style="background:{{ $lr->leaveType->color ?? '#94a3b8' }}20;color:{{ $lr->leaveType->color ?? '#64748b' }}">{{ $lr->leaveType->name ?? '—' }}</span>
                    </td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $lr->start_date->format('M j, Y') }}</td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $lr->end_date->format('M j, Y') }}</td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $lr->days }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('leave.show', $lr) }}" class="wise-link hover:underline text-sm">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No approved leave in this period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

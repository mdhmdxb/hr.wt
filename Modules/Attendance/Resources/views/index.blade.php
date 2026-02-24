@extends('core::layouts.app')

@section('title', 'Attendance')
@section('heading', 'Attendance')

@section('content')
<div class="mb-4 flex flex-wrap items-center gap-4">
    <a href="{{ route('attendance.batch') }}" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Batch entry (by month)</a>
    <a href="{{ route('attendance.create') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">Single entry</a>
    <form method="GET" action="{{ route('attendance.index') }}" class="flex flex-wrap gap-2 items-end">
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
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-0.5">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-0.5">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
        </div>
        <button type="submit" class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm">Filter</button>
    </form>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Date</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Employee</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Check In</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Check Out</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Hours</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">OT</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($attendances as $a)
            <tr>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100">{{ $a->date->format('Y-m-d') }}</td>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100">{{ $a->employee->full_name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $a->check_in_at ? \Carbon\Carbon::parse($a->check_in_at)->format('H:i') : '—' }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $a->check_out_at ? \Carbon\Carbon::parse($a->check_out_at)->format('H:i') : '—' }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $a->total_hours !== null ? $a->total_hours . 'h' : '—' }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $a->overtime_minutes !== null ? $a->overtime_minutes . 'm' : '—' }}</td>
                <td class="px-4 py-3">
                    @if($a->isLocked())<span class="text-amber-600 dark:text-amber-400" title="Locked">🔒</span> @endif
                    @php
                    $statusClass = match($a->status) {
                        'present' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                        'absent' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                        'weekly_off', 'alt_saturday_off' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                        default => 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300',
                    };
                @endphp
                    <span class="px-2 py-1 text-xs rounded {{ $statusClass }}">{{ \Modules\Attendance\Models\Attendance::statusOptions()[$a->status] ?? $a->status }}</span>
                </td>
                <td class="px-4 py-3 text-right">
                    @if($a->attachment_path)<a href="{{ route('attendance.attachment.download', $a) }}" class="wise-link hover:underline text-sm">File</a> @endif
                    <a href="{{ route('attendance.edit', $a) }}" class="wise-link hover:underline">Edit</a>
                    <form method="POST" action="{{ route('attendance.destroy', $a) }}" class="inline ml-2" onsubmit="return confirm('Delete this record?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No attendance records. Record attendance to get started.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($attendances->hasPages())
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
        {{ $attendances->links() }}
    </div>
    @endif
</div>
@endsection

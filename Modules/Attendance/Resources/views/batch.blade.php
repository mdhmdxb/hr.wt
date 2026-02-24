@extends('core::layouts.app')

@section('title', 'Batch Attendance')
@section('heading', 'Batch attendance entry')

@section('content')
<div class="space-y-6">
    {{-- Selector --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6">
        <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Select month & employee</h2>
        <form method="GET" action="{{ route('attendance.batch') }}" class="flex flex-wrap items-end gap-4">
            <div class="min-w-52">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Employee</label>
                <select name="employee_id" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <option value="">— Select —</option>
                    @foreach($employees as $e)
                        <option value="{{ $e->id }}" {{ ($employeeId ?? '') == $e->id ? 'selected' : '' }}>{{ $e->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Month</label>
                <select name="month" class="rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ ($month ?? Carbon\Carbon::now()->month) == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate(2000, $m, 1)->format('F') }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Year</label>
                <select name="year" class="rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @for($y = now()->year + 1; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ ($year ?? Carbon\Carbon::now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-xl font-medium">Load</button>
        </form>
    </div>

    @if($employee && $employeeId)
    {{-- Batch grid --}}
    <form method="POST" action="{{ route('attendance.batch.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="employee_id" value="{{ $employeeId }}">
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-4">
                <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100">
                    {{ $employee->full_name }} — {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
                </h2>
                <button type="submit" class="px-5 py-2.5 wise-btn text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all">Save batch</button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-32 whitespace-nowrap">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-20">Day</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-28">Check in</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-28">Check out</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-32">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-44 max-w-48">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $date = \Carbon\Carbon::createFromDate($year, $month, $day);
                                $dateStr = $date->format('Y-m-d');
                                $rec = $existing[$dateStr] ?? null;
                                $isWeekend = $date->isWeekend();
                                if ($rec) {
                                    $defaultStatus = $rec->status;
                                } elseif ($employee->isWeeklyOffDay($date)) {
                                    $defaultStatus = 'weekly_off';
                                } elseif ($employee->isAlternateSaturdayOffDay($date)) {
                                    $defaultStatus = 'alt_saturday_off';
                                } else {
                                    $defaultStatus = 'present';
                                }
                            @endphp
                            <tr class="{{ $isWeekend ? 'bg-slate-50/50 dark:bg-slate-800/50' : '' }}">
                                <td class="px-4 py-2 text-slate-700 dark:text-slate-300 text-sm font-medium whitespace-nowrap">{{ $dateStr }}</td>
                                <td class="px-4 py-2 text-slate-500 dark:text-slate-400 text-sm">{{ $date->format('D') }}</td>
                                <td class="px-4 py-2">
                                    <input type="hidden" name="attendance[{{ $day }}][date]" value="{{ $dateStr }}">
                                    <input type="time" name="attendance[{{ $day }}][check_in_at]" value="{{ $rec && $rec->check_in_at ? \Carbon\Carbon::parse($rec->check_in_at)->format('H:i') : ($employee->shift_start ?? '') }}"
                                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="time" name="attendance[{{ $day }}][check_out_at]" value="{{ $rec && $rec->check_out_at ? \Carbon\Carbon::parse($rec->check_out_at)->format('H:i') : ($employee->shift_end ?? '') }}"
                                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                                </td>
                                <td class="px-4 py-2">
                                    <select name="attendance[{{ $day }}][status]" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                                        @foreach(\Modules\Attendance\Models\Attendance::statusOptions() as $val => $label)
                                            <option value="{{ $val }}" {{ $defaultStatus === $val ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" name="attendance[{{ $day }}][notes]" value="{{ $rec ? $rec->notes : '' }}" placeholder="Note..."
                                        class="w-full max-w-44 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </form>
    @else
    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 text-center">
        <p class="text-slate-500 dark:text-slate-400">Select an employee, month and year, then click <strong>Load</strong> to edit attendance for that month.</p>
    </div>
    @endif
</div>

@endsection

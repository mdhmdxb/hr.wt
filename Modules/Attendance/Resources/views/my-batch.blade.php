@extends('core::layouts.app')

@section('title', 'My Attendance')
@section('heading', 'My attendance')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 print-area">
        <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Select month</h2>
        <form method="GET" action="{{ route('attendance.my') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Month</label>
                <select name="month" class="rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ ($month ?? now()->month) == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate(2000, $m, 1)->format('F') }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Year</label>
                <select name="year" class="rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @for($y = now()->year + 1; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ ($year ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-xl font-medium">Load</button>
        </form>
    </div>

    @if($employee && $employeeId)
        @php [$workedHours, $otWorkedHours, $acceptedOtHours] = $hoursSummary ?? [0, 0, 0]; @endphp
        @if($isLocked)
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 mb-4">
            <p class="text-amber-800 dark:text-amber-200 text-sm font-medium">This month is submitted and locked. You cannot edit unless HR grants you edit permission. Contact HR if you need to make changes.</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100">{{ $employee->full_name }} — {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }} (read-only)</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Worked hours: <span class="font-semibold">{{ number_format($workedHours, 2) }}</span> h ·
                        Overtime worked: <span class="font-semibold">{{ number_format($otWorkedHours, 2) }}</span> h ·
                        Accepted overtime (full hours only): <span class="font-semibold">{{ $acceptedOtHours }}</span> h
                    </p>
                </div>
                <button type="button" onclick="window.print()" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 print:hidden">
                    Print
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Day</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Check in</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Check out</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @php $holidayDates = $publicHolidayDates ?? []; $holidayNames = $publicHolidayNames ?? []; @endphp
                        @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $date = \Carbon\Carbon::createFromDate($year, $month, $day);
                                $dateStr = $date->format('Y-m-d');
                                $rec = $existing[$dateStr] ?? null;
                                $statusLabel = $rec ? (\Modules\Attendance\Models\Attendance::statusOptions()[$rec->status] ?? $rec->status) : '—';
                            @endphp
                            <tr>
                                <td class="px-4 py-2 text-slate-700 dark:text-slate-300 text-sm">{{ $dateStr }}</td>
                                <td class="px-4 py-2 text-slate-500 dark:text-slate-400 text-sm">{{ $date->format('D') }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300 text-sm">{{ $rec && $rec->check_in_at ? \Carbon\Carbon::parse($rec->check_in_at)->format('H:i') : '—' }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300 text-sm">{{ $rec && $rec->check_out_at ? \Carbon\Carbon::parse($rec->check_out_at)->format('H:i') : '—' }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300 text-sm">{{ $statusLabel }}</td>
                                <td class="px-4 py-2 text-slate-500 dark:text-slate-400 text-sm">{{ $rec && $rec->notes ? $rec->notes : '—' }}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
        @else
        {{-- Editable form: Save + Submit --}}
        <form method="POST" action="{{ route('attendance.my.store') }}" class="space-y-4" id="my-attendance-form">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100">{{ $employee->full_name }} — {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Save your changes, then click Submit to lock this month. After submission you cannot edit unless HR allows it.</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Worked hours so far: <span class="font-semibold">{{ number_format($workedHours, 2) }}</span> h ·
                            Overtime worked: <span class="font-semibold">{{ number_format($otWorkedHours, 2) }}</span> h ·
                            Accepted overtime (full hours only): <span class="font-semibold">{{ $acceptedOtHours }}</span> h
                        </p>
                    </div>
                    <div class="flex gap-2 items-center">
                        <button type="button" onclick="window.print()" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 print:hidden">
                            Print
                        </button>
                        <button type="submit" class="px-5 py-2.5 wise-btn text-white rounded-xl font-medium">Save</button>
                        <button type="button" onclick="document.getElementById('submit-month-form').submit()" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-medium">Submit month</button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-32">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-20">Day</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-28">Check in</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-28">Check out</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-32">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-44">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @php $holidayDates = $publicHolidayDates ?? []; $holidayNames = $publicHolidayNames ?? []; @endphp
                            @for($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $date = \Carbon\Carbon::createFromDate($year, $month, $day);
                                    $dateStr = $date->format('Y-m-d');
                                    $rec = $existing[$dateStr] ?? null;
                                    if ($rec) { $defaultStatus = $rec->status; } elseif (in_array($dateStr, $holidayDates)) { $defaultStatus = 'holiday'; } elseif ($employee->isWeeklyOffDay($date)) { $defaultStatus = 'weekly_off'; } elseif ($employee->isAlternateSaturdayOffDay($date)) { $defaultStatus = 'alt_saturday_off'; } else { $defaultStatus = 'present'; }
                                    $rowClass = $defaultStatus === 'holiday' ? 'bg-amber-50 dark:bg-amber-900/20' : ($defaultStatus === 'weekly_off' ? 'bg-slate-100 dark:bg-slate-800/70' : ($defaultStatus === 'alt_saturday_off' ? 'bg-blue-50 dark:bg-blue-900/20' : ''));
                                    $holidayName = $holidayNames[$dateStr] ?? null;
                                    $checkInVal = $rec && $rec->check_in_at ? \Carbon\Carbon::parse($rec->check_in_at)->format('H:i') : ($employee->shift_start ?? '');
                                    $checkOutVal = $rec && $rec->check_out_at ? \Carbon\Carbon::parse($rec->check_out_at)->format('H:i') : ($employee->shift_end ?? '');
                                @endphp
                                <tr class="{{ $rowClass }}" x-data="{ status: '{{ $defaultStatus }}' }">
                                    <td class="px-4 py-2 text-slate-700 dark:text-slate-300 text-sm font-medium whitespace-nowrap">{{ $dateStr }}</td>
                                    <td class="px-4 py-2 text-slate-500 dark:text-slate-400 text-sm">{{ $date->format('D') }}@if($holidayName) <span class="text-amber-600 dark:text-amber-400" title="{{ $holidayName }}">★</span>@endif</td>
                                    <td class="px-4 py-2">
                                        <input type="hidden" name="attendance[{{ $day }}][date]" value="{{ $dateStr }}">
                                        <span class="text-slate-400 dark:text-slate-500 text-sm" x-show="['weekly_off', 'alt_saturday_off', 'holiday'].includes(status)" x-cloak>—</span>
                                        <input type="hidden" :name="['weekly_off', 'alt_saturday_off', 'holiday'].includes(status) ? 'attendance[{{ $day }}][check_in_at]' : ''" value="">
                                        <input type="time" :name="!['weekly_off', 'alt_saturday_off', 'holiday'].includes(status) ? 'attendance[{{ $day }}][check_in_at]' : ''" value="{{ $checkInVal }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm" x-show="!['weekly_off', 'alt_saturday_off', 'holiday'].includes(status)">
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="text-slate-400 dark:text-slate-500 text-sm" x-show="['weekly_off', 'alt_saturday_off', 'holiday'].includes(status)" x-cloak>—</span>
                                        <input type="hidden" :name="['weekly_off', 'alt_saturday_off', 'holiday'].includes(status) ? 'attendance[{{ $day }}][check_out_at]' : ''" value="">
                                        <input type="time" :name="!['weekly_off', 'alt_saturday_off', 'holiday'].includes(status) ? 'attendance[{{ $day }}][check_out_at]' : ''" value="{{ $checkOutVal }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm" x-show="!['weekly_off', 'alt_saturday_off', 'holiday'].includes(status)">
                                    </td>
                                    <td class="px-4 py-2">
                                        <select name="attendance[{{ $day }}][status]" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm" x-model="status">
                                            @foreach(\Modules\Attendance\Models\Attendance::statusOptions() as $val => $label)
                                                <option value="{{ $val }}" {{ $defaultStatus === $val ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="text" name="attendance[{{ $day }}][notes]" value="{{ $rec ? $rec->notes : '' }}" placeholder="Note..." class="w-full max-w-44 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
        <form id="submit-month-form" method="POST" action="{{ route('attendance.my.submit') }}" class="hidden" onsubmit="return confirm('Submit this month? You will not be able to edit unless HR allows it.');">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
        </form>
        @endif
    @else
    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 text-center">
        <p class="text-slate-500 dark:text-slate-400">Select month and year, then click <strong>Load</strong> to view or edit your attendance.</p>
    </div>
    @endif
</div>
@endsection

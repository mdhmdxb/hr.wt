@extends('core::layouts.app')

@section('title', 'Edit Attendance')
@section('heading', 'Edit Attendance')

@section('content')
@if($attendance->isLocked() && (auth()->user()->isAdmin() || auth()->user()->isHr()))
<div class="mb-4 p-3 bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 rounded-lg flex items-center justify-between">
    <span class="text-sm">This record is locked. Unlock to allow editing (it will lock again on save).</span>
    <form method="POST" action="{{ route('attendance.unlock', $attendance) }}" class="inline">
        @csrf
        <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-200 dark:bg-amber-800 text-amber-900 dark:text-amber-100 text-sm font-medium">Unlock</button>
    </form>
</div>
@endif
<form method="POST" action="{{ route('attendance.update', $attendance) }}" enctype="multipart/form-data" class="max-w-xl space-y-4">
    @csrf
    @method('PUT')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Employee</label>
            <select name="employee_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @foreach($employees as $e)
                    <option value="{{ $e->id }}" {{ old('employee_id', $attendance->employee_id) == $e->id ? 'selected' : '' }}>{{ $e->full_name }} ({{ $e->employee_code }})</option>
                @endforeach
            </select>
            @error('employee_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Date</label>
            <input type="date" name="date" value="{{ old('date', $attendance->date->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" title="Future dates are not allowed">
            @error('date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Check In (time)</label>
                <input type="time" name="check_in_at" value="{{ old('check_in_at', $attendance->check_in_at ? \Carbon\Carbon::parse($attendance->check_in_at)->format('H:i') : '') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Check Out (time)</label>
                <input type="time" name="check_out_at" value="{{ old('check_out_at', $attendance->check_out_at ? \Carbon\Carbon::parse($attendance->check_out_at)->format('H:i') : '') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status</label>
            <select name="status" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @foreach(\Modules\Attendance\Models\Attendance::statusOptions() as $val => $label)
                    <option value="{{ $val }}" {{ old('status', $attendance->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Overtime (minutes)</label>
            <input type="number" name="overtime_minutes" value="{{ old('overtime_minutes', $attendance->overtime_minutes) }}" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Attachment</label>
            @if($attendance->attachment_path)
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-1"><a href="{{ route('attendance.attachment.download', $attendance) }}" class="wise-link">Download current file</a></p>
            @endif
            <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Leave empty to keep current. Max 5 MB.</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Notes</label>
            <textarea name="notes" rows="2" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">{{ old('notes', $attendance->notes) }}</textarea>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Update</button>
            <a href="{{ route('attendance.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        </div>
    </div>
</form>
@endsection

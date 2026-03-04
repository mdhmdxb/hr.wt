@extends('core::layouts.app')

@section('title', 'Add leave history')
@section('heading', 'Add leave history')

@section('content')
<p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Record past leave taken by an employee (before using the system). This does not change their current remaining leave balance.</p>
<form method="POST" action="{{ route('leave.history.store') }}" class="max-w-xl space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Employee *</label>
        <select name="employee_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            <option value="">— Select —</option>
            @foreach($employees as $e)
                <option value="{{ $e->id }}" {{ old('employee_id', $preselectedEmployeeId) == $e->id ? 'selected' : '' }}>{{ $e->full_name }}</option>
            @endforeach
        </select>
        @error('employee_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Leave type *</label>
        <select name="leave_type_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            <option value="">— Select —</option>
            @foreach($leaveTypes as $t)
                <option value="{{ $t->id }}" {{ old('leave_type_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
            @endforeach
        </select>
        @error('leave_type_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Start date *</label>
            <input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            @error('start_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">End date *</label>
            <input type="date" name="end_date" value="{{ old('end_date') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            @error('end_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Days *</label>
        <input type="number" step="0.5" min="0" name="days" value="{{ old('days') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" placeholder="e.g. 5">
        @error('days')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Notes</label>
        <textarea name="notes" rows="2" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" placeholder="Optional">{{ old('notes') }}</textarea>
        @error('notes')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div class="flex gap-3">
        <a href="{{ route('leave.history.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        <button type="submit" class="px-6 py-2 wise-btn text-white rounded-lg">Add entry</button>
    </div>
</form>
@endsection

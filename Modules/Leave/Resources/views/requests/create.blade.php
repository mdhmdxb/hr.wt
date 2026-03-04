@extends('core::layouts.app')

@section('title', 'Submit Leave Request')
@section('heading', 'Submit Leave Request')

@section('content')
<form method="POST" action="{{ route('leave.store') }}" class="max-w-xl space-y-4" enctype="multipart/form-data">
    @csrf
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Employee</label>
            <select name="employee_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <option value="">Select employee</option>
                @foreach($employees as $e)
                    <option value="{{ $e->id }}" {{ old('employee_id') == $e->id ? 'selected' : '' }}>{{ $e->full_name }} ({{ $e->employee_code }})</option>
                @endforeach
            </select>
            @error('employee_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Leave type</label>
            <select name="leave_type_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @foreach($leaveTypes as $lt)
                    <option value="{{ $lt->id }}" {{ old('leave_type_id') == $lt->id ? 'selected' : '' }}>{{ $lt->name }} ({{ $lt->days_per_year }} days/year)</option>
                @endforeach
            </select>
            @error('leave_type_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Start date</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @error('start_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">End date</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @error('end_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Reason (optional)</label>
            <textarea name="reason" rows="2" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">{{ old('reason') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Supporting document</label>
            <input type="file" name="document" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">If the selected leave type requires proof (e.g. medical certificate), please upload it here.</p>
            @error('document')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Submit Request</button>
            <a href="{{ route('leave.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        </div>
    </div>
</form>
@endsection

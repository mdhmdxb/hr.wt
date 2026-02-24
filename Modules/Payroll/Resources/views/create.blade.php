@extends('core::layouts.app')

@section('title', 'New Payroll Run')
@section('heading', 'New Payroll Run')

@section('content')
<form method="POST" action="{{ route('payroll.store') }}" class="max-w-xl space-y-4">
    @csrf
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <p class="text-sm text-slate-600 dark:text-slate-400">Creates a payroll run for the period and generates one payslip per active employee (using their basic salary). You can edit payslips after creation.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Period start</label>
                <input type="date" name="period_start" value="{{ old('period_start') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @error('period_start')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Period end</label>
                <input type="date" name="period_end" value="{{ old('period_end') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @error('period_end')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Create & Generate Payslips</button>
            <a href="{{ route('payroll.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        </div>
    </div>
</form>
@endsection

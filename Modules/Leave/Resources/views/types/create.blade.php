@extends('core::layouts.app')

@section('title', 'Add Leave Type')
@section('heading', 'Add Leave Type')

@section('content')
<form method="POST" action="{{ route('leave.types.store') }}" class="max-w-xl space-y-4">
    @csrf
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Annual Leave" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Days per year</label>
            <input type="number" name="days_per_year" value="{{ old('days_per_year', 0) }}" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="carry_over" value="1" id="carry_over" {{ old('carry_over') ? 'checked' : '' }} class="rounded border-slate-300">
            <label for="carry_over" class="text-sm text-slate-700 dark:text-slate-300">Allow carry over to next year</label>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Color (e.g. #3b82f6)</label>
            <input type="text" name="color" value="{{ old('color') }}" placeholder="#3b82f6" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_paid" value="1" id="is_paid" {{ old('is_paid', true) ? 'checked' : '' }} class="rounded border-slate-300">
            <label for="is_paid" class="text-sm text-slate-700 dark:text-slate-300">Paid leave</label>
        </div>
        @php
            $rawWf = old('workflow_steps');
            $wfSteps = is_array($rawWf) && isset($rawWf[0]) && is_array($rawWf[0]) ? array_column($rawWf, 'approver') : (is_array($rawWf) ? $rawWf : []);
            @endphp
        <div class="border-t border-slate-200 dark:border-slate-600 pt-4">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Approval workflow (order matters)</label>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Leave empty for single HR approval. Add steps for hierarchical approval.</p>
            @foreach([1,2,3,4] as $i)
            <div class="flex items-center gap-2 mb-2">
                <span class="text-sm text-slate-500 w-16">Step {{ $i }}</span>
                <select name="workflow_steps[]" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm flex-1">
                    <option value="">— None —</option>
                    @foreach(\Modules\Leave\Models\LeaveApprovalStep::approverTypeOptions() as $val => $label)
                        <option value="{{ $val }}" {{ ($wfSteps[$i-1] ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @endforeach
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Create</button>
            <a href="{{ route('leave.types.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        </div>
    </div>
</form>
@endsection

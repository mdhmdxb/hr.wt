@extends('core::layouts.app')

@section('title', 'Add document')
@section('heading', 'Add document')

@section('content')
@if(isset($renewalOf) && $renewalOf)
<div class="mb-4 p-3 bg-slate-100 dark:bg-slate-700/50 rounded-lg text-sm text-slate-700 dark:text-slate-300">
    Adding a <strong>renewal</strong> for: {{ $renewalOf->employee->full_name }} – {{ \Modules\Core\Models\EmployeeDocument::typeOptions()[$renewalOf->type] ?? $renewalOf->type }}. Previous expiry: {{ $renewalOf->expiry_date?->format('Y-m-d') ?? '—' }}. All versions are kept.
</div>
@endif
<form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="max-w-xl space-y-4">
    @csrf
    @if(isset($renewalOf) && $renewalOf)
    <input type="hidden" name="renewal_of_id" value="{{ $renewalOf->id }}">
    @endif
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Employee *</label>
            <select name="employee_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" @if(isset($renewalOf) && $renewalOf) readonly @endif>
                <option value="">Select employee</option>
                @foreach($employees as $e)
                    <option value="{{ $e->id }}" {{ old('employee_id', $selectedEmployee?->id ?? $renewalOf?->employee_id) == $e->id ? 'selected' : '' }}>{{ $e->full_name }}</option>
                @endforeach
            </select>
            @if(isset($renewalOf) && $renewalOf)<input type="hidden" name="employee_id" value="{{ $renewalOf->employee_id }}">@endif
            @error('employee_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Type *</label>
            <select name="type" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @foreach(\Modules\Core\Models\EmployeeDocument::typeOptions() as $val => $label)
                    <option value="{{ $val }}" {{ old('type', $renewalOf?->type) == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Title (optional)</label>
            <input type="text" name="title" value="{{ old('title', $renewalOf?->title) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" placeholder="e.g. Passport copy">
            @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">File *</label>
            <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">PDF, image, or document. Max 10 MB.</p>
            @error('file')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Issue date (optional)</label>
                <input type="date" name="issue_date" value="{{ old('issue_date') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @error('issue_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Expiry date (optional)</label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @error('expiry_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Notes (optional)</label>
            <textarea name="notes" rows="2" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">{{ old('notes') }}</textarea>
            @error('notes')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Upload</button>
            <a href="{{ route('documents.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        </div>
    </div>
</form>
@endsection

@extends('core::layouts.app')

@section('title', 'Add Designation')
@section('heading', 'Add Designation')

@section('content')
<form method="POST" action="{{ route('designation.store') }}" class="max-w-xl space-y-4">
    @csrf
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Designation name (job title)</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. IT Manager, HR Officer, Site Engineer"
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                Level (optional, for hierarchy)
            </label>
            <input type="number" name="level" value="{{ old('level', 0) }}" min="0"
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Lower number = more senior. Example: CEO 1, Director 2, Manager 3, Supervisor 4, Staff 5.
                This ordering is used when listing designations and can be used in reports/approvals.
            </p>
            @error('level')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Create Designation</button>
            <a href="{{ route('designation.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        </div>
    </div>
</form>
@endsection

@extends('core::layouts.app')

@section('title', 'Edit Designation')
@section('heading', 'Edit Designation')

@section('content')
<form method="POST" action="{{ route('designation.update', $designation) }}" class="max-w-xl space-y-4">
    @csrf
    @method('PUT')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Designation name (job title)</label>
            <input type="text" name="name" value="{{ old('name', $designation->name) }}" required
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Level (optional, for hierarchy)</label>
            <input type="number" name="level" value="{{ old('level', $designation->level) }}" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Lower number = more senior. Example: CEO 1, Director 2, Manager 3, Supervisor 4, Staff 5.
                Used for ordering and understanding reporting lines.
            </p>
            @error('level')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Update Designation</button>
            <a href="{{ route('designation.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        </div>
    </div>
</form>
@endsection

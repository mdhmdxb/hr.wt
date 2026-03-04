@extends('core::layouts.app')

@section('title', 'Add Public Holiday')
@section('heading', 'Add Public Holiday')

@section('content')
<form method="POST" action="{{ route('holidays.store') }}" class="max-w-xl space-y-4">
    @csrf
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. National Day"
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Start date</label>
                <input type="date" name="date" value="{{ old('date') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @error('date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">End date (optional)</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Leave empty for a single-day holiday.</p>
                @error('end_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Country (optional)</label>
            <input type="text" name="country_code" value="{{ old('country_code') }}" placeholder="e.g. AE" maxlength="10"
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('holidays.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        <button type="submit" class="px-6 py-2 wise-btn text-white rounded-lg">Add Holiday</button>
    </div>
</form>
@endsection

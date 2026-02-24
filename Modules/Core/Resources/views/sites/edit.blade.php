@extends('core::layouts.app')

@section('title', 'Edit Site')
@section('heading', 'Edit Site')

@section('content')
<form method="POST" action="{{ route('site.update', $site) }}" class="max-w-xl space-y-4">
    @csrf
    @method('PUT')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Branch</label>
            <select name="branch_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ old('branch_id', $site->branch_id) == $b->id ? 'selected' : '' }}>{{ $b->name }} ({{ $b->company->name ?? '' }})</option>
                @endforeach
            </select>
            @error('branch_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Site name</label>
            <input type="text" name="name" value="{{ old('name', $site->name) }}" required
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Address</label>
            <textarea name="address" rows="2" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">{{ old('address', $site->address) }}</textarea>
            @error('address')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="border-t border-slate-200 dark:border-slate-600 pt-4 mt-4">
            <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Default work schedule &amp; allowances (optional)</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Override branch defaults for this site when adding employees.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Default shift start</label>
                    <input type="time" name="default_shift_start" value="{{ old('default_shift_start', $site->default_shift_start) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Default shift end</label>
                    <input type="time" name="default_shift_end" value="{{ old('default_shift_end', $site->default_shift_end) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Default accommodation</label>
                    <input type="number" step="0.01" min="0" name="default_accommodation" value="{{ old('default_accommodation', $site->default_accommodation) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Default transportation</label>
                    <input type="number" step="0.01" min="0" name="default_transportation" value="{{ old('default_transportation', $site->default_transportation) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Default food allowance</label>
                    <input type="number" step="0.01" min="0" name="default_food_allowance" value="{{ old('default_food_allowance', $site->default_food_allowance) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Default other allowances</label>
                    <input type="number" step="0.01" min="0" name="default_other_allowances" value="{{ old('default_other_allowances', $site->default_other_allowances) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Update Site</button>
            <a href="{{ route('site.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        </div>
    </div>
</form>
@endsection

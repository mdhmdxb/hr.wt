@extends('core::layouts.app')

@section('title', 'Add Department')
@section('heading', 'Add Department')

@section('content')
<form method="POST" action="{{ route('department.store') }}" class="max-w-xl space-y-4">
    @csrf
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Branch</label>
            <select name="branch_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}@if($b->company) ({{ $b->company->name }})@endif</option>
                @endforeach
            </select>
            @error('branch_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Department name</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. HR, IT, Finance"
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Create Department</button>
            <a href="{{ route('department.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        </div>
    </div>
</form>
@endsection

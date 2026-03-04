@extends('core::layouts.app')

@section('title', 'Public Holidays')
@section('heading', 'Public Holidays')

@section('content')
<div class="mb-4 flex flex-wrap items-center gap-4">
    <a href="{{ route('holidays.create') }}" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add Holiday</a>
    <form method="GET" action="{{ route('holidays.index') }}" class="flex items-center gap-2">
        <label class="text-sm text-slate-600 dark:text-slate-400">Year</label>
        <select name="year" onchange="this.form.submit()" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
            @for($y = now()->year + 2; $y >= now()->year - 2; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </form>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Date range</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Days</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Name</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($holidays as $h)
            <tr>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100 font-medium">{{ $h->date->format('Y-m-d (l)') }}{!! $h->end_date ? ' – ' . $h->end_date->format('Y-m-d (l)') : '' !!}</td>
                <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $h->end_date ? $h->date->diffInDays($h->end_date) + 1 : 1 }}</td>
                <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $h->name }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('holidays.edit', $h) }}" class="wise-link hover:underline">Edit</a>
                    <form method="POST" action="{{ route('holidays.destroy', $h) }}" class="inline ml-2" onsubmit="return confirm('Delete this holiday?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No public holidays for {{ $year }}. Add one to mark company-wide off days.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

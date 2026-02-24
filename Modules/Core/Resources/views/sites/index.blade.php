@extends('core::layouts.app')

@section('title', 'Sites')
@section('heading', 'Sites')

@section('content')
<div class="mb-4">
    <a href="{{ route('site.create') }}" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add Site</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Branch</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Company</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($sites as $site)
            <tr>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100">{{ $site->name }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $site->branch->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $site->branch->company->name ?? '—' }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('site.edit', $site) }}" class="wise-link hover:underline">Edit</a>
                    <form method="POST" action="{{ route('site.destroy', $site) }}" class="inline ml-2" onsubmit="return confirm('Delete this site?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No sites yet. Add one per branch for site-based rules.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($sites->hasPages())
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
        {{ $sites->links() }}
    </div>
    @endif
</div>
@endsection

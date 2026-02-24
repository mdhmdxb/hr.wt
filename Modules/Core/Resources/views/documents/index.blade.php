@extends('core::layouts.app')

@section('title', 'Document vault')
@section('heading', 'Document vault')

@section('content')
<div class="mb-4 flex flex-wrap items-center gap-4">
    <a href="{{ route('documents.create') }}" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add document</a>
    @php $expiring = request('expiring'); $q = request()->except('expiring'); @endphp
    <a href="{{ route('documents.index', $expiring ? $q : array_merge($q, ['expiring' => 1])) }}" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm {{ $expiring ? 'wise-btn text-white' : '' }}">{{ $expiring ? 'Showing expiring soon (click to clear)' : 'Show expiring in 30 days' }}</a>
    <form method="GET" action="{{ route('documents.index') }}" class="flex flex-wrap gap-2 items-end">
        @if(request('expiring'))<input type="hidden" name="expiring" value="1">@endif
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-0.5">Employee</label>
            <select name="employee_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                <option value="">All</option>
                @foreach($employees as $e)
                    <option value="{{ $e->id }}" {{ request('employee_id') == $e->id ? 'selected' : '' }}>{{ $e->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-0.5">Type</label>
            <select name="type" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                <option value="">All</option>
                @foreach(\Modules\Core\Models\EmployeeDocument::typeOptions() as $val => $label)
                    <option value="{{ $val }}" {{ request('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm">Filter</button>
    </form>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Employee</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Type</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Title</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Issue</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Expiry</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($documents as $doc)
            <tr class="{{ $doc->isExpired() ? 'bg-red-50/30 dark:bg-red-900/10' : ($doc->isExpiringSoon() ? 'bg-amber-50/30 dark:bg-amber-900/10' : '') }}">
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100">{{ $doc->employee->full_name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ \Modules\Core\Models\EmployeeDocument::typeOptions()[$doc->type] ?? $doc->type }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $doc->title ?: '—' }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $doc->issue_date?->format('Y-m-d') ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                    @if($doc->expiry_date)
                        {{ $doc->expiry_date->format('Y-m-d') }}
                        @if($doc->isExpired())
                            <span class="text-red-600 dark:text-red-400 text-xs">Expired</span>
                        @elseif($doc->isExpiringSoon())
                            <span class="text-amber-600 dark:text-amber-400 text-xs">Soon</span>
                        @endif
                    @else
                        —
                    @endif
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('documents.download', $doc) }}" class="wise-link hover:underline text-sm">Download</a>
                    <a href="{{ route('documents.show', $doc) }}" class="wise-link hover:underline text-sm ml-2">View</a>
                    <form method="POST" action="{{ route('documents.destroy', $doc) }}" class="inline ml-2" onsubmit="return confirm('Delete this document?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="wise-link hover:underline text-sm text-red-600 dark:text-red-400">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No documents yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($documents->hasPages())
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">{{ $documents->links() }}</div>
    @endif
</div>
@endsection

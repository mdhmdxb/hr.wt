@extends('core::layouts.app')

@section('title', 'Assets')
@section('heading', 'Assets')

@section('content')
@if($assetsExpiring->isNotEmpty())
<div class="mb-4 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
    <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-200 mb-2">Expiry alerts</h3>
    <ul class="text-sm text-amber-700 dark:text-amber-300 space-y-1">
        @foreach($assetsExpiring as $a)
        <li>
            <a href="{{ route('assets.show', $a) }}" class="wise-link font-medium">{{ $a->name }}</a>
            — Expiry: {{ $a->expiry_date->format('Y-m-d') }}
            @if($a->isExpired())
                <span class="text-red-600 dark:text-red-400 font-medium">(Expired)</span>
            @else
                <span class="text-amber-600 dark:text-amber-400">(Expiring soon)</span>
            @endif
        </li>
        @endforeach
    </ul>
</div>
@endif
<div class="mb-4 flex flex-wrap items-center gap-4">
    <a href="{{ route('assets.create') }}" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add asset</a>
    <a href="{{ route('assets.types.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-sm">Manage types</a>
    <form method="GET" action="{{ route('assets.index') }}" class="flex flex-wrap gap-2 items-end">
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-0.5">Type</label>
            <select name="asset_type_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                <option value="">All</option>
                @foreach($assetTypes as $t)
                    <option value="{{ $t->id }}" {{ request('asset_type_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-0.5">Status</label>
            <select name="status" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                <option value="">All</option>
                @foreach(\Modules\Core\Models\Asset::statusOptions() as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-0.5">Assigned to</label>
            <select name="employee_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                <option value="">Any</option>
                @foreach($employees as $e)
                    <option value="{{ $e->id }}" {{ request('employee_id') == $e->id ? 'selected' : '' }}>{{ $e->full_name }}</option>
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
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Type</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Identifier</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Issue / Expiry</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Assigned to</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($assets as $asset)
            <tr>
                <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">{{ $asset->name }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $asset->assetType->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $asset->identifier ?? '—' }}</td>
                <td class="px-4 py-3"><span class="text-xs px-2 py-0.5 rounded {{ $asset->status === 'assigned' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : ($asset->status === 'available' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400') }}">{{ \Modules\Core\Models\Asset::statusOptions()[$asset->status] ?? $asset->status }}</span></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                    @if($asset->issue_date || $asset->expiry_date)
                        {{ $asset->issue_date ? $asset->issue_date->format('Y-m-d') : '—' }} / {{ $asset->expiry_date ? $asset->expiry_date->format('Y-m-d') : '—' }}
                        @if($asset->isExpired())<span class="text-red-600 dark:text-red-400" title="Expired">⚠</span>@elseif($asset->isExpiringSoon())<span class="text-amber-600 dark:text-amber-400" title="Expiring soon">⚠</span>@endif
                    @else
                        —
                    @endif
                </td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $asset->currentAssignment?->employee?->full_name ?? '—' }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('assets.show', $asset) }}" class="wise-link hover:underline text-sm">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No assets yet. Add an asset type first, then add assets.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($assets->hasPages())
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">{{ $assets->links() }}</div>
    @endif
</div>
@endsection

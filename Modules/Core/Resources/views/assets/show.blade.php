@extends('core::layouts.app')

@section('title', $asset->name)
@section('heading', $asset->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('assets.index') }}" class="wise-link hover:underline">← Back to Assets</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Type</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $asset->assetType->name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Identifier</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $asset->identifier ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Status</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ \Modules\Core\Models\Asset::statusOptions()[$asset->status] ?? $asset->status }}</dd></div>
        @if($asset->issue_date || $asset->expiry_date)
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Issue date</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $asset->issue_date ? $asset->issue_date->format('Y-m-d') : '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Expiry date</dt><dd class="font-medium text-slate-900 dark:text-slate-100">@if($asset->expiry_date){{ $asset->expiry_date->format('Y-m-d') }} @if($asset->isExpired())<span class="text-red-600 dark:text-red-400">(Expired)</span>@elseif($asset->isExpiringSoon())<span class="text-amber-600 dark:text-amber-400">(Expiring soon)</span>@endif @else — @endif</dd></div>
        @endif
        @if($asset->meta && count($asset->meta) > 0)
            @php $labels = \Modules\Core\Models\Asset::metaFieldLabels($asset->assetType->slug ?? ''); @endphp
            @foreach($asset->meta as $key => $val)
                @if(!empty($val))
                <div><dt class="text-sm text-slate-500 dark:text-slate-400">{{ $labels[$key] ?? $key }}</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $val }}</dd></div>
                @endif
            @endforeach
        @endif
        @php $current = $asset->currentAssignment ?? $asset->assignments->whereNull('returned_at')->sortByDesc('assigned_at')->first(); @endphp
        @if($current)
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500 dark:text-slate-400">Currently assigned to</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><a href="{{ route('employee.show', $current->employee) }}" class="wise-link">{{ $current->employee->full_name }}</a> since {{ $current->assigned_at->format('Y-m-d') }}</dd></div>
        @endif
        @if($asset->notes)
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500 dark:text-slate-400">Notes</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $asset->notes }}</dd></div>
        @endif
    </dl>
    <div class="mt-6 flex flex-wrap gap-2">
        <a href="{{ route('assets.edit', $asset) }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Edit</a>
        @if($current)
        <form method="POST" action="{{ route('assets.return', $asset) }}" class="inline">
            @csrf
            <input type="hidden" name="notes" value="">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Return asset</button>
        </form>
        @else
        <a href="{{ route('assets.show', $asset) }}?assign=1" class="px-4 py-2 wise-btn text-white rounded-lg">Assign to employee</a>
        @endif
    </div>
</div>
@if($asset->assignments->isNotEmpty())
<div class="mt-6 bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl">
    <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-4">Assignment history</h3>
    <ul class="space-y-2">
        @foreach($asset->assignments->sortByDesc('assigned_at') as $a)
        <li class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700/50 last:border-0">
            <span class="text-slate-700 dark:text-slate-300"><a href="{{ route('employee.show', $a->employee) }}" class="wise-link">{{ $a->employee->full_name }}</a> — {{ $a->assigned_at->format('Y-m-d') }} @if($a->returned_at) → {{ $a->returned_at->format('Y-m-d') }} @else (current) @endif</span>
        </li>
        @endforeach
    </ul>
</div>
@endif
@if(request('assign') && !$current)
<div class="mt-6 bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-xl" id="assign-form">
    <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-4">Assign to employee</h3>
    <form method="POST" action="{{ route('assets.assign', $asset) }}">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Employee *</label>
                <select name="employee_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <option value="">Select employee</option>
                    @foreach(\Modules\Core\Models\Employee::where('status', 'active')->orderBy('first_name')->get() as $e)
                        <option value="{{ $e->id }}">{{ $e->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Condition (optional)</label>
                <input type="text" name="condition" value="{{ old('condition') }}" placeholder="e.g. Good" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">{{ old('notes') }}</textarea>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Assign</button>
                <a href="{{ route('assets.show', $asset) }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endif
@endsection

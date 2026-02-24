@extends('core::layouts.app')

@section('title', 'Edit asset')
@section('heading', 'Edit asset')

@section('content')
<form method="POST" action="{{ route('assets.update', $asset) }}" class="max-w-xl space-y-4">
    @csrf
    @method('PUT')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Type *</label>
            <select name="asset_type_id" id="asset_type_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @foreach($assetTypes as $t)
                    <option value="{{ $t->id }}" data-slug="{{ $t->slug }}" {{ old('asset_type_id', $asset->asset_type_id) == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Name *</label>
            <input type="text" name="name" value="{{ old('name', $asset->name) }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Identifier</label>
            <input type="text" name="identifier" value="{{ old('identifier', $asset->identifier) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status</label>
            <select name="status" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @foreach(\Modules\Core\Models\Asset::statusOptions() as $val => $label)
                    <option value="{{ $val }}" {{ old('status', $asset->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Issue date</label>
            <input type="date" name="issue_date" value="{{ old('issue_date', $asset->issue_date?->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Expiry date</label>
            <input type="date" name="expiry_date" value="{{ old('expiry_date', $asset->expiry_date?->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        @foreach($assetTypes as $t)
            @php $metaLabels = \Modules\Core\Models\Asset::metaFieldLabels($t->slug); @endphp
            @if(!empty($metaLabels))
            <div class="asset-meta-group border border-slate-200 dark:border-slate-600 rounded-lg p-4 space-y-3" data-type-slug="{{ $t->slug }}" style="display: {{ ($asset->assetType && $asset->assetType->slug === $t->slug) ? 'block' : 'none' }};">
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Details for {{ $t->name }}</p>
                @foreach($metaLabels as $key => $label)
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $label }}</label>
                    <input type="text" name="meta[{{ $key }}]" value="{{ old('meta.'.$key, $asset->meta[$key] ?? '') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                @endforeach
            </div>
            @endif
        @endforeach
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Notes</label>
            <textarea name="notes" rows="2" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">{{ old('notes', $asset->notes) }}</textarea>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Update</button>
            <a href="{{ route('assets.show', $asset) }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        </div>
    </div>
</form>
<script>
document.getElementById('asset_type_id').addEventListener('change', function() {
    var slug = this.selectedOptions[0] && this.selectedOptions[0].getAttribute('data-slug');
    document.querySelectorAll('.asset-meta-group').forEach(function(el) {
        el.style.display = el.getAttribute('data-type-slug') === slug ? 'block' : 'none';
    });
});
</script>
@endsection

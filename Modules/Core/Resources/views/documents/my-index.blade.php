@extends('core::layouts.app')

@section('title', 'My documents')
@section('heading', 'My documents')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-slate-600 dark:text-slate-400">These are the documents you have uploaded. For most types you can upload only once, unless the document expires or HR enables a replacement.</p>
    <a href="{{ route('my-documents.create') }}" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg text-sm font-medium">Upload document</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Type</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Title</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Issue</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Expiry</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($documents as $doc)
            <tr class="{{ $doc->isExpired() ? 'bg-red-50/30 dark:bg-red-900/10' : ($doc->isExpiringSoon() ? 'bg-amber-50/30 dark:bg-amber-900/10' : '') }}">
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100">{{ \Modules\Core\Models\EmployeeDocument::typeOptions()[$doc->type] ?? $doc->type }}</td>
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
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400 text-sm">
                    {{ ucfirst($doc->status) }}
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('my-documents.download', $doc) }}" class="wise-link hover:underline text-sm">Download</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400 text-sm">You have not uploaded any documents yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection


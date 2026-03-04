@extends('core::layouts.app')

@section('title', 'Document')
@section('heading', 'Document')

@section('content')
<div class="mb-4">
    <a href="{{ route('documents.index') }}" class="wise-link hover:underline">← Back to Document vault</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Employee</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $document->employee->full_name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Type</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ \Modules\Core\Models\EmployeeDocument::typeOptions()[$document->type] ?? $document->type }}</dd></div>
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500 dark:text-slate-400">Title</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $document->title ?: '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Issue date</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $document->issue_date?->format('Y-m-d') ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Expiry date</dt><dd class="font-medium text-slate-900 dark:text-slate-100">
            @if($document->expiry_date)
                {{ $document->expiry_date->format('Y-m-d') }}
                @if($document->isExpired()) <span class="text-red-600 dark:text-red-400 text-sm">(Expired)</span>
                @elseif($document->isExpiringSoon(30)) <span class="text-amber-600 dark:text-amber-400 text-sm">(Expiring soon)</span>
                @endif
            @else
                —
            @endif
        </dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Uploaded</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $document->uploaded_at?->format('Y-m-d H:i') ?? '—' }} @if($document->uploadedByUser) by {{ $document->uploadedByUser->name }} @endif</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Employee can upload replacement</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $document->employee_can_upload_again ? 'Yes (one more upload allowed)' : 'No (locked)' }}</dd></div>
        @if($document->notes)
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500 dark:text-slate-400">Notes</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $document->notes }}</dd></div>
        @endif
    </dl>
    @if($document->renewalOf)
    <div class="mt-4 p-3 bg-slate-100 dark:bg-slate-700/50 rounded-lg">
        <p class="text-sm text-slate-600 dark:text-slate-400">This document is a <strong>renewal</strong> of a previous version.</p>
        <a href="{{ route('documents.show', $document->renewalOf) }}" class="text-sm wise-link mt-1 inline-block">View previous version →</a>
    </div>
    @endif
    @if($document->renewals->isNotEmpty())
    <div class="mt-4 p-3 bg-slate-100 dark:bg-slate-700/50 rounded-lg">
        <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Renewals (all versions kept)</p>
        <ul class="space-y-1">
            @foreach($document->renewals as $r)
            <li><a href="{{ route('documents.show', $r) }}" class="text-sm wise-link">Version uploaded {{ $r->uploaded_at?->format('Y-m-d') }} — Expiry {{ $r->expiry_date?->format('Y-m-d') ?? '—' }}</a></li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="mt-6 flex flex-wrap gap-2">
        <a href="{{ route('documents.download', $document) }}" class="px-4 py-2 wise-btn text-white rounded-lg">Download file</a>
        <a href="{{ route('documents.create', ['renewal_of_id' => $document->id, 'employee_id' => $document->employee_id]) }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Add renewal</a>
        <a href="{{ route('employee.show', $document->employee) }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">View employee</a>
        <form method="POST" action="{{ route('documents.destroy', $document) }}" class="inline" onsubmit="return confirm('Delete this document?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 border border-red-300 dark:border-red-700 rounded-lg text-red-600 dark:text-red-400">Delete</button>
        </form>
    </div>
</div>
@endsection

@extends('core::layouts.app')

@section('title', 'Upload document')
@section('heading', 'Upload document')

@section('content')
<div class="mb-4">
    <a href="{{ route('my-documents.index') }}" class="wise-link hover:underline">← Back to My documents</a>
</div>
<form method="POST" action="{{ route('my-documents.store') }}" enctype="multipart/form-data" class="max-w-xl space-y-4">
    @csrf
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <p class="text-sm text-slate-600 dark:text-slate-400">
            Please upload clear copies of your official documents. For most types you can upload only once.
            If you need to replace a document before it expires, contact HR so they can enable a replacement for you.
        </p>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Document type *</label>
            <select name="type" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @foreach($types as $val => $label)
                    <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }} @if($lockedTypes[$val] ?? false) disabled @endif>
                        {{ $label }} @if($lockedTypes[$val] ?? false) (already uploaded) @endif
                    </option>
                @endforeach
            </select>
            @error('type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Title (optional)</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" placeholder="e.g. Passport copy">
            @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">File *</label>
            <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">PDF, image, or document. Max 10 MB.</p>
            @error('file')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Issue date (optional)</label>
                <input type="date" name="issue_date" value="{{ old('issue_date') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @error('issue_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Expiry date (optional)</label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @error('expiry_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Notes (optional)</label>
            <textarea name="notes" rows="2" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">{{ old('notes') }}</textarea>
            @error('notes')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Upload</button>
            <a href="{{ route('my-documents.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        </div>
    </div>
</form>
@endsection


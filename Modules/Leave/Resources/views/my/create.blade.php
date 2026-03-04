@extends('core::layouts.app')

@section('title', 'New Leave Request')
@section('heading', 'New leave request')

@section('content')
<div class="mb-4">
    <a href="{{ route('my-leave.index') }}" class="wise-link hover:underline">← Back to My leave</a>
></div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-xl">
    <form method="POST" action="{{ route('my-leave.store') }}" class="space-y-4" enctype="multipart/form-data">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Leave type *</label>
            <select name="leave_type_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <option value="">— Select —</option>
                @foreach($leaveTypes as $t)
                    <option value="{{ $t->id }}" {{ old('leave_type_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
            @error('leave_type_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Start date *</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @error('start_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">End date *</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @error('end_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Reason (optional)</label>
            <textarea name="reason" rows="3" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" placeholder="Short note for your approver">{{ old('reason') }}</textarea>
            @error('reason')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Supporting document</label>
            <input type="file" name="document" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">For some leave types (e.g. sick, maternity), HR may require a document such as a medical note.</p>
            @error('document')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400">After submitting, your request will follow the configured approval workflow (Manager → HR → MD, etc.).</p>
        <div class="flex gap-3">
            <a href="{{ route('my-leave.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-sm">Cancel</a>
            <button type="submit" class="px-6 py-2 wise-btn text-white rounded-lg text-sm">Submit request</button>
        </div>
    </form>
</div>
@endsection


@extends('core::layouts.app')

@section('title', 'Email templates')
@section('heading', 'Email templates')

@section('content')
<div class="max-w-4xl space-y-6">
    <section class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Leave email templates</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
            These templates are used when the system sends email notifications about leave decisions.
            You can use placeholders like <code class="text-xs bg-slate-100 dark:bg-slate-700 px-1 rounded">@{{ employee_name }}</code>.
        </p>
        <form method="POST" action="{{ route('email-templates.update') }}" class="space-y-4">
            @csrf
            @foreach($templates as $key => $tpl)
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $tpl['label'] }}</label>
                <textarea name="{{ $key }}" rows="5" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white px-3 py-2 text-sm font-mono">{{ old($key, $tpl['value']) }}</textarea>
            </div>
            @endforeach
            <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mt-6 mb-2">Letter templates (PDF body)</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-3">Main text of leave approval and cancellation letters (PDF).</p>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Leave approval letter body</label>
                <textarea name="template_leave_approval" rows="5" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white px-3 py-2 text-sm font-mono">{{ old('template_leave_approval', $leaveApprovalLetter ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Leave cancellation letter body</label>
                <textarea name="template_leave_cancellation" rows="5" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white px-3 py-2 text-sm font-mono">{{ old('template_leave_cancellation', $leaveCancellationLetter ?? '') }}</textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg text-sm font-medium">Save all templates</button>
            </div>
        </form>
    </section>

    <section class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-2">Available placeholders</h3>
        <ul class="text-xs text-slate-600 dark:text-slate-300 space-y-1">
            @foreach($placeholders as $ph => $help)
            <li><code class="bg-slate-100 dark:bg-slate-700 px-1 rounded">{{ $ph }}</code> – {{ $help }}</li>
            @endforeach
        </ul>
    </section>
</div>
@endsection


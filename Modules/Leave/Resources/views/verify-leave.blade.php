@extends('core::layouts.guest')

@section('title', 'Verify Leave')

@section('content')
<div class="max-w-md mx-auto bg-white dark:bg-slate-800 rounded-xl shadow p-6 text-center">
    @if($valid && $leaveRequest->status === 'approved')
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Valid leave approval</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">This leave has been verified by Wise HRM.</p>
        <dl class="text-left space-y-2 border-t border-slate-200 dark:border-slate-600 pt-4">
            <div><dt class="text-xs text-slate-500 dark:text-slate-400">Employee</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->employee->full_name ?? '—' }}</dd></div>
            <div><dt class="text-xs text-slate-500 dark:text-slate-400">Leave type</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->leaveType->name ?? '—' }}</dd></div>
            <div><dt class="text-xs text-slate-500 dark:text-slate-400">Period</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->start_date->format('M j, Y') }} – {{ $leaveRequest->end_date->format('M j, Y') }}</dd></div>
            <div><dt class="text-xs text-slate-500 dark:text-slate-400">Status</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ ucfirst($leaveRequest->status) }}</dd></div>
        </dl>
    @elseif($valid && $leaveRequest->status === 'cancelled')
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Cancelled leave</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">This leave request was cancelled.</p>
        <dl class="text-left space-y-2 border-t border-slate-200 dark:border-slate-600 pt-4">
            <div><dt class="text-xs text-slate-500 dark:text-slate-400">Employee</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->employee->full_name ?? '—' }}</dd></div>
            <div><dt class="text-xs text-slate-500 dark:text-slate-400">Leave type</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->leaveType->name ?? '—' }}</dd></div>
            <div><dt class="text-xs text-slate-500 dark:text-slate-400">Period</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->start_date->format('M j, Y') }} – {{ $leaveRequest->end_date->format('M j, Y') }}</dd></div>
            @if($leaveRequest->cancelled_at)
            <div><dt class="text-xs text-slate-500 dark:text-slate-400">Cancelled at</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->cancelled_at->format('M j, Y H:i') }}</dd></div>
            @endif
            @if($leaveRequest->cancel_reason)
            <div><dt class="text-xs text-slate-500 dark:text-slate-400">Cancel reason</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->cancel_reason }}</dd></div>
            @endif
        </dl>
    @else
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Verification failed</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400">This link is invalid or has expired.</p>
    @endif
</div>
@endsection

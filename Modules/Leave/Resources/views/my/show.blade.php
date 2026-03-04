@extends('core::layouts.app')

@section('title', 'My Leave Request')
@section('heading', 'My leave request')

@section('content')
<div class="mb-4 flex flex-wrap items-center gap-3">
    <a href="{{ route('my-leave.index') }}" class="wise-link hover:underline">← Back to My leave</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Leave type</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->leaveType->name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Status</dt><dd class="font-medium">
            @if($leaveRequest->status === 'pending')
                <span class="px-2 py-1 text-xs rounded bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Pending</span>
            @elseif($leaveRequest->status === 'approved')
                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Approved</span>
            @elseif($leaveRequest->status === 'cancelled')
                <span class="px-2 py-1 text-xs rounded bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-100">Cancelled</span>
            @else
                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Rejected</span>
            @endif
        </dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Start date</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->start_date->format('Y-m-d') }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">End date</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->end_date->format('Y-m-d') }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Days</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->days }}</dd></div>
        @if($leaveRequest->reason)
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500 dark:text-slate-400">Reason</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->reason }}</dd></div>
        @endif
        @if($leaveRequest->supporting_document_path)
        <div class="sm:col-span-2">
            <dt class="text-sm text-slate-500 dark:text-slate-400">Supporting document</dt>
            <dd class="font-medium text-slate-900 dark:text-slate-100">
                <a href="{{ route('my-leave.document', $leaveRequest) }}" class="wise-link text-sm" target="_blank">Download document</a>
            </dd>
        </div>
        @endif
        @if($leaveRequest->approvalSteps->isNotEmpty())
        <div class="sm:col-span-2">
            <dt class="text-sm text-slate-500 dark:text-slate-400 mb-2">Approval workflow</dt>
            <dd class="space-y-1">
                @foreach($leaveRequest->approvalSteps as $step)
                <div class="flex items-center gap-2 text-sm">
                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $loop->iteration }}. {{ \Modules\Leave\Models\LeaveApprovalStep::approverTypeOptions()[$step->approver_type] ?? $step->approver_type }}</span>
                    @if($step->status === 'approved')
                        <span class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Approved</span>
                        @if($step->approvedByUser) <span class="text-slate-500 dark:text-slate-400">by {{ $step->approvedByUser->name }}</span> @endif
                        <span class="text-slate-500 dark:text-slate-400">{{ $step->approved_at?->format('M j, Y H:i') }}</span>
                    @elseif($step->status === 'rejected')
                        <span class="px-2 py-0.5 text-xs rounded bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Rejected</span>
                        @if($step->approvedByUser) <span class="text-slate-500 dark:text-slate-400">by {{ $step->approvedByUser->name }}</span> @endif
                        <span class="text-slate-500 dark:text-slate-400">{{ $step->approved_at?->format('M j, Y H:i') }}</span>
                    @else
                        <span class="px-2 py-0.5 text-xs rounded bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Pending</span>
                    @endif
                </div>
                @endforeach
            </dd>
        </div>
        @endif
    </dl>
    @if($leaveRequest->canBeCancelledBy(auth()->user()))
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-600">
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">You can cancel this request while it is still pending and not yet approved.</p>
        <form method="POST" action="{{ route('my-leave.cancel', $leaveRequest) }}" class="inline flex flex-wrap items-center gap-2">
            @csrf
            <input type="text" name="cancel_reason" placeholder="Reason (optional)" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm max-w-xs">
            <button type="submit" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-sm">Cancel request</button>
        </form>
    </div>
    @endif
</div>
@endsection


@extends('core::layouts.app')

@section('title', 'Leave Request')
@section('heading', 'Leave Request')

@section('content')
<div class="mb-4">
    <a href="{{ route('leave.index') }}" class="wise-link hover:underline">← Back to Leave</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Employee</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->employee->full_name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Leave type</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->leaveType->name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Start date</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->start_date->format('Y-m-d') }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">End date</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->end_date->format('Y-m-d') }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Days</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->days }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Application date</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->created_at?->format('Y-m-d H:i') }}</dd></div>
        @if($leaveRequest->actual_return_date)
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Actual return date</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->actual_return_date->format('Y-m-d') }}</dd></div>
        @if($leaveRequest->overstay_days)
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Overstay (unpaid)</dt><dd class="font-medium text-amber-700 dark:text-amber-400">{{ $leaveRequest->overstay_days }} day(s) — counted as unpaid leave</dd></div>
        @endif
        @endif
        @if(!$leaveRequest->leaveType->is_paid && $leaveRequest->employee && $leaveRequest->employee->basic_salary)
        @php $dailyRate = (float) $leaveRequest->employee->basic_salary / 30; $estDeduction = round($dailyRate * $leaveRequest->days, 2); @endphp
        <div class="sm:col-span-2">
            <dt class="text-sm text-slate-500 dark:text-slate-400">Estimated salary impact (unpaid leave)</dt>
            <dd class="font-medium text-amber-700 dark:text-amber-400">− {{ number_format($estDeduction, 2) }} ({{ $leaveRequest->days }} days × {{ number_format($dailyRate, 2) }}/day)</dd>
        </div>
        @endif
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
        @if($leaveRequest->reason)
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500 dark:text-slate-400">Reason</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->reason }}</dd></div>
        @endif
        @if($leaveRequest->supporting_document_path)
        <div class="sm:col-span-2">
            <dt class="text-sm text-slate-500 dark:text-slate-400">Supporting document</dt>
            <dd class="font-medium text-slate-900 dark:text-slate-100">
                <a href="{{ route('leave.document', $leaveRequest) }}" class="wise-link text-sm" target="_blank">Download document</a>
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
                        @if($step->approvedByUser) <span class="text-slate-500 dark:text-slate-400">by {{ $step->approvedByUser->name }}</span> @endif
                    @else
                        <span class="px-2 py-0.5 text-xs rounded bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Pending</span>
                    @endif
                </div>
                @endforeach
            </dd>
        </div>
        @endif
        {{-- Final actor + timestamps --}}
        @if($leaveRequest->status === 'approved' && $leaveRequest->approved_by)
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Approved by</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->approvedByUser->name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Approved at</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->approved_at?->format('Y-m-d H:i') }}</dd></div>
        @elseif($leaveRequest->status === 'rejected' && $leaveRequest->approved_by)
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Rejected by</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->approvedByUser->name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Rejected at</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->approved_at?->format('Y-m-d H:i') }}</dd></div>
        @elseif($leaveRequest->status === 'cancelled' && $leaveRequest->cancelled_by)
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Cancelled by</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->cancelledByUser->name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Cancelled at</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->cancelled_at?->format('Y-m-d H:i') }}</dd></div>
        @endif
        @if($leaveRequest->rejection_reason)
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500 dark:text-slate-400">Rejection reason</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->rejection_reason }}</dd></div>
        @endif
        @if($leaveRequest->cancel_reason)
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500 dark:text-slate-400">Cancel reason</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->cancel_reason }}</dd></div>
        @endif
    </dl>
    @if($leaveRequest->status === 'approved' && !$leaveRequest->actual_return_date)
    <div class="mt-6 p-4 bg-slate-50 dark:bg-slate-700/30 rounded-lg">
        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 mb-2">Record return from leave</h3>
        <p class="text-xs text-slate-600 dark:text-slate-400 mb-2">When the employee returns: early return adds unused days back to remaining leave; returning after the end date records overstay as unpaid days.</p>
        <form method="POST" action="{{ route('leave.record-return', $leaveRequest) }}" class="flex flex-wrap items-end gap-2">
            @csrf
            <div>
                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Actual return date</label>
                <input type="date" name="actual_return_date" required class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 text-sm">Record return</button>
        </form>
    </div>
    @endif
    @if($leaveRequest->status === 'approved' || $leaveRequest->status === 'cancelled')
    <div class="mt-6 flex flex-wrap items-center gap-4">
        <a href="{{ route('leave.letter', $leaveRequest) }}" class="px-4 py-2 wise-btn text-white rounded-lg inline-flex items-center gap-2">
            <span>📄</span> Download letter (PDF)
        </a>
        @if(!empty($leaveRequest->verification_token))
        <div class="flex items-center gap-2">
            <img src="{{ route('leave.qr', $leaveRequest) }}" alt="QR" class="w-16 h-16" width="64" height="64">
            <div class="text-xs text-slate-500 dark:text-slate-400">
                <p>Scan to verify</p>
                <a href="{{ route('leave.verify', ['leaveRequest' => $leaveRequest->id, 'token' => $leaveRequest->verification_token]) }}" target="_blank" rel="noopener" class="wise-link">Verification link</a>
            </div>
        </div>
        @endif
    </div>
    @endif
    <div class="mt-6 flex flex-wrap gap-2">
        @if($leaveRequest->status === 'pending' && $leaveRequest->canBeActedOnBy(auth()->user()))
        <form method="POST" action="{{ route('leave.approve', $leaveRequest) }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Approve</button>
        </form>
        <form method="POST" action="{{ route('leave.reject', $leaveRequest) }}" class="inline flex items-center gap-2">
            @csrf
            <input type="text" name="rejection_reason" placeholder="Reason (optional)" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Reject</button>
        </form>
        @endif
        @if($leaveRequest->canBeCancelledBy(auth()->user()))
        <form method="POST" action="{{ route('leave.cancel', $leaveRequest) }}" class="inline flex items-center gap-2">
            @csrf
            <input type="text" name="cancel_reason" placeholder="Cancel reason (optional)" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
            <button type="submit" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700">Cancel request</button>
        </form>
        @endif
    </div>
</div>
@endsection

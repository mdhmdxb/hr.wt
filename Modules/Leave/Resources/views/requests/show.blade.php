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
            @else
                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Rejected</span>
            @endif
        </dd></div>
        @if($leaveRequest->reason)
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500 dark:text-slate-400">Reason</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->reason }}</dd></div>
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
                    @else
                        <span class="px-2 py-0.5 text-xs rounded bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Pending</span>
                    @endif
                </div>
                @endforeach
            </dd>
        </div>
        @elseif($leaveRequest->approved_by)
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Processed by</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->approvedByUser->name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Processed at</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->approved_at?->format('Y-m-d H:i') }}</dd></div>
        @endif
        @if($leaveRequest->rejection_reason)
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500 dark:text-slate-400">Rejection reason</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $leaveRequest->rejection_reason }}</dd></div>
        @endif
    </dl>
    @if($leaveRequest->status === 'approved')
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
    @if($leaveRequest->status === 'pending' && $leaveRequest->canBeActedOnBy(auth()->user()))
    <div class="mt-6 flex gap-2">
        <form method="POST" action="{{ route('leave.approve', $leaveRequest) }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Approve</button>
        </form>
        <form method="POST" action="{{ route('leave.reject', $leaveRequest) }}" class="inline flex items-center gap-2">
            @csrf
            <input type="text" name="rejection_reason" placeholder="Reason (optional)" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Reject</button>
        </form>
    </div>
    @endif
</div>
@endsection

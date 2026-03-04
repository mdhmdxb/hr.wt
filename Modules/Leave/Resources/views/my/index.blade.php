@extends('core::layouts.app')

@section('title', 'My Leave')
@section('heading', 'My leave')

@section('content')
<div class="mb-4 flex flex-wrap items-center gap-3">
    <a href="{{ route('dashboard') }}" class="wise-link hover:underline">← Back to Dashboard</a>
    <a href="{{ route('my-leave.create') }}" class="px-4 py-2 wise-btn text-white rounded-lg text-sm font-medium">New leave request</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
    <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Your leave requests</h2>
    @if($leaveRequests->isEmpty())
        <p class="text-sm text-slate-500 dark:text-slate-400">No leave requests yet. Click “New leave request” to apply.</p>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Type</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Dates</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Days</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Status</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                @foreach($leaveRequests as $lr)
                <tr>
                    <td class="px-4 py-2 text-slate-800 dark:text-slate-100">{{ $lr->leaveType->name ?? '—' }}</td>
                    <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ $lr->start_date->format('Y-m-d') }} → {{ $lr->end_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ $lr->days }}</td>
                    <td class="px-4 py-2">
                        @if($lr->status === \Modules\Leave\Models\LeaveRequest::STATUS_PENDING)
                            <span class="px-2 py-0.5 text-xs rounded bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Pending</span>
                        @elseif($lr->status === \Modules\Leave\Models\LeaveRequest::STATUS_APPROVED)
                            <span class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Approved</span>
                        @elseif($lr->status === \Modules\Leave\Models\LeaveRequest::STATUS_CANCELLED)
                            <span class="px-2 py-0.5 text-xs rounded bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-100">Cancelled</span>
                        @else
                            <span class="px-2 py-0.5 text-xs rounded bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Rejected</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('my-leave.show', $lr) }}" class="wise-link text-sm">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $leaveRequests->links() }}
    </div>
    @endif
</div>
@endsection


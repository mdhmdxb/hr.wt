@extends('core::layouts.app')

@section('title', 'Leave Types')
@section('heading', 'Leave Types')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <a href="{{ route('leave.index') }}" class="wise-link hover:underline">← Back to Leave</a>
    <a href="{{ route('leave.types.create') }}" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add Leave Type</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Days/Year</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Carry Over</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Paid</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Workflow</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Requests</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($leaveTypes as $lt)
            <tr>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100">{{ $lt->name }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $lt->days_per_year }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $lt->carry_over ? 'Yes' : 'No' }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $lt->is_paid ? 'Yes' : 'No' }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400 text-xs">
                    @php
                        $steps = $lt->getWorkflowStepsNormalized();
                        $labels = \Modules\Leave\Models\LeaveApprovalStep::approverTypeOptions();
                    @endphp
                    @if(empty($steps))
                        Single HR approval
                    @else
                        @foreach($steps as $idx => $step)
                            {{ $labels[$step['approver'] ?? 'hr'] ?? ($step['approver'] ?? 'HR') }}@if($idx < count($steps)-1) → @endif
                        @endforeach
                    @endif
                </td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $lt->leave_requests_count }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('leave.types.edit', $lt) }}" class="wise-link hover:underline">Edit</a>
                    <form method="POST" action="{{ route('leave.types.destroy', $lt) }}" class="inline ml-2" onsubmit="return confirm('Delete this leave type?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No leave types. Add one to allow leave requests.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($leaveTypes->hasPages())
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
        {{ $leaveTypes->links() }}
    </div>
    @endif
</div>
@endsection

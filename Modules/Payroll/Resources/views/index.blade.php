@extends('core::layouts.app')

@section('title', 'Payroll')
@section('heading', 'Payroll')

@section('content')
<div class="mb-4">
    <a href="{{ route('payroll.create') }}" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">New Payroll Run</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Period</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Payslips</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($runs as $run)
            <tr>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100">{{ $run->period_start->format('Y-m-d') }} – {{ $run->period_end->format('Y-m-d') }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $run->payslips_count }}</td>
                <td class="px-4 py-3">
                    @if($run->status === 'draft')
                        <span class="px-2 py-1 text-xs rounded bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">Draft</span>
                    @elseif($run->status === 'finalized')
                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">Finalized</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Paid</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('payroll.show', $run) }}" class="wise-link hover:underline">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No payroll runs. Create one to generate payslips for the period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($runs->hasPages())
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
        {{ $runs->links() }}
    </div>
    @endif
</div>
@endsection

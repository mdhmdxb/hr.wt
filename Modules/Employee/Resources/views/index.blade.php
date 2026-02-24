@extends('core::layouts.app')

@section('title', 'Employees')
@section('heading', 'Employees')

@section('content')
<div class="mb-4">
    <a href="{{ route('employee.create') }}" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add Employee</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Code</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Department</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($employees as $emp)
            <tr>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100">{{ $emp->employee_code }}</td>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100">{{ $emp->full_name }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $emp->department->name ?? '-' }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs rounded {{ $emp->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300' }}">{{ $emp->status }}</span>
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('employee.show', $emp) }}" class="wise-link hover:underline">View</a>
                    <a href="{{ route('employee.edit', $emp) }}" class="ml-2 wise-link hover:underline">Edit</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No employees yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($employees->hasPages())
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
        {{ $employees->links() }}
    </div>
    @endif
</div>
@endsection

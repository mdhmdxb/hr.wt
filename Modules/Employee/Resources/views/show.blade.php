@extends('core::layouts.app')

@section('title', $employee->full_name)
@section('heading', $employee->full_name)

@section('content')
<div class="flex gap-4 mb-4">
    <a href="{{ route('employee.edit', $employee) }}" class="px-4 py-2 wise-btn text-white rounded-lg">Edit</a>
    <a href="{{ route('employee.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Back to list</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl">
    <div class="flex items-center gap-4 mb-4">
        <div class="w-16 h-16 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden flex items-center justify-center">
            @if($employee->photo_path)
                <img src="{{ asset('storage/' . ltrim($employee->photo_path, '/')) }}" alt="{{ $employee->full_name }}" class="w-16 h-16 object-cover">
            @else
                <span class="text-slate-500 dark:text-slate-300 text-sm">{{ substr($employee->full_name, 0, 1) }}</span>
            @endif
        </div>
        <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Employee</p>
            <p class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $employee->full_name }}</p>
        </div>
    </div>
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Employee Code</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->employee_code }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Email</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->email }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Phone</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->phone ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Nationality</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->nationality ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Gender</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->gender ? ucfirst($employee->gender) : '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Religion</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->religion ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Break (min)</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->break_minutes !== null ? $employee->break_minutes . ' min' : '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Branch</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->branch->name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Site</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->site->name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Department</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->department->name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Designation</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->designation->name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Reporting Manager</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->reportingManager->full_name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Hire Date</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->hire_date?->format('Y-m-d') }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Date of birth</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->date_of_birth?->format('Y-m-d') ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Employment Type</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->employment_type }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Status</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->status }}</dd></div>
        @php
            $uaeEntitled = $employee->uaeAnnualLeaveEntitlement();
            $uaeTaken = $employee->uaeAnnualLeaveTaken();
            $uaeRemaining = $employee->uaeAnnualLeaveRemaining();
        @endphp
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">UAE annual leave – entitled</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $uaeEntitled }} day(s)</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">UAE annual leave – used</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $uaeTaken }} day(s)</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">UAE annual leave – remaining</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $uaeRemaining }} day(s)</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Portal login</dt><dd class="font-medium text-slate-900 dark:text-slate-100">@if($employee->user) Yes ({{ $employee->user->email }}) @else No — <a href="{{ route('employee.edit', $employee) }}" class="wise-link">Edit employee</a> to create login @endif</dd></div>
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500 dark:text-slate-400">Permanent address</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->permanent_address ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Emergency contact</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->emergency_contact_name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Emergency phone</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->emergency_contact_phone ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Basic Salary</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ number_format($employee->basic_salary ?? 0, 2) }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Accommodation</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ number_format($employee->accommodation ?? 0, 2) }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Transportation</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ number_format($employee->transportation ?? 0, 2) }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Food allowance</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ number_format($employee->food_allowance ?? 0, 2) }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Other allowances</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ number_format($employee->other_allowances ?? 0, 2) }}</dd></div>
        @if($employee->getWeeklyOffDaysList() || $employee->getAlternateSaturdayWeeksList() || $employee->shift_start || $employee->shift_end)
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500 dark:text-slate-400">Weekly off</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->getWeeklyOffDaysList() ? implode(', ', array_map('ucfirst', $employee->getWeeklyOffDaysList())) : '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Alt. Saturday off</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->getAlternateSaturdayWeeksList() ? implode(', ', array_map(function ($w) { return $w . (['1'=>'st','2'=>'nd','3'=>'rd','4'=>'th','5'=>'th'][$w] ?? 'th') . ' Sat'; }, $employee->getAlternateSaturdayWeeksList())) : 'No' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Default shift</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->shift_start && $employee->shift_end ? $employee->shift_start . ' – ' . $employee->shift_end : '—' }}</dd></div>
        @endif
    </dl>
    {{-- Role is intentionally NOT shown here (employees must not see system role) --}}
</div>
@if(auth()->user()->isAdmin() || auth()->user()->isHr())
<div class="mt-6 bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl">
    <div class="flex items-center justify-between mb-4">
        <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100">Documents</h3>
        <a href="{{ route('documents.create', ['employee_id' => $employee->id]) }}" class="text-sm wise-link">Add document</a>
    </div>
    @if($documents->isNotEmpty())
    <ul class="space-y-2">
        @foreach($documents as $doc)
        <li class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700/50 last:border-0">
            <span class="text-slate-700 dark:text-slate-300">{{ \Modules\Core\Models\EmployeeDocument::typeOptions()[$doc->type] ?? $doc->type }}{{ $doc->title ? ': ' . $doc->title : '' }}</span>
            <span class="text-slate-500 dark:text-slate-400 text-sm">{{ $doc->expiry_date ? $doc->expiry_date->format('Y-m-d') : '—' }}</span>
            <a href="{{ route('documents.show', $doc) }}" class="text-sm wise-link">View</a>
        </li>
        @endforeach
    </ul>
    @else
    <p class="text-slate-500 dark:text-slate-400 text-sm">No documents yet. <a href="{{ route('documents.create', ['employee_id' => $employee->id]) }}" class="wise-link">Add document</a></p>
    @endif
</div>
<div class="mt-6 bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl">
    <div class="flex items-center justify-between mb-4">
        <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100">Assigned assets</h3>
        <a href="{{ route('assets.index', ['employee_id' => $employee->id]) }}" class="text-sm wise-link">View all assets</a>
    </div>
    @if(isset($assetAssignments) && $assetAssignments->isNotEmpty())
    <ul class="space-y-2">
        @foreach($assetAssignments as $a)
        <li class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700/50 last:border-0">
            <span class="text-slate-700 dark:text-slate-300">{{ $a->asset->assetType->name ?? '—' }}: {{ $a->asset->name }}</span>
            <a href="{{ route('assets.show', $a->asset) }}" class="text-sm wise-link">View</a>
        </li>
        @endforeach
    </ul>
    @else
    <p class="text-slate-500 dark:text-slate-400 text-sm">No assets currently assigned.</p>
    @endif
</div>
@php $revisions = $employee->salaryRevisions ?? collect(); @endphp
@if($revisions->isNotEmpty())
<div class="mt-6 bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl">
    <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-4">Salary revision history</h3>
    <ul class="space-y-2">
        @foreach($revisions->take(10) as $rev)
        <li class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-700/50 last:border-0 text-sm">
            <span class="text-slate-700 dark:text-slate-300">{{ $rev->effective_from->format('Y-m-d') }} — Basic {{ number_format($rev->basic_salary ?? 0, 2) }}</span>
        </li>
        @endforeach
    </ul>
</div>
@endif
@endif
@endsection

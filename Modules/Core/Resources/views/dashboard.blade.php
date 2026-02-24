@extends('core::layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
@php
    $hour = (int) date('H');
    if ($hour < 12) { $greeting = 'Good morning'; } elseif ($hour < 17) { $greeting = 'Good afternoon'; } else { $greeting = 'Good evening'; }
    $isAdmin = auth()->user()->isAdmin();
    $isHr = auth()->user()->isHr();
    $isAccounts = auth()->user()->isAccounts();
@endphp

{{-- Hero welcome --}}
@php $isEmployeeOnly = auth()->user()->employee_id && !$isAdmin && !$isHr && !$isAccounts; @endphp
<div class="relative overflow-hidden rounded-2xl mb-6 p-8 md:p-10 text-white shadow-xl" style="background: linear-gradient(135deg, var(--wise-primary) 0%, var(--wise-secondary) 50%, var(--wise-accent) 100%);">
    <div class="relative z-10">
        <p class="text-white/90 text-sm font-medium uppercase tracking-wider mb-1">{{ $greeting }}</p>
        <h2 class="wise-heading text-3xl md:text-4xl font-bold mb-2">{{ $user->name }}</h2>
        <p class="text-white/90 max-w-xl">@if($isEmployeeOnly) Your dashboard. Check in and out here and see your activity. @else Here’s what’s happening across your organization today. @endif</p>
    </div>
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
</div>

{{-- One-click check-in / check-out for employees --}}
@if(auth()->user()->employee_id)
@php
    $att = $todayAttendance ?? null;
    $canCheckIn = !$att;
    $canCheckOut = $att && $att->check_in_at && !$att->check_out_at;
@endphp
<div class="mb-6 p-6 bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200/50 dark:border-slate-700/50">
    <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Today's attendance</h3>
    @if($att)
    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
        Check-in: <strong>{{ $att->check_in_at ? \Carbon\Carbon::parse($att->check_in_at)->format('h:i A') : '—' }}</strong>
        @if($att->check_out_at)
            · Check-out: <strong>{{ \Carbon\Carbon::parse($att->check_out_at)->format('h:i A') }}</strong>
        @endif
    </p>
    @endif
    <div class="flex flex-col sm:flex-row gap-3">
        @if($canCheckIn)
        <form method="POST" action="{{ route('attendance.self.check-in') }}" class="flex-1">
            @csrf
            <button type="submit" class="w-full sm:flex-1 py-4 px-6 rounded-xl font-semibold text-white text-lg shadow-lg hover:shadow-xl transition-all min-h-14 touch-manipulation" style="background: linear-gradient(135deg, var(--wise-primary), var(--wise-secondary));">
                Check in
            </button>
        </form>
        @endif
        @if($canCheckOut)
        <form method="POST" action="{{ route('attendance.self.check-out') }}" class="flex-1">
            @csrf
            <button type="submit" class="w-full sm:flex-1 py-4 px-6 rounded-xl font-semibold text-white text-lg shadow-lg hover:shadow-xl transition-all min-h-14 touch-manipulation bg-amber-500 hover:bg-amber-600 dark:bg-amber-600 dark:hover:bg-amber-700">
                Check out
            </button>
        </form>
        @endif
        @if(!$canCheckIn && !$canCheckOut && $att)
        <p class="text-slate-500 dark:text-slate-400 py-2">You have completed today's attendance.</p>
        @endif
    </div>
</div>
@endif

{{-- Everything below is for Admin/HR/Accounts only. Employees only see greeting + check-in/out above. --}}
@if($isAdmin || $isHr)
{{-- Dashboard filter: Company, Branch, Site, Department --}}
<form method="GET" action="{{ route('dashboard') }}" id="dashboard-filter-form" class="mb-6 p-4 bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200/50 dark:border-slate-700/50">
    <h3 class="wise-heading text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Filter by</h3>
    <div class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Company</label>
            <select name="company_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm min-w-40" onchange="var f=this.form; f.branch_id.value=''; f.site_id.value=''; f.department_id.value=''; f.submit();">
                <option value="">All companies</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}" {{ (\Illuminate\Support\Arr::get($filter, 'company_id', '')) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Branch</label>
            <select name="branch_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm min-w-40" onchange="var f=this.form; f.site_id.value=''; f.department_id.value=''; f.submit();">
                <option value="">All branches</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ (\Illuminate\Support\Arr::get($filter, 'branch_id', '')) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Site</label>
            <select name="site_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm min-w-40" onchange="this.form.submit()">
                <option value="">All sites</option>
                @foreach($sites as $s)
                    <option value="{{ $s->id }}" {{ (\Illuminate\Support\Arr::get($filter, 'site_id', '')) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Department</label>
            <select name="department_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm min-w-40" onchange="this.form.submit()">
                <option value="">All departments</option>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" {{ (\Illuminate\Support\Arr::get($filter, 'department_id', '')) == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        @if(isset($projects) && $projects->isNotEmpty())
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Project</label>
            <select name="project_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm min-w-40" onchange="this.form.submit()">
                <option value="">All projects</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ (\Illuminate\Support\Arr::get($filter, 'project_id', '')) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        @if(\Illuminate\Support\Arr::get($filter, 'company_id') ?: \Illuminate\Support\Arr::get($filter, 'branch_id') ?: \Illuminate\Support\Arr::get($filter, 'site_id') ?: \Illuminate\Support\Arr::get($filter, 'department_id') ?: \Illuminate\Support\Arr::get($filter, 'project_id'))
        <a href="{{ route('dashboard') }}" class="px-3 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50">Clear</a>
        @endif
    </div>
</form>
@endif

{{-- Stats grid (Admin/HR only) --}}
@if($isAdmin || $isHr)
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Employees</p>
                <p class="text-3xl font-bold text-slate-900 dark:text-slate-100 mt-1">{{ \Illuminate\Support\Arr::get($stats, 'employees', 0) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Active</p>
            </div>
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl bg-slate-100 dark:bg-slate-700" style="color: var(--wise-primary);">👥</div>
        </div>
        @if($isAdmin || $isHr)
        <a href="{{ route('employee.index') }}" class="mt-4 inline-block text-sm font-medium wise-link">View all →</a>
        @endif
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Today’s attendance</p>
                <p class="text-3xl font-bold text-slate-900 dark:text-slate-100 mt-1">{{ \Illuminate\Support\Arr::get($stats, 'attendance_today', 0) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Records</p>
            </div>
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">🕐</div>
        </div>
        @if($isAdmin || $isHr)
        <a href="{{ route('attendance.index') }}" class="mt-4 inline-block text-sm font-medium wise-link">View / record →</a>
        @endif
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Pending leave</p>
                <p class="text-3xl font-bold text-slate-900 dark:text-slate-100 mt-1">{{ \Illuminate\Support\Arr::get($stats, 'pending_leave', 0) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Awaiting approval</p>
            </div>
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">📅</div>
        </div>
        @if($isAdmin || $isHr)
        <a href="{{ route('leave.index') }}?status=pending" class="mt-4 inline-block text-sm font-medium wise-link">Review →</a>
        @endif
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Payroll this month</p>
                <p class="text-3xl font-bold text-slate-900 dark:text-slate-100 mt-1">{{ \Illuminate\Support\Arr::get($stats, 'payroll_this_month', 0) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Runs</p>
            </div>
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">💰</div>
        </div>
        @if($isAdmin || $isAccounts)
        <a href="{{ route('payroll.index') }}" class="mt-4 inline-block text-sm font-medium wise-link">View payroll →</a>
        @endif
    </div>
</div>
@endif

{{-- Asset expiry alerts (HR/Admin) --}}
@if(($isAdmin || $isHr) && isset($assetsExpiring) && $assetsExpiring->isNotEmpty())
<div class="mb-6 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
    <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-200 mb-2">Asset expiry alerts</h3>
    <ul class="text-sm text-amber-700 dark:text-amber-300 space-y-1">
        @foreach($assetsExpiring as $a)
        <li>
            <a href="{{ route('assets.show', $a) }}" class="wise-link font-medium">{{ $a->name }}</a>
            — {{ $a->expiry_date->format('Y-m-d') }}
            @if($a->isExpired())<span class="text-red-600 dark:text-red-400">(Expired)</span>@else<span>(Expiring soon)</span>@endif
        </li>
        @endforeach
    </ul>
    <a href="{{ route('assets.index') }}" class="inline-block mt-2 text-sm font-medium wise-link">View all assets →</a>
</div>
@endif

{{-- Quick actions (Admin/HR/Accounts only) --}}
@if($isAdmin || $isHr || $isAccounts)
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 mb-8">
    <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Quick actions</h3>
    <div class="flex flex-wrap gap-3">
        @if($isAdmin || $isHr)
        <a href="{{ route('attendance.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 wise-btn text-white rounded-lg text-sm font-medium">Record attendance</a>
        <a href="{{ route('leave.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 wise-btn text-white rounded-lg text-sm font-medium">Submit leave request</a>
        <a href="{{ route('employee.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700/50">Add employee</a>
        @endif
        @if($isAdmin || $isAccounts)
        <a href="{{ route('payroll.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700/50">New payroll run</a>
        @endif
        @if($isAdmin)
        <a href="{{ route('settings.general') }}" class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700/50">Settings</a>
        @endif
    </div>
</div>
@endif

{{-- Recent activity or welcome tip (Admin/HR see this; employees see simple message below) --}}
@if($isAdmin || $isHr || $isAccounts)
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @if($recentActivity->isNotEmpty())
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6">
        <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Recent activity</h3>
        <ul class="space-y-3">
            @foreach($recentActivity as $log)
            <li class="flex items-start gap-3 text-sm">
                <span class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0" style="background: var(--wise-primary);"></span>
                <div>
                    <span class="text-slate-700 dark:text-slate-300">{{ $log->description ?? $log->action }}</span>
                    <span class="text-slate-500 dark:text-slate-400 text-xs ml-1">{{ $log->created_at->diffForHumans() }}</span>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
    @else
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6">
        <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Getting started</h3>
        <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
            <li class="flex items-center gap-2">✓ Add employees and assign them to branches</li>
            <li class="flex items-center gap-2">✓ Record attendance and manage leave requests</li>
            <li class="flex items-center gap-2">✓ Run payroll for your team</li>
            <li class="flex items-center gap-2">✓ Customize logo and theme in Settings</li>
        </ul>
    </div>
    @endif
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6">
        <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">At a glance</h3>
        <p class="text-slate-600 dark:text-slate-400 text-sm">Your HR hub is ready. Use the quick actions above or the sidebar to manage employees, attendance, leave, and payroll. Everything stays consistent with your company settings and theme.</p>
    </div>
</div>
@else
{{-- Employee-only: short friendly block after check-in/out --}}
@if(auth()->user()->employee_id)
<div class="bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200/50 dark:border-slate-700/50 p-6">
    <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Your space</h3>
    <p class="text-slate-600 dark:text-slate-400 text-sm">Use the sidebar to open Leave (request time off), About, or contact your manager for any help.</p>
</div>
@endif
@endif
@endsection

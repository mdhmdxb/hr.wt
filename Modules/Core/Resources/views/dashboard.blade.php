@extends('core::layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
@php
    $hour = (int) date('H');
    if ($hour < 12) { $greeting = 'Good morning'; } elseif ($hour < 17) { $greeting = 'Good afternoon'; } else { $greeting = 'Good evening'; }
    $user = auth()->user();
    $isAdmin = $user->isAdmin();
    $isHr = $user->isHr();
    $isAccounts = $user->isAccounts();
    $isManager = $user->isManager();
    $canSeeTeamDashboard = $isAdmin || $isHr || $isManager || $user->hasPermission('manage_leave');
@endphp

{{-- Hero welcome --}}
@php $isEmployeeOnly = auth()->user()->employee_id && !$isAdmin && !$isHr && !$isAccounts; @endphp
<div class="relative overflow-hidden rounded-2xl mb-6 p-8 md:p-10 text-white shadow-xl" style="background: linear-gradient(135deg, var(--wise-primary) 0%, var(--wise-secondary) 50%, var(--wise-accent) 100%);">
    <div class="relative z-10">
        <p class="text-white/90 text-sm font-medium uppercase tracking-wider mb-1">{{ $greeting }}</p>
        <h2 class="wise-heading text-3xl md:text-4xl font-bold mb-2">{{ $user->name }}</h2>
        <p class="text-white/90 max-w-xl">@if($isEmployeeOnly) Your dashboard. View your attendance and activity. @else Here’s what’s happening across your organization today. @endif</p>
    </div>
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
    @if($isEmployeeOnly && ($isBirthday ?? false) && isset($myEmployee))
        <div class="mt-4 inline-flex items-center gap-3 rounded-xl bg-white/10 px-4 py-2 backdrop-blur-sm animate-pulse">
            <div class="text-2xl">🎉</div>
            <div>
                <p class="wise-heading text-sm font-semibold">Happy birthday, {{ $myEmployee->full_name }}!</p>
                <p class="text-xs text-white/80">Wishing you a wonderful year ahead from Wise HRM.</p>
            </div>
        </div>
    @endif
</div>

{{-- One-click check-in / check-out for employees (hidden by default; Owner can enable in Owner Portal) --}}
@if(auth()->user()->employee_id && ($showIndividualCheckin ?? false))
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

{{-- Everything below is for Admin/HR/Managers/Accounts. Employees only see greeting + check-in/out above. --}}
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

{{-- Stats grid (Admin/HR/Manager or manage_leave) --}}
@if($canSeeTeamDashboard)
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
        @if($isAdmin || $isHr || $isManager || $user->hasPermission('manage_leave'))
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

{{-- Asset & document expiry, upcoming vacations & birthdays (HR/Admin) — order from settings --}}
@if($isAdmin || $isHr)
@php
    $gridCardKeys = ['assets_expiring_soon', 'assets_expired', 'documents_expiring_soon', 'documents_expired', 'upcoming_vacations', 'birthdays'];
    $gridOrder = array_values(array_intersect($dashboardCardOrder ?? [], $gridCardKeys));
@endphp
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    @foreach($gridOrder as $cardKey)
    @if($cardKey === 'assets_expiring_soon' && isset($assetsExpiringSoon) && $assetsExpiringSoon->isNotEmpty())
    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
        <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-200 mb-2">Assets expiring soon</h3>
        <ul class="text-sm text-amber-700 dark:text-amber-300 space-y-1">
            @foreach($assetsExpiringSoon as $a)
            <li>
                <a href="{{ route('assets.show', $a) }}" class="wise-link font-medium">{{ $a->name }}</a>
                — {{ $a->expiry_date->format('Y-m-d') }}
            </li>
            @endforeach
        </ul>
        <a href="{{ route('assets.index') }}" class="inline-block mt-2 text-sm font-medium wise-link">View all assets →</a>
    </div>
    @elseif($cardKey === 'assets_expired' && isset($assetsExpired) && $assetsExpired->isNotEmpty())
    <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
        <h3 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">Assets already expired</h3>
        <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
            @foreach($assetsExpired as $a)
            <li>
                <a href="{{ route('assets.show', $a) }}" class="wise-link font-medium">{{ $a->name }}</a>
                — {{ $a->expiry_date->format('Y-m-d') }}
            </li>
            @endforeach
        </ul>
        <a href="{{ route('assets.index') }}" class="inline-block mt-2 text-sm font-medium wise-link">View all assets →</a>
    </div>
    @elseif($cardKey === 'documents_expiring_soon' && isset($documentsExpiringSoon) && $documentsExpiringSoon->isNotEmpty())
    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
        <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-200 mb-2">Documents expiring soon</h3>
        <ul class="text-sm text-amber-700 dark:text-amber-300 space-y-1">
            @foreach($documentsExpiringSoon as $doc)
            <li>
                <a href="{{ route('documents.show', $doc) }}" class="wise-link font-medium">{{ $doc->employee->full_name ?? '—' }}</a>
                — {{ \Modules\Core\Models\EmployeeDocument::typeOptions()[$doc->type] ?? $doc->type }} — {{ $doc->expiry_date->format('Y-m-d') }}
            </li>
            @endforeach
        </ul>
        <a href="{{ route('documents.index', ['expiring' => 1]) }}" class="inline-block mt-2 text-sm font-medium wise-link">View documents →</a>
    </div>
    @elseif($cardKey === 'documents_expired' && isset($documentsExpired) && $documentsExpired->isNotEmpty())
    <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
        <h3 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">Documents already expired</h3>
        <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
            @foreach($documentsExpired as $doc)
            <li>
                <a href="{{ route('documents.show', $doc) }}" class="wise-link font-medium">{{ $doc->employee->full_name ?? '—' }}</a>
                — {{ \Modules\Core\Models\EmployeeDocument::typeOptions()[$doc->type] ?? $doc->type }} — {{ $doc->expiry_date->format('Y-m-d') }}
            </li>
            @endforeach
        </ul>
        <a href="{{ route('documents.index') }}" class="inline-block mt-2 text-sm font-medium wise-link">View documents →</a>
    </div>
    @elseif($cardKey === 'upcoming_vacations' && isset($upcomingLeaves) && $upcomingLeaves->isNotEmpty())
    <div class="p-4 rounded-xl bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800">
        <h3 class="text-sm font-semibold text-sky-800 dark:text-sky-200 mb-2">Upcoming vacations</h3>
        <ul class="text-sm text-sky-800 dark:text-sky-100 space-y-1">
            @foreach($upcomingLeaves as $lr)
            <li>
                <span class="font-medium">{{ $lr->employee->full_name ?? '—' }}</span>
                — {{ $lr->start_date->format('Y-m-d') }} → {{ $lr->end_date->format('Y-m-d') }}
                @if($lr->leaveType)
                    <span class="text-xs text-sky-700 dark:text-sky-300">({{ $lr->leaveType->name }})</span>
                @endif
            </li>
            @endforeach
        </ul>
        <a href="{{ route('leave.index') }}?status=approved" class="inline-block mt-2 text-sm font-medium wise-link">View all leave →</a>
    </div>
    @elseif($cardKey === 'birthdays' && isset($upcomingBirthdays) && $upcomingBirthdays->isNotEmpty())
    <div class="p-4 rounded-xl bg-pink-50 dark:bg-pink-900/20 border border-pink-200 dark:border-pink-800">
        <h3 class="text-sm font-semibold text-pink-800 dark:text-pink-100 mb-2">Birthdays</h3>
        <ul class="text-sm text-pink-800 dark:text-pink-100 space-y-1">
            @foreach($upcomingBirthdays as $row)
            @php $emp = $row['employee']; $date = $row['next_birthday']; $isToday = $row['is_today'] ?? false; @endphp
            <li>
                <span class="font-medium">{{ $emp->full_name }}</span>
                — @if($isToday)<span class="font-medium">Today</span>@else{{ $date->format('M d') }}@endif
                @if($emp->branch) <span class="text-xs text-pink-700 dark:text-pink-300">({{ $emp->branch->name }})</span> @endif
            </li>
            @endforeach
        </ul>
    </div>
    @endif
    @endforeach
</div>
@endif

{{-- Manager / approver: quick view of team vacations & birthdays (no assets/documents) --}}
@if(!$isAdmin && !$isHr && $canSeeTeamDashboard)
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    @if(isset($upcomingLeaves) && $upcomingLeaves->isNotEmpty())
    <div class="p-4 rounded-xl bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800">
        <h3 class="text-sm font-semibold text-sky-800 dark:text-sky-200 mb-2">Your team – upcoming vacations</h3>
        <ul class="text-sm text-sky-800 dark:text-sky-100 space-y-1">
            @foreach($upcomingLeaves as $lr)
            <li>
                <span class="font-medium">{{ $lr->employee->full_name ?? '—' }}</span>
                — {{ $lr->leaveType->name ?? 'Leave' }}
                ({{ $lr->start_date->format('Y-m-d') }} → {{ $lr->end_date->format('Y-m-d') }})
            </li>
            @endforeach
        </ul>
        <a href="{{ route('leave.index') }}" class="inline-block mt-2 text-sm font-medium wise-link">Open leave list →</a>
    </div>
    @endif
    @if(isset($upcomingBirthdays) && $upcomingBirthdays->isNotEmpty())
    <div class="p-4 rounded-xl bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800">
        <h3 class="text-sm font-semibold text-purple-800 dark:text-purple-200 mb-2">Your team – upcoming birthdays</h3>
        <ul class="text-sm text-purple-800 dark:text-purple-100 space-y-1">
            @foreach($upcomingBirthdays as $row)
            @php /** @var \Modules\Core\Models\Employee $emp */ $emp = $row['employee']; $d = $row['next_birthday']; @endphp
            <li>
                <span class="font-medium">{{ $emp->full_name }}</span>
                — {{ $d->format('Y-m-d') }} @if(!empty($row['is_today'])) <span class="text-xs font-semibold">(Today)</span> @endif
            </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@endif

{{-- Calendar (Gregorian + Lunar) & public holidays (Admin/HR) --}}
@if($isAdmin || $isHr)
@if(in_array('calendar', $dashboardCardOrder) && !empty($calendarWeeks) && count($calendarWeeks) > 0)
@php
    $calMonth = (int) ($calendarMonthNum ?? date('n'));
    $calYear = (int) ($calendarYear ?? date('Y'));
    $calPrev = $calMonth <= 1 ? [$calYear - 1, 12] : [$calYear, $calMonth - 1];
    $calNext = $calMonth >= 12 ? [$calYear + 1, 1] : [$calYear, $calMonth + 1];
    $q = request()->query();
    $prevUrl = route('dashboard', array_merge($q, ['calendar_year' => $calPrev[0], 'calendar_month' => $calPrev[1]]));
    $nextUrl = route('dashboard', array_merge($q, ['calendar_year' => $calNext[0], 'calendar_month' => $calNext[1]]));
@endphp
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 mb-6">
    <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Calendar</h3>
    <div class="flex flex-wrap items-center gap-4 mb-4">
        <a href="{{ $prevUrl }}" class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50">← Previous</a>
        <span class="font-medium text-slate-800 dark:text-slate-100">{{ $calendarMonth ?? now()->format('F Y') }}</span>
        <a href="{{ $nextUrl }}" class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50">Next →</a>
        <form method="get" action="{{ route('dashboard') }}" class="flex items-center gap-2 flex-wrap">
            @foreach(request()->query() as $k => $v) @if($k !== 'calendar_month' && $k !== 'calendar_year')<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif @endforeach
            <select name="calendar_month" class="rounded border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1 text-sm" onchange="this.form.submit()">
                @foreach(['1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July','8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December'] as $m => $label)
                <option value="{{ $m }}" {{ (isset($calendarMonthNum) && (int)$calendarMonthNum === (int)$m) ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="calendar_year" class="rounded border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1 text-sm" onchange="this.form.submit()">
                @for($y = (isset($today) ? $today->year : date('Y')) - 2; $y <= (isset($today) ? $today->year : date('Y')) + 2; $y++)
                <option value="{{ $y }}" {{ (isset($calendarYear) && (int)$calendarYear === $y) ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>
    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Gregorian / Hijri (Lunar) @if(!empty($companyCountry)) · Country: {{ \Modules\Core\Helpers\CountryList::codes()[$companyCountry] ?? $companyCountry }} @endif</p>
    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-600">
                    <th class="text-left py-2 px-1 text-slate-600 dark:text-slate-400 font-medium">Sun</th>
                    <th class="text-left py-2 px-1 text-slate-600 dark:text-slate-400 font-medium">Mon</th>
                    <th class="text-left py-2 px-1 text-slate-600 dark:text-slate-400 font-medium">Tue</th>
                    <th class="text-left py-2 px-1 text-slate-600 dark:text-slate-400 font-medium">Wed</th>
                    <th class="text-left py-2 px-1 text-slate-600 dark:text-slate-400 font-medium">Thu</th>
                    <th class="text-left py-2 px-1 text-slate-600 dark:text-slate-400 font-medium">Fri</th>
                    <th class="text-left py-2 px-1 text-slate-600 dark:text-slate-400 font-medium">Sat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($calendarWeeks as $week)
                <tr class="border-b border-slate-100 dark:border-slate-700/50">
                    @foreach($week as $day)
                    <td class="align-top py-2 px-1 w-[14%]">
                        <div class="min-h-[3.5rem] rounded-lg p-2 {{ $day['in_month'] ? 'bg-slate-50 dark:bg-slate-700/30' : 'bg-slate-100/50 dark:bg-slate-800/50' }} {{ $day['is_today'] ? 'ring-2 ring-indigo-500 dark:ring-indigo-400' : '' }} {{ $day['is_holiday'] ? 'bg-amber-100 dark:bg-amber-900/30' : '' }}">
                            <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $day['day'] }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $day['hijri'] }}</div>
                            @if($day['holiday_name'])
                            <div class="text-xs font-medium text-amber-700 dark:text-amber-300 mt-0.5 truncate" title="{{ $day['holiday_name'] }}">{{ Str::limit($day['holiday_name'], 12) }}</div>
                            @endif
                        </div>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Public holidays: this month & upcoming --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    @if(in_array('public_holidays_month', $dashboardCardOrder) && isset($publicHolidaysThisMonth) && $publicHolidaysThisMonth->isNotEmpty())
    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
        <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-200 mb-2">Public holidays this month</h3>
        <ul class="text-sm text-amber-800 dark:text-amber-100 space-y-1">
            @foreach($publicHolidaysThisMonth as $h)
            <li>
                <span class="font-medium">{{ $h->name }}</span>
                — {{ $h->date->format('M j') }}@if($h->end_date && $h->end_date->ne($h->date)) – {{ $h->end_date->format('M j') }}@endif
            </li>
            @endforeach
        </ul>
        <a href="{{ route('holidays.index') }}" class="inline-block mt-2 text-sm font-medium wise-link">Manage public holidays →</a>
    </div>
    @elseif(in_array('public_holidays_month', $dashboardCardOrder))
    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
        <h3 class="text-sm font-semibold text-slate-600 dark:text-slate-300 mb-2">Public holidays this month</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400">None set for this month. Set company country in Settings and add holidays.</p>
        <a href="{{ route('holidays.index') }}" class="inline-block mt-2 text-sm font-medium wise-link">Manage public holidays →</a>
    </div>
    @endif

    @if(in_array('upcoming_public_holidays', $dashboardCardOrder) && isset($upcomingPublicHolidays) && $upcomingPublicHolidays->isNotEmpty())
    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800">
        <h3 class="text-sm font-semibold text-emerald-800 dark:text-emerald-200 mb-2">Upcoming public holidays</h3>
        <ul class="text-sm text-emerald-800 dark:text-emerald-100 space-y-1">
            @foreach($upcomingPublicHolidays->take(8) as $h)
            <li>
                <span class="font-medium">{{ $h->name }}</span>
                — {{ $h->date->format('M j, Y') }}@if($h->end_date && $h->end_date->ne($h->date)) – {{ $h->end_date->format('M j') }}@endif
            </li>
            @endforeach
        </ul>
        <a href="{{ route('holidays.index') }}" class="inline-block mt-2 text-sm font-medium wise-link">View all →</a>
    </div>
    @elseif(in_array('upcoming_public_holidays', $dashboardCardOrder))
    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
        <h3 class="text-sm font-semibold text-slate-600 dark:text-slate-300 mb-2">Upcoming public holidays</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400">None in the next 60 days. Add holidays in Public Holidays.</p>
        <a href="{{ route('holidays.index') }}" class="inline-block mt-2 text-sm font-medium wise-link">Manage public holidays →</a>
    </div>
    @endif
</div>
@endif

{{-- Quick actions (Admin/HR/Accounts only) --}}
@if(($isAdmin || $isHr || $isAccounts) && in_array('quick_actions', $dashboardCardOrder))
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
{{-- Employee-only: upcoming leave + info block --}}
@if(auth()->user()->employee_id)
@php
    $myUpcomingLeave = \Modules\Leave\Models\LeaveRequest::with('leaveType')
        ->where('employee_id', auth()->user()->employee_id)
        ->where('status', \Modules\Leave\Models\LeaveRequest::STATUS_APPROVED)
        ->whereDate('end_date', '>=', now()->toDateString())
        ->orderBy('start_date')
        ->take(3)
        ->get();
@endphp
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200/50 dark:border-slate-700/50 p-6">
        <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Your space</h3>
        <p class="text-slate-600 dark:text-slate-400 text-sm">Use the sidebar to open My Attendance, My Leave (request time off), and About. Contact your manager or HR if you need help.</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200/50 dark:border-slate-700/50 p-6">
        <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Your upcoming leave</h3>
        @if($myUpcomingLeave->isEmpty())
            <p class="text-sm text-slate-500 dark:text-slate-400">No approved upcoming leave yet.</p>
        @else
        <ul class="text-sm text-slate-700 dark:text-slate-300 space-y-1">
            @foreach($myUpcomingLeave as $lr)
            <li>
                {{ $lr->start_date->format('Y-m-d') }} → {{ $lr->end_date->format('Y-m-d') }}
                @if($lr->leaveType)
                    <span class="text-xs text-slate-500 dark:text-slate-400">({{ $lr->leaveType->name }})</span>
                @endif
            </li>
            @endforeach
        </ul>
        <a href="{{ route('my-leave.index') }}" class="inline-block mt-2 text-sm font-medium wise-link">View all my leave →</a>
        @endif
    </div>
</div>
@endif
@endif
@endsection

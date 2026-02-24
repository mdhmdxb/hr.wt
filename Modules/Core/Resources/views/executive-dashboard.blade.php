@extends('core::layouts.app')

@section('title', 'Executive Dashboard')
@section('heading', 'Executive Dashboard')

@section('content')
@php
    $currency = \Modules\Settings\Services\SettingsService::get('currency', 'USD');
    $maxCost = $departmentCosts->isEmpty() ? 1 : $departmentCosts->max('cost');
@endphp

{{-- Month/Year filter --}}
<form method="GET" action="{{ route('dashboard.executive') }}" class="mb-6 p-4 bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200/50 dark:border-slate-700/50 flex flex-wrap items-end gap-3">
    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Data for</span>
    <select name="month" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" onchange="this.form.submit()">
        @foreach($months ?? [] as $m => $label)
            <option value="{{ $m }}" {{ ($filterMonth ?? now()->month) == $m ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <select name="year" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" onchange="this.form.submit()">
        @for($y = now()->year + 1; $y >= now()->year - 5; $y--)
            <option value="{{ $y }}" {{ ($filterYear ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endfor
    </select>
</form>

{{-- Hero --}}
<div class="relative rounded-2xl mb-8 p-8 md:p-10 text-white shadow-2xl overflow-hidden" style="background: linear-gradient(135deg, var(--wise-primary) 0%, var(--wise-secondary) 40%, var(--wise-accent) 100%);">
    <div class="absolute inset-0 opacity-80" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.08\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    <div class="relative z-10">
        <p class="text-white/90 text-sm font-semibold uppercase tracking-widest mb-1">Executive Overview</p>
        <h2 class="wise-heading text-3xl md:text-4xl font-bold mb-2">At a glance</h2>
        <p class="text-white/90 max-w-xl">Key metrics and department cost breakdown for management.</p>
    </div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-2xl"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2 blur-xl"></div>
</div>

{{-- KPI cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 md:gap-6 mb-8">
    <div class="group bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total employees</p>
                <p class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-slate-100 mt-1">{{ number_format($totalEmployees) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Active</p>
            </div>
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl bg-slate-100 dark:bg-slate-700 group-hover:scale-110 transition-transform" style="color: var(--wise-primary);">👥</div>
        </div>
    </div>
    <div class="group bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Attendance rate</p>
                <p class="text-3xl md:text-4xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $attendanceRate }}%</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $months[$filterMonth ?? now()->month] ?? '' }} {{ $filterYear ?? now()->year }}</p>
            </div>
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">🕐</div>
        </div>
    </div>
    <div class="group bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Payroll this month</p>
                <p class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-slate-100 mt-1">{{ $currency }} {{ number_format($totalPayroll, 0) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $payrollRunsCount }} run(s) in period</p>
            </div>
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 group-hover:scale-110 transition-transform">💰</div>
        </div>
    </div>
    <div class="group bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Pending leave</p>
                <p class="text-3xl md:text-4xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $pendingLeave }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Awaiting approval</p>
            </div>
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform">📅</div>
        </div>
    </div>
    <div class="group bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Expiring soon</p>
                <p class="text-3xl md:text-4xl font-bold text-orange-600 dark:text-orange-400 mt-1">{{ $expiringCount ?? 0 }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Documents (30 days)</p>
            </div>
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 group-hover:scale-110 transition-transform">📂</div>
        </div>
    </div>
</div>

{{-- Charts row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6">
        <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Department cost breakdown</h3>
        @if($departmentCosts->isEmpty())
        <p class="text-slate-500 dark:text-slate-400 text-sm py-8 text-center">No department salary data yet.</p>
        @else
        <div class="space-y-4">
            @foreach($departmentCosts as $dept)
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium text-slate-700 dark:text-slate-300 truncate pr-2">{{ $dept['name'] }}</span>
                    <span class="text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $currency }} {{ number_format($dept['cost'], 0) }}</span>
                </div>
                <div class="h-3 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500" style="width: {{ $maxCost > 0 ? min(100, 100 * $dept['cost'] / $maxCost) : 0 }}%; background: linear-gradient(90deg, var(--wise-primary), var(--wise-accent));"></div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6">
        <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Leave trend (last 6 months)</h3>
        @if(collect($leaveTrend)->sum('count') == 0)
        <p class="text-slate-500 dark:text-slate-400 text-sm py-8 text-center">No leave data in this period.</p>
        @else
        <div class="flex items-end gap-2 h-48">
            @foreach($leaveTrend as $t)
            @php $maxLeave = max(1, collect($leaveTrend)->max('count')); $h = 100 * $t['count'] / $maxLeave; @endphp
            <div class="flex-1 flex flex-col items-center gap-2">
                <div class="w-full flex flex-col justify-end h-36 rounded-t-lg overflow-hidden" title="{{ $t['month'] }}: {{ $t['count'] }}">
                    <div class="w-full rounded-t transition-all duration-500 hover:opacity-90" style="height: {{ $h }}%; min-height: {{ $t['count'] > 0 ? 4 : 0 }}px; background: linear-gradient(180deg, var(--wise-accent), var(--wise-primary));"></div>
                </div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $t['month'] }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- Expiry alerts --}}
@if(isset($expiringSoon) && $expiringSoon->isNotEmpty())
<div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 mb-8">
    <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Documents expiring in 30 days</h3>
    <ul class="space-y-2">
        @foreach($expiringSoon as $doc)
        <li class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700/50 last:border-0">
            <span class="text-slate-700 dark:text-slate-300">{{ $doc->employee->full_name ?? '—' }} · {{ \Modules\Core\Models\EmployeeDocument::typeOptions()[$doc->type] ?? $doc->type }}</span>
            <span class="text-slate-500 dark:text-slate-400 text-sm">{{ $doc->expiry_date->format('M j, Y') }}</span>
            <a href="{{ route('documents.show', $doc) }}" class="text-sm wise-link">View</a>
        </li>
        @endforeach
    </ul>
    <a href="{{ route('documents.index') }}" class="inline-block mt-3 text-sm wise-link">View all documents →</a>
</div>
@endif

{{-- Quick links --}}
<div class="flex flex-wrap gap-3">
    <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 wise-btn text-white rounded-xl text-sm font-medium shadow-lg hover:shadow-xl transition-all">View reports →</a>
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all">Main dashboard</a>
</div>
@endsection

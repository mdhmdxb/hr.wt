@extends('core::layouts.app')

@section('title', 'Edit Payslip')
@section('heading', 'Edit Payslip')

@section('content')
<form method="POST" action="{{ route('payroll.payslip.update', $payslip) }}" class="max-w-3xl space-y-6" id="payslip-form">
    @csrf
    @method('PUT')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-6">
        <p class="text-sm text-slate-600 dark:text-slate-400"><strong>{{ $payslip->employee->full_name ?? '—' }}</strong> · {{ $payslip->payrollRun->period_start->format('M Y') }}</p>

        {{-- Salary & allowances --}}
        <div>
            <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Salary & allowances</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Basic salary *</label>
                    <input type="number" name="basic_salary" value="{{ old('basic_salary', $payslip->basic_salary) }}" step="0.01" min="0" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @error('basic_salary')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Accommodation</label>
                    <input type="number" name="accommodation" value="{{ old('accommodation', $payslip->accommodation ?? 0) }}" step="0.01" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @error('accommodation')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Transportation</label>
                    <input type="number" name="transportation" value="{{ old('transportation', $payslip->transportation ?? 0) }}" step="0.01" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @error('transportation')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Food allowance</label>
                    <input type="number" name="food_allowance" value="{{ old('food_allowance', $payslip->food_allowance ?? 0) }}" step="0.01" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @error('food_allowance')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Other allowances</label>
                    <input type="number" name="other_allowances" value="{{ old('other_allowances', $payslip->other_allowances ?? 0) }}" step="0.01" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @error('other_allowances')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Bonus</label>
                    <input type="number" name="bonus" value="{{ old('bonus', $payslip->bonus ?? 0) }}" step="0.01" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @error('bonus')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Days --}}
        <div>
            <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Days</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Days worked</label>
                    <input type="number" name="days_worked" value="{{ old('days_worked', $payslip->days_worked) }}" step="0.01" min="0" placeholder="—" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @error('days_worked')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Days off</label>
                    <input type="number" name="days_off" value="{{ old('days_off', $payslip->days_off) }}" step="0.01" min="0" placeholder="—" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @error('days_off')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Holiday</label>
                    <input type="number" name="holiday" value="{{ old('holiday', $payslip->holiday) }}" step="0.01" min="0" placeholder="—" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @error('holiday')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Annual leave</label>
                    <input type="number" name="annual_leave" value="{{ old('annual_leave', $payslip->annual_leave) }}" step="0.01" min="0" placeholder="—" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @error('annual_leave')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Unpaid leave</label>
                    <input type="number" name="unpaid_leave" value="{{ old('unpaid_leave', $payslip->unpaid_leave) }}" step="0.01" min="0" placeholder="—" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @error('unpaid_leave')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Overtime --}}
        <div>
            <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Overtime</h3>
            @if(isset($offDayWorkHours) && ($offDayWorkHours > 0 || !empty($offDayWorkDetails)))
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                <strong>Hours worked on off days (from attendance):</strong> {{ number_format($offDayWorkHours ?? 0, 2) }}
                @if(!empty($offDayWorkDetails))
                    —
                    @foreach($offDayWorkDetails as $d)
                        {{ $d['date'] }} ({{ $d['hours'] }} h, {{ $d['label'] }})@if(!$loop->last); @endif
                    @endforeach
                @endif
            </p>
            @endif
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Regular overtime hours</label>
                    <input type="number" name="overtime_hours" value="{{ old('overtime_hours', $payslip->overtime_hours ?? 0) }}" step="0.01" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @error('overtime_hours')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Overtime premium (MOHRE)</label>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Off days / night time per MOHRE UAE</p>
                    <input type="number" name="overtime_premium" value="{{ old('overtime_premium', $payslip->overtime_premium ?? 0) }}" step="0.01" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @error('overtime_premium')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Overtime bonus + transport + food</label>
                    <input type="number" name="overtime_bonus_transport_food" value="{{ old('overtime_bonus_transport_food', $payslip->overtime_bonus_transport_food ?? 0) }}" step="0.01" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    @error('overtime_bonus_transport_food')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Deduction / adjustment --}}
        <div>
            <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Deduction & remarks</h3>
            <div class="grid grid-cols-1 gap-4">
                <div class="max-w-xs">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Salary adjustment (deduction)</label>
                    <input type="number" name="salary_adjustment" value="{{ old('salary_adjustment', $payslip->salary_adjustment ?? 0) }}" step="0.01" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" placeholder="0">
                    @error('salary_adjustment')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Remarks</label>
                    <textarea name="remarks" rows="2" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" placeholder="Optional notes">{{ old('remarks', $payslip->remarks) }}</textarea>
                    @error('remarks')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <p class="text-sm text-slate-500 dark:text-slate-400">Total net salary and Total WPS salary are calculated on save (Basic + all allowances − salary adjustment).</p>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Update Payslip</button>
            <a href="{{ route('payroll.show', $payslip->payrollRun) }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        </div>
    </div>
</form>
@endsection

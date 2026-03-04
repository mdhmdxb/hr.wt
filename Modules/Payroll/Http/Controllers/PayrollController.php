<?php

namespace Modules\Payroll\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Attendance\Models\Attendance;
use Modules\Core\Models\Employee;
use Modules\Core\Models\PublicHoliday;
use Modules\Payroll\Models\PayrollRun;
use Modules\Payroll\Models\Payslip;
use Modules\Settings\Http\Controllers\SettingsController;
use Modules\Settings\Models\Setting as SettingsSetting;

class PayrollController extends Controller
{
    public function index()
    {
        $runs = PayrollRun::withCount('payslips')->latest('period_end')->paginate(15);
        return view('payroll::index', compact('runs'));
    }

    public function create()
    {
        return view('payroll::create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        $exists = PayrollRun::where('period_start', $request->period_start)->where('period_end', $request->period_end)->first();
        if ($exists) {
            return back()->withInput()->with('error', 'A payroll run already exists for this period.');
        }

        $run = PayrollRun::create([
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'status' => PayrollRun::STATUS_DRAFT,
        ]);

        $employees = Employee::where('status', 'active')->get();
        $periodStart = $request->period_start;
        $periodEnd = $request->period_end;
        foreach ($employees as $emp) {
            $basic = (float) ($emp->basic_salary ?? 0);
            $accommodation = (float) ($emp->accommodation ?? 0);
            $transportation = (float) ($emp->transportation ?? 0);
            $foodAllowance = (float) ($emp->food_allowance ?? 0);
            $otherAllowances = (float) ($emp->other_allowances ?? 0);
            $totalAllowances = $accommodation + $transportation + $foodAllowance + $otherAllowances;
            $overtimeMinutes = (int) Attendance::where('employee_id', $emp->id)
                ->whereBetween('date', [$periodStart, $periodEnd])
                ->sum('overtime_minutes');
            $overtimeHours = round($overtimeMinutes / 60, 2);
            $deductions = 0;
            $net = round($basic + $totalAllowances - $deductions, 2);
            Payslip::create([
                'payroll_run_id' => $run->id,
                'employee_id' => $emp->id,
                'basic_salary' => $basic,
                'accommodation' => $accommodation,
                'transportation' => $transportation,
                'food_allowance' => $foodAllowance,
                'other_allowances' => $otherAllowances,
                'bonus' => 0,
                'days_worked' => null,
                'days_off' => null,
                'holiday' => null,
                'annual_leave' => null,
                'unpaid_leave' => null,
                'overtime_hours' => $overtimeHours,
                'overtime_premium' => 0,
                'overtime_bonus_transport_food' => 0,
                'salary_adjustment' => 0,
                'allowances' => $totalAllowances,
                'deductions' => $deductions,
                'net_pay' => $net,
                'total_wps_salary' => $net,
                'remarks' => null,
            ]);
        }

        return redirect()->route('payroll.show', $run)->with('success', 'Payroll run created with ' . $employees->count() . ' payslips.');
    }

    public function show(Request $request, PayrollRun $payroll)
    {
        $payroll->load(['payslips.employee']);
        $allPayslips = $payroll->payslips;
        $employeeOptions = $allPayslips->map(fn ($ps) => $ps->employee)->filter()->unique('id')->sortBy('first_name');
        $selectedIds = collect($request->input('employees', []))->map(fn ($v) => (int) $v)->filter()->all();
        $payslips = $allPayslips;
        if (! empty($selectedIds)) {
            $payslips = $allPayslips->whereIn('employee_id', $selectedIds);
        }
        $payroll->setRelation('payslips', $payslips->values());
        return view('payroll::show', [
            'payroll' => $payroll,
            'employeeOptions' => $employeeOptions,
            'selectedEmployeeIds' => $selectedIds,
        ]);
    }

    public function finalize(PayrollRun $payroll)
    {
        if ($payroll->status !== PayrollRun::STATUS_DRAFT) {
            return back()->with('error', 'Only draft runs can be finalized.');
        }
        $payroll->update(['status' => PayrollRun::STATUS_FINALIZED]);
        return back()->with('success', 'Payroll run finalized.');
    }

    public function payslip(Payslip $payslip)
    {
        $payslip->load(['payrollRun', 'employee']);
        if (empty($payslip->verification_token)) {
            $payslip->update(['verification_token' => \Illuminate\Support\Str::random(48)]);
        }
        [$offDayWorkHours, $offDayWorkDetails] = self::computeOffDayWorkHours(
            $payslip->employee,
            $payslip->payrollRun->period_start,
            $payslip->payrollRun->period_end
        );
        $payslipDisplayRaw = SettingsSetting::getValue('payslip_display', null);
        $payslipDisplay = is_string($payslipDisplayRaw) ? (json_decode($payslipDisplayRaw, true) ?: array_keys(SettingsController::payslipDisplayKeys())) : array_keys(SettingsController::payslipDisplayKeys());
        $letterFooterText = SettingsSetting::getValue('letter_footer_text');
        if (! is_string($letterFooterText) || trim($letterFooterText) === '') {
            $letterFooterText = SettingsController::defaultLetterFooterText();
        }
        $documentStampOn = json_decode(SettingsSetting::getValue('document_stamp_on', '[]') ?: '[]', true) ?: [];
        $stampImageUrl = null;
        $showStamp = false;
        if (in_array('payslip', $documentStampOn, true)) {
            $stampPath = SettingsSetting::getValue('company_stamp_path');
            if ($stampPath) {
                // Always expose the public URL; if storage link is missing the browser will show a broken image, which helps diagnose.
                $stampImageUrl = asset('storage/' . ltrim($stampPath, '/'));
                $showStamp = true;
            }
        }
        return view('payroll::payslip', compact('payslip', 'offDayWorkHours', 'offDayWorkDetails', 'payslipDisplay', 'letterFooterText', 'showStamp', 'stampImageUrl'));
    }

    public function payslipQr(Payslip $payslip)
    {
        if (empty($payslip->verification_token)) {
            $payslip->update(['verification_token' => \Illuminate\Support\Str::random(48)]);
        }
        $url = url()->route('payroll.verify', ['payslip' => $payslip->id, 'token' => $payslip->verification_token]);
        $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(180)->margin(2)->generate($url);
        return response($svg)->header('Content-Type', 'image/svg+xml');
    }

    public function editPayslip(Payslip $payslip)
    {
        $payslip->load(['payrollRun', 'employee']);
        if ($payslip->payrollRun->status !== PayrollRun::STATUS_DRAFT) {
            return redirect()->route('payroll.show', $payslip->payrollRun)->with('error', 'Only draft runs can be edited.');
        }
        [$offDayWorkHours, $offDayWorkDetails] = self::computeOffDayWorkHours(
            $payslip->employee,
            $payslip->payrollRun->period_start,
            $payslip->payrollRun->period_end
        );
        return view('payroll::edit-payslip', compact('payslip', 'offDayWorkHours', 'offDayWorkDetails'));
    }

    public function updatePayslip(Request $request, Payslip $payslip)
    {
        if ($payslip->payrollRun->status !== PayrollRun::STATUS_DRAFT) {
            return back()->with('error', 'Only draft runs can be edited.');
        }
        $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'accommodation' => 'nullable|numeric|min:0',
            'transportation' => 'nullable|numeric|min:0',
            'food_allowance' => 'nullable|numeric|min:0',
            'other_allowances' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'days_worked' => 'nullable|numeric|min:0',
            'days_off' => 'nullable|numeric|min:0',
            'holiday' => 'nullable|numeric|min:0',
            'annual_leave' => 'nullable|numeric|min:0',
            'unpaid_leave' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_premium' => 'nullable|numeric|min:0',
            'overtime_bonus_transport_food' => 'nullable|numeric|min:0',
            'salary_adjustment' => 'nullable|numeric',
            'remarks' => 'nullable|string|max:1000',
        ]);
        $basic = (float) $request->basic_salary;
        $accommodation = (float) ($request->accommodation ?? 0);
        $transportation = (float) ($request->transportation ?? 0);
        $foodAllowance = (float) ($request->food_allowance ?? 0);
        $otherAllowances = (float) ($request->other_allowances ?? 0);
        $bonus = (float) ($request->bonus ?? 0);
        $overtimePremium = (float) ($request->overtime_premium ?? 0);
        $overtimeBonusTransportFood = (float) ($request->overtime_bonus_transport_food ?? 0);
        $salaryAdjustment = (float) ($request->salary_adjustment ?? 0);
        $totalAllowances = $accommodation + $transportation + $foodAllowance + $otherAllowances + $bonus + $overtimePremium + $overtimeBonusTransportFood;
        $deductions = $salaryAdjustment > 0 ? $salaryAdjustment : 0;
        $netPay = round($basic + $totalAllowances - $deductions, 2);
        $payslip->update([
            'basic_salary' => $basic,
            'accommodation' => $accommodation,
            'transportation' => $transportation,
            'food_allowance' => $foodAllowance,
            'other_allowances' => $otherAllowances,
            'bonus' => $bonus,
            'days_worked' => $request->filled('days_worked') ? (float) $request->days_worked : null,
            'days_off' => $request->filled('days_off') ? (float) $request->days_off : null,
            'holiday' => $request->filled('holiday') ? (float) $request->holiday : null,
            'annual_leave' => $request->filled('annual_leave') ? (float) $request->annual_leave : null,
            'unpaid_leave' => $request->filled('unpaid_leave') ? (float) $request->unpaid_leave : null,
            'overtime_hours' => (float) ($request->overtime_hours ?? 0),
            'overtime_premium' => $overtimePremium,
            'overtime_bonus_transport_food' => $overtimeBonusTransportFood,
            'salary_adjustment' => $salaryAdjustment,
            'allowances' => $totalAllowances,
            'deductions' => $deductions,
            'net_pay' => $netPay,
            'total_wps_salary' => $netPay,
            'remarks' => $request->filled('remarks') ? $request->remarks : null,
        ]);
        return redirect()->route('payroll.show', $payslip->payrollRun)->with('success', 'Payslip updated.');
    }

    /**
     * Compute hours worked on off days (public holiday, weekly off, alt. Saturday) in the given period.
     * Returns [totalHours, details] where details is array of ['date' => 'Y-m-d', 'hours' => float, 'label' => string].
     */
    public static function computeOffDayWorkHours(Employee $employee, $periodStart, $periodEnd): array
    {
        $start = Carbon::parse($periodStart)->startOfDay();
        $end = Carbon::parse($periodEnd)->endOfDay();
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->whereIn('status', [Attendance::STATUS_PRESENT, Attendance::STATUS_HALF_DAY])
            ->orderBy('date')
            ->get();

        $totalHours = 0.0;
        $details = [];
        foreach ($attendances as $att) {
            $date = $att->date instanceof Carbon ? $att->date : Carbon::parse($att->date);
            $isHoliday = PublicHoliday::isHoliday($date);
            $isWeeklyOff = $employee->isWeeklyOffDay($date);
            $isAltSat = $employee->isAlternateSaturdayOffDay($date);
            if (! $isHoliday && ! $isWeeklyOff && ! $isAltSat) {
                continue;
            }
            $label = $isHoliday ? 'Public holiday' : ($isAltSat ? 'Alt. Saturday off' : 'Weekly off');
            $hours = 0.0;
            if (! empty($att->check_in_at) && ! empty($att->check_out_at)) {
                $in = Carbon::parse($att->date->format('Y-m-d') . ' ' . $att->check_in_at);
                $out = Carbon::parse($att->date->format('Y-m-d') . ' ' . $att->check_out_at);
                $hours = round(max(0, $out->diffInMinutes($in) / 60), 2);
            }
            $totalHours += $hours;
            $details[] = ['date' => $date->format('Y-m-d'), 'hours' => $hours, 'label' => $label];
        }
        return [round($totalHours, 2), $details];
    }
}

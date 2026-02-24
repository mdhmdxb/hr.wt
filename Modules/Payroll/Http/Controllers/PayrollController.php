<?php

namespace Modules\Payroll\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Attendance\Models\Attendance;
use Modules\Core\Models\Employee;
use Modules\Payroll\Models\PayrollRun;
use Modules\Payroll\Models\Payslip;

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

    public function show(PayrollRun $payroll)
    {
        $payroll->load(['payslips.employee']);
        return view('payroll::show', compact('payroll'));
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
        return view('payroll::payslip', compact('payslip'));
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
        return view('payroll::edit-payslip', compact('payslip'));
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
}

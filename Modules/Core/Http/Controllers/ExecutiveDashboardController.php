<?php
/**
 * Wise HRM – Executive (MD) Dashboard
 * Developer: M H Morshed
 */

namespace Modules\Core\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Modules\Attendance\Models\Attendance;
use Modules\Core\Models\Department;
use Modules\Core\Models\Employee;
use Modules\Leave\Models\LeaveRequest;
use Modules\Core\Models\EmployeeDocument;
use Modules\Payroll\Models\PayrollRun;
use Modules\Payroll\Models\Payslip;

class ExecutiveDashboardController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $today = Carbon::today();
        $filterMonth = (int) $request->query('month', $today->month);
        $filterYear = (int) $request->query('year', $today->year);
        $filterMonth = max(1, min(12, $filterMonth));
        $filterYear = max(2020, min(2100, $filterYear));

        $startOfMonth = Carbon::createFromDate($filterYear, $filterMonth, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $totalEmployees = Employee::where('status', 'active')->count();
        $attendanceRecords = Attendance::whereBetween('date', [$startOfMonth, $endOfMonth])->where('status', 'present')->count();
        $workingDays = 0;
        $d = $startOfMonth->copy();
        while ($d <= $endOfMonth) {
            if (!$d->isWeekend()) {
                $workingDays++;
            }
            $d->addDay();
        }
        if ($workingDays <= 0) {
            $workingDays = 22;
        }
        $expectedChecks = $totalEmployees * $workingDays;
        $attendanceRate = $expectedChecks > 0 ? round(100 * $attendanceRecords / $expectedChecks, 1) : 0;

        $payrollRunsThisMonth = PayrollRun::whereBetween('period_end', [$startOfMonth, $endOfMonth])->get();
        $totalPayroll = Payslip::whereIn('payroll_run_id', $payrollRunsThisMonth->pluck('id'))->sum('net_pay');
        $pendingLeave = LeaveRequest::where('status', 'pending')->count();

        $departmentCosts = Department::with('branch')->get()
            ->map(function ($d) {
                $cost = $d->employees()->where('status', 'active')->sum('basic_salary');
                return ['name' => $d->name . ' (' . ($d->branch->name ?? '') . ')', 'cost' => (float) $cost];
            })
            ->filter(fn ($d) => $d['cost'] > 0)
            ->sortByDesc('cost')
            ->values();

        $leaveTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $today->copy()->subMonths($i);
            $leaveTrend[] = [
                'month' => $month->format('M Y'),
                'count' => LeaveRequest::whereYear('start_date', $month->year)->whereMonth('start_date', $month->month)->whereIn('status', ['approved', 'taken'])->count(),
            ];
        }

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = Carbon::createFromDate(2000, $m, 1)->format('F');
        }

        $expiringSoon = EmployeeDocument::with('employee')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>', $today)
            ->where('expiry_date', '<=', $today->copy()->addDays(30))
            ->orderBy('expiry_date')
            ->limit(10)
            ->get();
        $expiringCount = EmployeeDocument::whereNotNull('expiry_date')
            ->where('expiry_date', '>', $today)
            ->where('expiry_date', '<=', $today->copy()->addDays(30))
            ->count();

        return view('core::executive-dashboard', [
            'totalEmployees' => $totalEmployees,
            'attendanceRate' => $attendanceRate,
            'totalPayroll' => $totalPayroll,
            'pendingLeave' => $pendingLeave,
            'departmentCosts' => $departmentCosts,
            'leaveTrend' => $leaveTrend,
            'payrollRunsCount' => $payrollRunsThisMonth->count(),
            'filterMonth' => $filterMonth,
            'filterYear' => $filterYear,
            'months' => $months,
            'expiringSoon' => $expiringSoon,
            'expiringCount' => $expiringCount,
        ]);
    }
}

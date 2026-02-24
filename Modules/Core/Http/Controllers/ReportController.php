<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Attendance\Models\Attendance;
use Modules\Core\Models\Employee;
use Modules\Leave\Models\LeaveRequest;
use Modules\Payroll\Models\PayrollRun;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (! $user || (! $user->isAdmin() && ! $user->isHr() && ! $user->isAccounts())) {
                abort(403, 'Unauthorized.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        return view('core::reports.index');
    }

    public function attendance(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isHr()) {
            abort(403, 'Unauthorized.');
        }
        $query = Attendance::with('employee');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->orderBy('date', 'desc')->orderBy('employee_id')->paginate(50)->withQueryString();
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();

        return view('core::reports.attendance', compact('records', 'employees'));
    }

    public function leave(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isHr()) {
            abort(403, 'Unauthorized.');
        }
        $query = LeaveRequest::with(['employee', 'leaveType']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $records = $query->latest()->paginate(50)->withQueryString();
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();

        return view('core::reports.leave', compact('records', 'employees'));
    }

    public function payroll(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isAccounts()) {
            abort(403, 'Unauthorized.');
        }
        $query = PayrollRun::withCount('payslips');

        if ($request->filled('year')) {
            $query->whereYear('period_end', $request->year);
        }
        if ($request->filled('month')) {
            $query->whereMonth('period_end', $request->month);
        }

        $runs = $query->orderBy('period_end', 'desc')->paginate(20)->withQueryString();

        $summary = null;
        if ($request->filled('run_id')) {
            $run = PayrollRun::with('payslips.employee')->find($request->run_id);
            if ($run) {
                $summary = [
                    'run' => $run,
                    'total_net' => $run->payslips->sum('net_pay'),
                    'total_basic' => $run->payslips->sum('basic_salary'),
                    'total_allowances' => $run->payslips->sum('allowances'),
                    'total_deductions' => $run->payslips->sum('deductions'),
                    'total_wps_salary' => $run->payslips->sum(fn ($p) => $p->total_wps_salary ?? $p->net_pay),
                ];
            }
        }

        return view('core::reports.payroll', compact('runs', 'summary'));
    }
}

<?php

namespace Modules\Core\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Attendance\Models\Attendance;
use Modules\Core\Models\Company;
use Modules\Core\Models\Department;
use Modules\Core\Models\Employee;
use Modules\Core\Models\Branch;
use Modules\Core\Models\Project;
use Modules\Core\Models\Site;
use Modules\Core\Models\Asset;
use Modules\Leave\Models\LeaveRequest;
use Modules\Payroll\Models\PayrollRun;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();

        $companyId = $request->query('company_id');
        $branchId = $request->query('branch_id');
        $siteId = $request->query('site_id');
        $departmentId = $request->query('department_id');
        $projectId = $request->query('project_id');

        // Enforce dependency: branch belongs to company; site/department belong to branch
        if ($companyId && $branchId) {
            $branch = Branch::find($branchId);
            if (!$branch || $branch->company_id != $companyId) {
                $branchId = null;
                $siteId = null;
                $departmentId = null;
            }
        }
        if ($branchId && $siteId) {
            $site = Site::find($siteId);
            if (!$site || $site->branch_id != $branchId) {
                $siteId = null;
            }
        }
        if ($branchId && $departmentId) {
            $dept = Department::find($departmentId);
            if (!$dept || $dept->branch_id != $branchId) {
                $departmentId = null;
            }
        }

        $employeeQuery = Employee::where('status', 'active');
        if ($companyId) {
            $employeeQuery->whereHas('branch', fn ($q) => $q->where('company_id', $companyId));
        }
        if ($branchId) {
            $employeeQuery->where('branch_id', $branchId);
        }
        if ($siteId) {
            $employeeQuery->where('site_id', $siteId);
        }
        if ($departmentId) {
            $employeeQuery->where('department_id', $departmentId);
        }
        if ($projectId) {
            $employeeQuery->whereHas('projects', fn ($q) => $q->where('projects.id', $projectId));
        }

        $employeeIds = $employeeQuery->pluck('id');

        $stats = [
            'employees' => $employeeIds->count(),
            'attendance_today' => Attendance::whereDate('date', $today)->whereIn('employee_id', $employeeIds)->count(),
            'pending_leave' => LeaveRequest::where('status', 'pending')->whereIn('employee_id', $employeeIds)->count(),
            'payroll_this_month' => PayrollRun::whereYear('period_end', $today->year)
                ->whereMonth('period_end', $today->month)->count(),
        ];

        $recentActivity = $user->activityLogs()->exists()
            ? $user->activityLogs()->latest()->take(5)->get()
            : collect();

        $companies = Company::orderBy('name')->get();
        $branches = Branch::when($companyId, fn ($q) => $q->where('company_id', $companyId))->orderBy('name')->get();
        $sites = Site::when($branchId, fn ($q) => $q->where('branch_id', $branchId))->orderBy('name')->get();
        $departments = Department::when($branchId, fn ($q) => $q->where('branch_id', $branchId))->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();

        $todayAttendance = null;
        if ($user->employee_id) {
            $todayAttendance = Attendance::where('employee_id', $user->employee_id)->whereDate('date', $today)->first();
        }

        $assetsExpiring = collect();
        if ($user->isAdmin() || $user->isHr()) {
            $assetsExpiring = Asset::whereNotNull('expiry_date')
                ->where(function ($q) {
                    $q->where('expiry_date', '<=', now()->addDays(30))->orWhere('expiry_date', '<', now());
                })
                ->orderBy('expiry_date')
                ->take(10)
                ->get();
        }

        return view('core::dashboard', [
            'user' => $user,
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'companies' => $companies,
            'branches' => $branches,
            'sites' => $sites,
            'departments' => $departments,
            'projects' => $projects,
            'filter' => [
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'site_id' => $siteId,
                'department_id' => $departmentId,
                'project_id' => $projectId,
            ],
            'todayAttendance' => $todayAttendance,
            'assetsExpiring' => $assetsExpiring,
        ]);
    }
}

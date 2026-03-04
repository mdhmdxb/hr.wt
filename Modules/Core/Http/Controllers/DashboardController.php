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
use Modules\Core\Models\Setting;
use Modules\Core\Models\Site;
use Modules\Core\Models\Asset;
use Modules\Core\Models\EmployeeDocument;
use Modules\Core\Models\PublicHoliday;
use Modules\Core\Helpers\HijriHelper;
use Modules\Leave\Models\LeaveRequest;
use Modules\Payroll\Models\PayrollRun;
use Modules\Settings\Services\SettingsService;

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

        // For Manager role, default dashboard numbers should reflect only their own team
        // (employees who report directly to them), not the whole company.
        if ($user->isManager() && $user->employee_id && ! $user->isAdmin() && ! $user->isHr()) {
            $teamIds = Employee::where('reporting_manager_id', $user->employee_id)->where('status', 'active')->pluck('id');
            if ($teamIds->isNotEmpty()) {
                $employeeIds = $teamIds;
            }
        }

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
        $myEmployee = null;
        $isBirthday = false;
        if ($user->employee_id) {
            $todayAttendance = Attendance::where('employee_id', $user->employee_id)->whereDate('date', $today)->first();
            $myEmployee = Employee::find($user->employee_id);
            if ($myEmployee && $myEmployee->date_of_birth) {
                $dob = $myEmployee->date_of_birth;
                $isBirthday = ($dob->month === $today->month) && ($dob->day === $today->day);
            }
        }

        $assetsExpiringSoon = collect();
        $assetsExpired = collect();
        $documentsExpiringSoon = collect();
        $documentsExpired = collect();
        $upcomingLeaves = collect();
        $upcomingBirthdays = collect();

        $today = Carbon::today();

        // Assets & documents expiry are only for Admin/HR
        if ($user->isAdmin() || $user->isHr()) {
            $assetsExpiringSoon = Asset::whereNotNull('expiry_date')
                ->where('expiry_date', '>', $today)
                ->where('expiry_date', '<=', $today->copy()->addDays(30))
                ->orderBy('expiry_date')
                ->take(10)
                ->get();
            $assetsExpired = Asset::whereNotNull('expiry_date')
                ->where('expiry_date', '<', $today)
                ->orderBy('expiry_date')
                ->take(10)
                ->get();
            $documentsExpiringSoon = EmployeeDocument::with('employee')
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '>', $today)
                ->where('expiry_date', '<=', $today->copy()->addDays(30))
                ->orderBy('expiry_date')
                ->take(10)
                ->get();
            $documentsExpired = EmployeeDocument::with('employee')
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '<', $today)
                ->orderBy('expiry_date')
                ->take(10)
                ->get();
        }

        // Upcoming vacations & birthdays:
        //  - Admin/HR: across filtered employees
        //  - Manager / users with manage_leave privilege: only their own team (employeeIds was adjusted above)
        if ($user->isAdmin() || $user->isHr() || $user->isManager() || $user->hasPermission('manage_leave')) {
            $upcomingLeaves = LeaveRequest::with(['employee', 'leaveType'])
                ->where('status', LeaveRequest::STATUS_APPROVED)
                ->whereIn('employee_id', $employeeIds)
                ->whereDate('start_date', '>=', $today)
                ->whereDate('start_date', '<=', $today->copy()->addDays(30))
                ->orderBy('start_date')
                ->take(8)
                ->get();

            // Birthdays: today + upcoming (next 30 days)
            $upcomingBirthdays = Employee::whereNotNull('date_of_birth')
                ->whereIn('id', $employeeIds)
                ->get()
                ->map(function (Employee $e) use ($today) {
                    $dob = $e->date_of_birth;
                    $next = $dob->copy()->year($today->year);
                    if ($next->lt($today)) {
                        $next->addYear();
                    }
                    $isToday = $next->isSameDay($today);
                    return ['employee' => $e, 'next_birthday' => $next, 'is_today' => $isToday];
                })
                ->filter(function ($row) use ($today) {
                    /** @var \Carbon\Carbon $d */
                    $d = $row['next_birthday'];
                    return $d->between($today, $today->copy()->addDays(30));
                })
                ->sortBy('next_birthday')
                ->take(8);
        }

        // Company country for public holidays & calendar
        $companyCountry = SettingsService::get('company_country', '');
        $publicHolidaysThisMonth = collect();
        $upcomingPublicHolidays = collect();
        $calendarWeeks = [];
        $calendarYear = (int) $request->query('calendar_year', $today->year);
        $calendarMonth = (int) $request->query('calendar_month', $today->month);
        if ($calendarMonth < 1 || $calendarMonth > 12) {
            $calendarMonth = $today->month;
        }
        if ($calendarYear < 2000 || $calendarYear > 2100) {
            $calendarYear = $today->year;
        }
        $calendarDate = Carbon::createFromDate($calendarYear, $calendarMonth, 1);
        if ($user->isAdmin() || $user->isHr()) {
            $publicHolidaysThisMonth = PublicHoliday::forMonth($calendarYear, $calendarMonth, $companyCountry ?: null);
            $upcomingPublicHolidays = PublicHoliday::upcoming($today, 60, $companyCountry ?: null);
            $calendarWeeks = $this->buildCalendarWeeks($calendarYear, $calendarMonth, $companyCountry ?: null);
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
            'myEmployee' => $myEmployee,
            'isBirthday' => $isBirthday,
            'assetsExpiringSoon' => $assetsExpiringSoon,
            'assetsExpired' => $assetsExpired,
            'documentsExpiringSoon' => $documentsExpiringSoon,
            'documentsExpired' => $documentsExpired,
            'upcomingLeaves' => $upcomingLeaves,
            'upcomingBirthdays' => $upcomingBirthdays,
            'showIndividualCheckin' => $this->showIndividualCheckin(),
            'companyCountry' => $companyCountry,
            'publicHolidaysThisMonth' => $publicHolidaysThisMonth,
            'upcomingPublicHolidays' => $upcomingPublicHolidays,
            'calendarWeeks' => $calendarWeeks,
            'calendarMonth' => $calendarDate->format('F Y'),
            'calendarMonthNum' => $calendarMonth,
            'calendarYear' => $calendarYear,
            'calendarHijriMonth' => HijriHelper::format($calendarDate->copy()->startOfMonth()),
            'today' => $today,
            'dashboardCardOrder' => $this->dashboardCardOrder(),
        ]);
    }

    /** Ordered list of dashboard card keys to show (from settings or default). */
    protected function dashboardCardOrder(): array
    {
        $raw = Setting::getValue('dashboard_cards');
        if (is_array($raw) && ! empty($raw)) {
            return $raw;
        }
        return [
            'assets_expiring_soon', 'assets_expired', 'documents_expiring_soon', 'documents_expired',
            'upcoming_vacations', 'birthdays', 'calendar', 'public_holidays_month', 'upcoming_public_holidays', 'quick_actions',
        ];
    }

    /** Build calendar grid for a month: array of weeks, each week = array of days { date, day, hijri, is_holiday, holiday_name, is_today }. */
    protected function buildCalendarWeeks(int $year, int $month, ?string $countryCode): array
    {
        $start = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $holidays = PublicHoliday::forMonth($year, $month, $countryCode);

        $getHolidayFor = function (Carbon $d) use ($holidays) {
            foreach ($holidays as $h) {
                if ($h->coversDate($d)) {
                    return $h->name;
                }
            }
            return null;
        };

        $firstWeekday = (int) $start->format('w');
        $daysInMonth = $start->copy()->endOfMonth()->day;
        $totalCells = (int) ceil(($firstWeekday + $daysInMonth) / 7) * 7;
        $cells = [];
        $current = $start->copy()->subDays($firstWeekday);
        for ($i = 0; $i < $totalCells; $i++) {
            $inMonth = $current->month === $month;
            $cells[] = [
                'date' => $current->copy(),
                'day' => $current->day,
                'in_month' => $inMonth,
                'hijri' => HijriHelper::short($current),
                'is_holiday' => $inMonth && $getHolidayFor($current) !== null,
                'holiday_name' => $inMonth ? $getHolidayFor($current) : null,
                'is_today' => $current->isToday(),
            ];
            $current->addDay();
        }
        return array_chunk($cells, 7);
    }

    /** Whether to show individual Check-in/Check-out to employees (owner-controlled). Default false. */
    protected function showIndividualCheckin(): bool
    {
        $v = Setting::getValue('show_individual_checkin');
        return is_array($v) && isset($v[0]) && (bool) $v[0];
    }

    /** Whether AI features are toggled on in Owner portal. */
    public static function isAiEnabled(): bool
    {
        $v = Setting::getValue('ai_enabled');
        return is_array($v) && isset($v[0]) && (bool) $v[0];
    }
}

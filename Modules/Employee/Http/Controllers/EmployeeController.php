<?php

namespace Modules\Employee\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\ActivityLog;
use Modules\Core\Models\Branch;
use Modules\Core\Models\Department;
use Modules\Core\Models\Designation;
use Modules\Core\Models\Employee;
use Modules\Core\Models\Site;
use Modules\Core\Models\User;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['branch', 'site', 'department', 'designation'])->latest()->paginate(15);
        return view('employee::index', compact('employees'));
    }

    public function create()
    {
        $branches = Branch::with('company')->get();
        $sites = Site::with('branch')->orderBy('name')->get();
        $departments = Department::with('branch')->get();
        $designations = Designation::orderBy('level')->get();
        $managers = Employee::where('status', 'active')->get();
        $branchDefaults = $branches->keyBy('id')->map(fn ($b) => [
            'shift_start' => $b->default_shift_start,
            'shift_end' => $b->default_shift_end,
            'accommodation' => $b->default_accommodation ?? 0,
            'transportation' => $b->default_transportation ?? 0,
            'food_allowance' => $b->default_food_allowance ?? 0,
            'other_allowances' => $b->default_other_allowances ?? 0,
        ])->toArray();
        $siteDefaults = $sites->keyBy('id')->map(fn ($s) => [
            'shift_start' => $s->default_shift_start,
            'shift_end' => $s->default_shift_end,
            'accommodation' => $s->default_accommodation ?? 0,
            'transportation' => $s->default_transportation ?? 0,
            'food_allowance' => $s->default_food_allowance ?? 0,
            'other_allowances' => $s->default_other_allowances ?? 0,
        ])->toArray();
        return view('employee::create', compact('branches', 'sites', 'departments', 'designations', 'managers', 'branchDefaults', 'siteDefaults'));
    }

    public function store(Request $request)
    {
        $rules = [
            'employee_code' => 'required|string|max:50|unique:employees,employee_code',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => array_filter([
                'required',
                'email',
                'unique:employees,email',
                $request->boolean('create_login') ? 'unique:users,email' : null,
            ]),
            'photo' => 'nullable|image',
            'phone' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:male,female,other',
            'religion' => 'nullable|string|max:50',
            'break_minutes' => 'nullable|integer|min:0|max:480',
            'permanent_address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:150',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'branch_id' => 'required|exists:branches,id',
            'site_id' => 'nullable|exists:sites,id',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'reporting_manager_id' => 'nullable|exists:employees,id',
            'hire_date' => 'required|date',
            'date_of_birth' => 'nullable|date',
            'employment_type' => 'required|string|in:full_time,part_time,contract,intern',
            'basic_salary' => 'nullable|numeric|min:0',
            'accommodation' => 'nullable|numeric|min:0',
            'transportation' => 'nullable|numeric|min:0',
            'food_allowance' => 'nullable|numeric|min:0',
            'other_allowances' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'remaining_leave' => 'nullable|numeric|min:0',
            'alternate_sat_weeks' => 'nullable|array',
            'alternate_sat_weeks.*' => 'integer|in:1,2,3,4,5',
            'shift_start' => 'nullable|date_format:H:i',
            'shift_end' => 'nullable|date_format:H:i',
            'create_login' => 'nullable|boolean',
        ];
        if ($request->boolean('create_login')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }
        $maxSetting = \Modules\Settings\Models\Setting::getValue('upload_max_employee_photo_kb');
        $maxKb = (is_array($maxSetting) && isset($maxSetting[0]) && (int) $maxSetting[0] > 0 ? (int) $maxSetting[0] : 2048);
        $rules['photo'] = 'nullable|image|max:' . $maxKb;
        $valid = $request->validate($rules);
        $valid['alternate_saturday_weeks'] = $request->filled('alternate_sat_weeks') && is_array($request->alternate_sat_weeks)
            ? implode(',', array_map('intval', array_intersect($request->alternate_sat_weeks, [1, 2, 3, 4, 5]))) ?: null
            : null;
        $valid['weekly_off_days'] = collect(Employee::weekdayKeys())->filter(fn ($day) => $request->boolean('weekly_off_' . $day))->implode(',') ?: null;
        unset($valid['create_login'], $valid['password'], $valid['password_confirmation']);
        if (array_key_exists('remaining_leave', $valid) && ($valid['remaining_leave'] === '' || $valid['remaining_leave'] === null)) {
            $valid['remaining_leave'] = null;
        }
        $emp = Employee::create($valid);
        if ($request->hasFile('photo')) {
            $maxKb = \Modules\Core\Models\Setting::getValue('upload_max_employee_photo_kb');
            // Validation already checked 'image', just ensure size if configured
            $path = $request->file('photo')->store('employee-photos', 'public');
            $emp->update(['photo_path' => $path]);
        }
        if ($request->boolean('create_login') && $request->filled('password')) {
            User::create([
                'employee_id' => $emp->id,
                'name' => $emp->full_name,
                'email' => $emp->email,
                'password' => $request->password,
                'role' => User::ROLE_EMPLOYEE,
            ]);
            ActivityLog::log('user_created', 'Created portal login for employee: ' . $emp->full_name);
        }
        ActivityLog::log('employee_created', 'Created employee: ' . $emp->full_name . ' (ID ' . $emp->id . ')');
        return redirect()->route('employee.index')->with('success', 'Employee created.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['branch', 'site', 'department', 'designation', 'reportingManager', 'salaryRevisions', 'user']);
        $documents = \Modules\Core\Models\EmployeeDocument::where('employee_id', $employee->id)->latest()->get();
        $assetAssignments = \Modules\Core\Models\AssetAssignment::where('employee_id', $employee->id)->whereNull('returned_at')->with('asset.assetType')->get();
        return view('employee::show', compact('employee', 'documents', 'assetAssignments'));
    }

    public function edit(Employee $employee)
    {
        $branches = Branch::with('company')->get();
        $sites = Site::with('branch')->orderBy('name')->get();
        $departments = Department::with('branch')->get();
        $designations = Designation::orderBy('level')->get();
        $managers = Employee::where('status', 'active')->where('id', '!=', $employee->id)->get();
        $portalUser = User::where('employee_id', $employee->id)->first();
        $branchDefaults = $branches->keyBy('id')->map(fn ($b) => [
            'shift_start' => $b->default_shift_start,
            'shift_end' => $b->default_shift_end,
            'accommodation' => $b->default_accommodation ?? 0,
            'transportation' => $b->default_transportation ?? 0,
            'food_allowance' => $b->default_food_allowance ?? 0,
            'other_allowances' => $b->default_other_allowances ?? 0,
        ])->toArray();
        $siteDefaults = $sites->keyBy('id')->map(fn ($s) => [
            'shift_start' => $s->default_shift_start,
            'shift_end' => $s->default_shift_end,
            'accommodation' => $s->default_accommodation ?? 0,
            'transportation' => $s->default_transportation ?? 0,
            'food_allowance' => $s->default_food_allowance ?? 0,
            'other_allowances' => $s->default_other_allowances ?? 0,
        ])->toArray();
        return view('employee::edit', compact('employee', 'branches', 'sites', 'departments', 'designations', 'managers', 'portalUser', 'branchDefaults', 'siteDefaults'));
    }

    public function update(Request $request, Employee $employee)
    {
        $maxSetting = \Modules\Settings\Models\Setting::getValue('upload_max_employee_photo_kb');
        $maxKb = (is_array($maxSetting) && isset($maxSetting[0]) && (int) $maxSetting[0] > 0 ? (int) $maxSetting[0] : 2048);
        $valid = $request->validate([
            'employee_code' => 'required|string|max:50|unique:employees,employee_code,' . $employee->id,
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:male,female,other',
            'religion' => 'nullable|string|max:50',
            'break_minutes' => 'nullable|integer|min:0|max:480',
            'permanent_address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:150',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'branch_id' => 'required|exists:branches,id',
            'site_id' => 'nullable|exists:sites,id',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'reporting_manager_id' => 'nullable|exists:employees,id',
            'hire_date' => 'required|date',
            'date_of_birth' => 'nullable|date',
            'employment_type' => 'required|string|in:full_time,part_time,contract,intern',
            'basic_salary' => 'nullable|numeric|min:0',
            'accommodation' => 'nullable|numeric|min:0',
            'transportation' => 'nullable|numeric|min:0',
            'food_allowance' => 'nullable|numeric|min:0',
            'other_allowances' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'remaining_leave' => 'nullable|numeric|min:0',
            'alternate_sat_weeks' => 'nullable|array',
            'alternate_sat_weeks.*' => 'integer|in:1,2,3,4,5',
            'shift_start' => 'nullable|date_format:H:i',
            'shift_end' => 'nullable|date_format:H:i',
            'create_login' => 'nullable|boolean',
            'photo' => 'nullable|image|max:' . $maxKb,
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        $valid['remaining_leave'] = $request->filled('remaining_leave') ? (float) $request->remaining_leave : null;
        $portalUser = User::where('employee_id', $employee->id)->first();
        if ($request->boolean('create_login') && ! $portalUser) {
            $request->validate(['password' => 'required|string|min:8|confirmed']);
        }
        $valid['alternate_saturday_weeks'] = $request->filled('alternate_sat_weeks') && is_array($request->alternate_sat_weeks)
            ? implode(',', array_map('intval', array_intersect($request->alternate_sat_weeks, [1, 2, 3, 4, 5]))) ?: null
            : null;
        $valid['weekly_off_days'] = collect(Employee::weekdayKeys())->filter(fn ($day) => $request->boolean('weekly_off_' . $day))->implode(',') ?: null;
        unset($valid['create_login'], $valid['password'], $valid['password_confirmation']);
        $salaryFields = ['basic_salary', 'accommodation', 'transportation', 'food_allowance', 'other_allowances'];
        $salaryChanged = false;
        foreach ($salaryFields as $f) {
            $newVal = isset($valid[$f]) ? (float) $valid[$f] : null;
            $oldVal = $employee->$f !== null ? (float) $employee->$f : null;
            if ($newVal != $oldVal) {
                $salaryChanged = true;
                break;
            }
        }
        $employee->update($valid);
        if ($request->hasFile('photo')) {
            if ($employee->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($employee->photo_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($employee->photo_path);
            }
            $path = $request->file('photo')->store('employee-photos', 'public');
            $employee->update(['photo_path' => $path]);
        }
        if ($request->boolean('create_login') && ! $portalUser && $request->filled('password')) {
            User::create([
                'employee_id' => $employee->id,
                'name' => $employee->full_name,
                'email' => $employee->email,
                'password' => $request->password,
                'role' => User::ROLE_EMPLOYEE,
            ]);
            ActivityLog::log('user_created', 'Created portal login for employee: ' . $employee->full_name);
        } elseif ($portalUser) {
            $portalUser->update([
                'name' => $employee->full_name,
                'email' => $employee->email,
            ]);
            if ($request->filled('password')) {
                $portalUser->update(['password' => $request->password]);
            }
        }
        if ($salaryChanged) {
            \Modules\Core\Models\EmployeeSalaryRevision::create([
                'employee_id' => $employee->id,
                'effective_from' => now()->toDateString(),
                'basic_salary' => $employee->basic_salary,
                'accommodation' => $employee->accommodation,
                'transportation' => $employee->transportation,
                'food_allowance' => $employee->food_allowance,
                'other_allowances' => $employee->other_allowances,
                'notes' => 'Updated from employee edit',
                'changed_by' => auth()->id(),
            ]);
        }
        ActivityLog::log('employee_updated', 'Updated employee: ' . $employee->full_name . ' (ID ' . $employee->id . ')');
        return redirect()->route('employee.index')->with('success', 'Employee updated.');
    }

    public function destroy(Employee $employee)
    {
        $name = $employee->full_name;
        $id = $employee->id;
        $employee->delete();
        ActivityLog::log('employee_deleted', 'Deleted employee: ' . $name . ' (ID ' . $id . ')');
        return redirect()->route('employee.index')->with('success', 'Employee deleted.');
    }
}

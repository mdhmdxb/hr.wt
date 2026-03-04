<?php

namespace Modules\Leave\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\Employee;
use Modules\Leave\Models\LeaveHistory;
use Modules\Leave\Models\LeaveType;

class LeaveHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveHistory::with(['employee', 'leaveType'])->latest('start_date');
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        $history = $query->paginate(20)->withQueryString();
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        return view('leave::history.index', compact('history', 'employees'));
    }

    public function create(Request $request)
    {
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        $leaveTypes = LeaveType::orderBy('name')->get();
        $preselectedEmployeeId = $request->get('employee_id');
        return view('leave::history.create', compact('employees', 'leaveTypes', 'preselectedEmployeeId'));
    }

    public function store(Request $request)
    {
        $valid = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);
        LeaveHistory::create($valid);
        return redirect()->route('leave.history.index', ['employee_id' => $valid['employee_id']])
            ->with('success', 'Leave history entry added.');
    }

    public function destroy(LeaveHistory $leaveHistory)
    {
        $employeeId = $leaveHistory->employee_id;
        $leaveHistory->delete();
        return redirect()->route('leave.history.index', ['employee_id' => $employeeId])
            ->with('success', 'Leave history entry removed.');
    }
}

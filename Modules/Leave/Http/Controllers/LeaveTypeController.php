<?php

namespace Modules\Leave\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Leave\Models\LeaveType;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::withCount('leaveRequests')->orderBy('name')->paginate(15);
        return view('leave::types.index', compact('leaveTypes'));
    }

    public function create()
    {
        return view('leave::types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'days_per_year' => 'required|integer|min:0',
            'carry_over' => 'boolean',
            'color' => 'nullable|string|max:20',
            'is_paid' => 'boolean',
            'workflow_steps' => 'nullable|array',
            'workflow_steps.*' => 'string|in:reporting_manager,hr,accounts,admin',
        ]);

        $workflow = $this->normalizeWorkflowSteps($request->workflow_steps ?? []);

        LeaveType::create([
            'name' => $request->name,
            'days_per_year' => $request->days_per_year,
            'carry_over' => $request->boolean('carry_over'),
            'color' => $request->color ?: null,
            'is_paid' => $request->boolean('is_paid', true),
            'workflow_steps' => $workflow,
        ]);

        return redirect()->route('leave.types.index')->with('success', 'Leave type created.');
    }

    public function edit(LeaveType $type)
    {
        return view('leave::types.edit', compact('type'));
    }

    public function update(Request $request, LeaveType $type)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'days_per_year' => 'required|integer|min:0',
            'carry_over' => 'boolean',
            'color' => 'nullable|string|max:20',
            'is_paid' => 'boolean',
            'workflow_steps' => 'nullable|array',
            'workflow_steps.*' => 'string|in:reporting_manager,hr,accounts,admin',
        ]);

        $workflow = $this->normalizeWorkflowSteps($request->workflow_steps ?? []);

        $type->update([
            'name' => $request->name,
            'days_per_year' => $request->days_per_year,
            'carry_over' => $request->boolean('carry_over'),
            'color' => $request->color ?: null,
            'is_paid' => $request->boolean('is_paid', true),
            'workflow_steps' => $workflow,
        ]);

        return redirect()->route('leave.types.index')->with('success', 'Leave type updated.');
    }

    public function destroy(LeaveType $type)
    {
        if ($type->leaveRequests()->exists()) {
            return back()->with('error', 'Cannot delete leave type that has requests. Reassign or delete requests first.');
        }
        $type->delete();
        return redirect()->route('leave.types.index')->with('success', 'Leave type deleted.');
    }

    private function normalizeWorkflowSteps(array $steps): ?array
    {
        $steps = array_values(array_filter($steps));
        if (empty($steps)) {
            return null;
        }
        $out = [];
        foreach ($steps as $i => $approver) {
            $out[] = ['order' => $i + 1, 'approver' => $approver];
        }
        return $out;
    }
}

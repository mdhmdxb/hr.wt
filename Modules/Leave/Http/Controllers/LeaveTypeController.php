<?php

namespace Modules\Leave\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Leave\Models\LeaveType;

class LeaveTypeController extends Controller
{
    public function __construct()
    {
        // Only Admin / Management (treated as admin) and HR may manage leave types.
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (! $user || (! $user->isAdmin() && ! $user->isHr())) {
                abort(403, 'Unauthorized.');
            }
            return $next($request);
        });
    }

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
            'allow_document' => 'nullable|boolean',
            'require_document' => 'nullable|boolean',
            'document_label' => 'nullable|string|max:255',
            'workflow_steps' => 'nullable|array',
            'workflow_steps.*' => 'string|in:reporting_manager,hr,accounts,admin,owner',
        ]);

        $workflow = $this->normalizeWorkflowSteps($request->workflow_steps ?? []);

        LeaveType::create([
            'name' => $request->name,
            'days_per_year' => $request->days_per_year,
            'carry_over' => $request->boolean('carry_over'),
            'color' => $request->color ?: null,
            'is_paid' => $request->boolean('is_paid', true),
            'allow_document' => $request->boolean('allow_document', false),
            'require_document' => $request->boolean('require_document', false),
            'document_label' => $request->input('document_label') ?: null,
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
            'allow_document' => 'nullable|boolean',
            'require_document' => 'nullable|boolean',
            'document_label' => 'nullable|string|max:255',
            'workflow_steps' => 'nullable|array',
            'workflow_steps.*' => 'string|in:reporting_manager,hr,accounts,admin,owner',
        ]);

        $workflow = $this->normalizeWorkflowSteps($request->workflow_steps ?? []);

        $type->update([
            'name' => $request->name,
            'days_per_year' => $request->days_per_year,
            'carry_over' => $request->boolean('carry_over'),
            'color' => $request->color ?: null,
            'is_paid' => $request->boolean('is_paid', true),
            'allow_document' => $request->boolean('allow_document', false),
            'require_document' => $request->boolean('require_document', false),
            'document_label' => $request->input('document_label') ?: null,
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

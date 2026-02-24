<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\Branch;
use Modules\Core\Models\Employee;
use Modules\Core\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::withCount('employees')->with('branch')->orderBy('name')->paginate(20);
        return view('core::projects.index', compact('projects'));
    }

    public function create()
    {
        $branches = Branch::orderBy('name')->get();
        return view('core::projects.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50',
            'branch_id' => 'nullable|exists:branches,id',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:active,on_hold,completed',
            'description' => 'nullable|string|max:1000',
        ]);
        Project::create([
            'name' => $request->name,
            'code' => $request->code ?: null,
            'branch_id' => $request->branch_id ?: null,
            'budget' => $request->budget ?: null,
            'status' => $request->status ?: 'active',
            'description' => $request->description ?: null,
        ]);
        return redirect()->route('projects.index')->with('success', 'Project created.');
    }

    public function show(Project $project)
    {
        $project->load(['branch', 'employees']);
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        return view('core::projects.show', compact('project', 'employees'));
    }

    public function edit(Project $project)
    {
        $branches = Branch::orderBy('name')->get();
        return view('core::projects.edit', compact('project', 'branches'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50',
            'branch_id' => 'nullable|exists:branches,id',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:active,on_hold,completed',
            'description' => 'nullable|string|max:1000',
        ]);
        $project->update([
            'name' => $request->name,
            'code' => $request->code ?: null,
            'branch_id' => $request->branch_id ?: null,
            'budget' => $request->budget ?: null,
            'status' => $request->status ?: 'active',
            'description' => $request->description ?: null,
        ]);
        return redirect()->route('projects.show', $project)->with('success', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        $project->employees()->detach();
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }

    public function attachEmployee(Request $request, Project $project)
    {
        $request->validate(['employee_id' => 'required|exists:employees,id', 'role' => 'nullable|string|max:50']);
        if ($project->employees()->where('employee_id', $request->employee_id)->exists()) {
            return back()->with('error', 'Employee already assigned to this project.');
        }
        $project->employees()->attach($request->employee_id, ['role' => $request->role ?: null]);
        return back()->with('success', 'Employee assigned.');
    }

    public function detachEmployee(Project $project, Employee $employee)
    {
        $project->employees()->detach($employee->id);
        return back()->with('success', 'Employee removed from project.');
    }
}

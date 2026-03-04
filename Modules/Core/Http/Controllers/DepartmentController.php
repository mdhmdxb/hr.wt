<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\Branch;
use Modules\Core\Models\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('branch')->orderBy('name')->paginate(15);
        return view('core::departments.index', compact('departments'));
    }

    public function create()
    {
        $branches = Branch::with('company')->orderBy('name')->get();
        return view('core::departments.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
        ]);

        Department::create($request->only(['branch_id', 'name']));
        return redirect()->route('department.index')->with('success', 'Department created.');
    }

    public function edit(Department $department)
    {
        $branches = Branch::with('company')->orderBy('name')->get();
        return view('core::departments.edit', compact('department', 'branches'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
        ]);

        $department->update($request->only(['branch_id', 'name']));
        return redirect()->route('department.index')->with('success', 'Department updated.');
    }

    public function destroy(Department $department)
    {
        if ($department->employees()->exists()) {
            return back()->with('error', 'Cannot delete department with employees. Reassign them first.');
        }
        $department->delete();
        return redirect()->route('department.index')->with('success', 'Department deleted.');
    }
}

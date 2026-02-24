<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\Designation;

class DesignationController extends Controller
{
    public function index()
    {
        $designations = Designation::withCount('employees')->orderBy('level')->orderBy('name')->paginate(15);
        return view('core::designations.index', compact('designations'));
    }

    public function create()
    {
        return view('core::designations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'nullable|integer|min:0',
        ]);

        Designation::create([
            'name' => $request->name,
            'level' => $request->level ?? 0,
        ]);
        return redirect()->route('designation.index')->with('success', 'Designation created.');
    }

    public function edit(Designation $designation)
    {
        return view('core::designations.edit', compact('designation'));
    }

    public function update(Request $request, Designation $designation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'nullable|integer|min:0',
        ]);

        $designation->update([
            'name' => $request->name,
            'level' => $request->level ?? 0,
        ]);
        return redirect()->route('designation.index')->with('success', 'Designation updated.');
    }

    public function destroy(Designation $designation)
    {
        if ($designation->employees()->exists()) {
            return back()->with('error', 'Cannot delete designation assigned to employees. Reassign them first.');
        }
        $designation->delete();
        return redirect()->route('designation.index')->with('success', 'Designation deleted.');
    }
}

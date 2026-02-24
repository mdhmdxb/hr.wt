<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\Branch;
use Modules\Core\Models\Company;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::with('company')->latest()->paginate(15);
        return view('core::branches.index', compact('branches'));
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get();
        return view('core::branches.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'default_shift_start' => 'nullable|date_format:H:i',
            'default_shift_end' => 'nullable|date_format:H:i',
            'default_accommodation' => 'nullable|numeric|min:0',
            'default_transportation' => 'nullable|numeric|min:0',
            'default_food_allowance' => 'nullable|numeric|min:0',
            'default_other_allowances' => 'nullable|numeric|min:0',
        ]);

        Branch::create($request->only([
            'company_id', 'name', 'address',
            'default_shift_start', 'default_shift_end',
            'default_accommodation', 'default_transportation', 'default_food_allowance', 'default_other_allowances',
        ]));
        return redirect()->route('branch.index')->with('success', 'Branch created.');
    }

    public function edit(Branch $branch)
    {
        $companies = Company::orderBy('name')->get();
        return view('core::branches.edit', compact('branch', 'companies'));
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'default_shift_start' => 'nullable|date_format:H:i',
            'default_shift_end' => 'nullable|date_format:H:i',
            'default_accommodation' => 'nullable|numeric|min:0',
            'default_transportation' => 'nullable|numeric|min:0',
            'default_food_allowance' => 'nullable|numeric|min:0',
            'default_other_allowances' => 'nullable|numeric|min:0',
        ]);

        $branch->update($request->only([
            'company_id', 'name', 'address',
            'default_shift_start', 'default_shift_end',
            'default_accommodation', 'default_transportation', 'default_food_allowance', 'default_other_allowances',
        ]));
        return redirect()->route('branch.index')->with('success', 'Branch updated.');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->employees()->exists() || $branch->departments()->exists() || $branch->sites()->exists()) {
            return back()->with('error', 'Cannot delete branch with employees, departments, or sites. Reassign or remove them first.');
        }
        $branch->delete();
        return redirect()->route('branch.index')->with('success', 'Branch deleted.');
    }
}

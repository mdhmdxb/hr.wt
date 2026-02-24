<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\Branch;
use Modules\Core\Models\Site;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::with('branch.company')->latest()->paginate(15);
        return view('core::sites.index', compact('sites'));
    }

    public function create()
    {
        $branches = Branch::with('company')->orderBy('name')->get();
        return view('core::sites.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'default_shift_start' => 'nullable|date_format:H:i',
            'default_shift_end' => 'nullable|date_format:H:i',
            'default_accommodation' => 'nullable|numeric|min:0',
            'default_transportation' => 'nullable|numeric|min:0',
            'default_food_allowance' => 'nullable|numeric|min:0',
            'default_other_allowances' => 'nullable|numeric|min:0',
        ]);

        Site::create($request->only([
            'branch_id', 'name', 'address',
            'default_shift_start', 'default_shift_end',
            'default_accommodation', 'default_transportation', 'default_food_allowance', 'default_other_allowances',
        ]));
        return redirect()->route('site.index')->with('success', 'Site created.');
    }

    public function edit(Site $site)
    {
        $branches = Branch::with('company')->orderBy('name')->get();
        return view('core::sites.edit', compact('site', 'branches'));
    }

    public function update(Request $request, Site $site)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'default_shift_start' => 'nullable|date_format:H:i',
            'default_shift_end' => 'nullable|date_format:H:i',
            'default_accommodation' => 'nullable|numeric|min:0',
            'default_transportation' => 'nullable|numeric|min:0',
            'default_food_allowance' => 'nullable|numeric|min:0',
            'default_other_allowances' => 'nullable|numeric|min:0',
        ]);

        $site->update($request->only([
            'branch_id', 'name', 'address',
            'default_shift_start', 'default_shift_end',
            'default_accommodation', 'default_transportation', 'default_food_allowance', 'default_other_allowances',
        ]));
        return redirect()->route('site.index')->with('success', 'Site updated.');
    }

    public function destroy(Site $site)
    {
        if ($site->employees()->exists()) {
            return back()->with('error', 'Cannot delete site with assigned employees. Reassign them first.');
        }
        $site->delete();
        return redirect()->route('site.index')->with('success', 'Site deleted.');
    }
}

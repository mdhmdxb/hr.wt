<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Models\Company;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount('branches')->latest()->paginate(15);
        return view('core::companies.index', compact('companies'));
    }

    public function create()
    {
        return view('core::companies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'address', 'phone', 'email']);
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('companies', 'public');
        }
        Company::create($data);
        return redirect()->route('company.index')->with('success', 'Company created.');
    }

    public function edit(Company $company)
    {
        return view('core::companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'address', 'phone', 'email']);
        if ($request->hasFile('logo')) {
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $data['logo'] = $request->file('logo')->store('companies', 'public');
        }
        $company->update($data);
        return redirect()->route('company.index')->with('success', 'Company updated.');
    }

    public function destroy(Company $company)
    {
        if ($company->branches()->exists()) {
            return back()->with('error', 'Cannot delete company with branches. Delete or reassign branches first.');
        }
        if ($company->logo && Storage::disk('public')->exists($company->logo)) {
            Storage::disk('public')->delete($company->logo);
        }
        $company->delete();
        return redirect()->route('company.index')->with('success', 'Company deleted.');
    }
}

<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\Company;
use Modules\Core\Models\Setting;

class OwnerController extends Controller
{
    public function index()
    {
        $companies = Company::orderBy('name')->get();
        $moduleKeys = Setting::moduleKeys();
        $globalModules = Setting::getValue('modules', null);
        if (! is_array($globalModules)) {
            $globalModules = array_keys($moduleKeys);
        }

        return view('core::owner.index', [
            'companies' => $companies,
            'moduleKeys' => $moduleKeys,
            'globalModules' => $globalModules,
        ]);
    }

    public function updateModules(Request $request)
    {
        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'modules' => 'array',
            'modules.*' => 'string|in:' . implode(',', array_keys(Setting::moduleKeys())),
        ]);

        $companyId = $request->filled('company_id') ? $request->company_id : null;
        $modules = $request->input('modules', []);

        Setting::setValue('modules', array_values($modules), $companyId);

        $target = $companyId ? 'company' : 'global';
        return redirect()->route('owner.index')->with('success', "Modules updated for {$target}.");
    }
}

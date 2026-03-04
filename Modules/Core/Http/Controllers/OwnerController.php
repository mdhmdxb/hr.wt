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
        $showIndividualCheckin = Setting::getValue('show_individual_checkin');
        $showIndividualCheckin = is_array($showIndividualCheckin) && isset($showIndividualCheckin[0]) && (bool) $showIndividualCheckin[0];

        return view('core::owner.index', [
            'companies' => $companies,
            'moduleKeys' => $moduleKeys,
            'globalModules' => $globalModules,
            'showIndividualCheckin' => $showIndividualCheckin,
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

    public function updateOptions(Request $request)
    {
        $request->validate([
            'show_individual_checkin' => 'nullable|in:0,1',
            'ai_enabled' => 'nullable|in:0,1',
            'ai_provider' => 'nullable|string|max:50',
            'ai_model' => 'nullable|string|max:100',
        ]);
        $show = (bool) $request->input('show_individual_checkin', 0);
        Setting::setValue('show_individual_checkin', $show ? [1] : [0], null);
        $aiEnabled = (bool) $request->input('ai_enabled', 0);
        Setting::setValue('ai_enabled', $aiEnabled ? [1] : [0], null);
        Setting::setValue('ai_provider', [$request->input('ai_provider') ?: ''], null);
        Setting::setValue('ai_model', [$request->input('ai_model') ?: ''], null);
        return redirect()->route('owner.index')->with('success', 'Options saved.');
    }

}

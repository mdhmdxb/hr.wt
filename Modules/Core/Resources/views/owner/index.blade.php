@extends('core::layouts.app')

@section('title', 'Owner Portal')
@section('heading', 'Owner Portal')

@section('content')
<div class="max-w-4xl space-y-8">
    <p class="text-slate-600 dark:text-slate-400">Control which companies exist and which modules are available. This area is restricted to the owner role only.</p>

    @if(session('success'))
    <div class="p-4 rounded-xl bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">{{ session('success') }}</div>
    @endif

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Companies</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Manage companies from Organization → Companies. Here you configure which modules are enabled.</p>
        <ul class="space-y-2">
            @forelse($companies as $c)
            <li class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700 last:border-0">
                <span class="font-medium text-slate-900 dark:text-slate-100">{{ $c->name }}</span>
                <a href="{{ route('company.index') }}" class="text-sm wise-link">Edit in Organization</a>
            </li>
            @empty
            <li class="text-slate-500 dark:text-slate-400">No companies yet. Add one from Organization → Companies.</li>
            @endforelse
        </ul>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Enable modules (global)</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">These settings apply app-wide. Uncheck a module to hide it from the sidebar and restrict access for all users. Grouped by area.</p>
        <form method="POST" action="{{ route('owner.modules.update') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="company_id" value="">
            @foreach(\Modules\Core\Models\Setting::moduleGroups() as $groupLabel => $groupKeys)
            <div>
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ $groupLabel }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pl-2 border-l-2 border-slate-200 dark:border-slate-600">
                    @foreach($groupKeys as $key)
                    @php $label = $moduleKeys[$key] ?? $key; @endphp
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer">
                        <input type="checkbox" name="modules[]" value="{{ $key }}" {{ in_array($key, $globalModules, true) ? 'checked' : '' }}>
                        <span class="text-slate-800 dark:text-slate-200">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
            <div class="pt-2">
                <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Save module settings</button>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Per-company modules (optional)</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Select a company to override which modules are enabled for that company only. If not set, global settings above apply.</p>
        <form method="POST" action="{{ route('owner.modules.update') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Company</label>
                <select name="company_id" class="w-full max-w-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" required>
                    <option value="">Select company</option>
                    @foreach($companies as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            @foreach(\Modules\Core\Models\Setting::moduleGroups() as $groupLabel => $groupKeys)
            <div class="mb-3">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ $groupLabel }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pl-2 border-l-2 border-slate-200 dark:border-slate-600">
                    @foreach($groupKeys as $key)
                    @php $label = $moduleKeys[$key] ?? $key; @endphp
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer">
                        <input type="checkbox" name="modules[]" value="{{ $key }}">
                        <span class="text-slate-800 dark:text-slate-200">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
            <div class="pt-2">
                <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Save for this company</button>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-6">
        <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Feature options</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Control visibility of features and prepare for AI-powered helpers.</p>
        <form method="POST" action="{{ route('owner.options.update') }}" class="space-y-5 max-w-2xl">
            @csrf
            <input type="hidden" name="show_individual_checkin" value="0">
            <input type="hidden" name="ai_enabled" value="0">
            <div>
                <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer max-w-md">
                    <input type="checkbox" name="show_individual_checkin" value="1" {{ ($showIndividualCheckin ?? false) ? 'checked' : '' }}>
                    <span class="text-slate-800 dark:text-slate-200">Show individual Check-in / Check-out to employees</span>
                </label>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">When unchecked, employees do not see Check-in/Check-out buttons on the dashboard. They use My Attendance (batch) only.</p>
            </div>

            <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 mb-2">AI integration (prepare)</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                    Toggle this on when you are ready to connect to an external AI provider (OpenAI, Azure OpenAI, etc.).
                    The app does not call any AI yet; this switch and the settings below are to prepare configuration.
                </p>
                @php
                    $aiEnabledVal = \Modules\Core\Models\Setting::getValue('ai_enabled');
                    $aiEnabled = is_array($aiEnabledVal) && isset($aiEnabledVal[0]) && (bool) $aiEnabledVal[0];
                    $aiProviderVal = \Modules\Core\Models\Setting::getValue('ai_provider');
                    $aiProvider = is_array($aiProviderVal) && isset($aiProviderVal[0]) ? $aiProviderVal[0] : '';
                    $aiModelVal = \Modules\Core\Models\Setting::getValue('ai_model');
                    $aiModel = is_array($aiModelVal) && isset($aiModelVal[0]) ? $aiModelVal[0] : '';
                @endphp
                <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer max-w-md mb-3">
                    <input type="checkbox" name="ai_enabled" value="1" {{ $aiEnabled ? 'checked' : '' }}>
                    <span class="text-slate-800 dark:text-slate-200">Enable AI helpers (beta / future)</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">AI provider key</label>
                        <input type="text" name="ai_provider" value="{{ $aiProvider }}" placeholder="e.g. openai, azure, local-api"
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Default model</label>
                        <input type="text" name="ai_model" value="{{ $aiModel }}" placeholder="e.g. gpt-4.1-mini"
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                    </div>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Free / low-cost options include OpenAI’s small models or any self-hosted open‑source model you expose via HTTP.
                    When you are ready, a developer can plug these credentials into background jobs (summary emails, smart suggestions, etc.).
                </p>
            </div>

            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Save options</button>
        </form>
    </div>

    <div class="text-sm text-slate-500 dark:text-slate-400">
        <strong>Note:</strong> When a module is disabled, its menu item is hidden and direct URL access is restricted. Only the owner can change these settings.
    </div>
</div>
@endsection

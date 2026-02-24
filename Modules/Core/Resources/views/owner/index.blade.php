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
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">These settings apply app-wide. Uncheck a module to hide it from the sidebar and restrict access for all users.</p>
        <form method="POST" action="{{ route('owner.modules.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="company_id" value="">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($moduleKeys as $key => $label)
                <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer">
                    <input type="checkbox" name="modules[]" value="{{ $key }}" {{ in_array($key, $globalModules, true) ? 'checked' : '' }}>
                    <span class="text-slate-800 dark:text-slate-200">{{ $label }}</span>
                </label>
                @endforeach
            </div>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($moduleKeys as $key => $label)
                <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer">
                    <input type="checkbox" name="modules[]" value="{{ $key }}">
                    <span class="text-slate-800 dark:text-slate-200">{{ $label }}</span>
                </label>
                @endforeach
            </div>
            <div class="pt-2">
                <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Save for this company</button>
            </div>
        </form>
    </div>

    <div class="text-sm text-slate-500 dark:text-slate-400">
        <strong>Note:</strong> When a module is disabled, its menu item is hidden and direct URL access is restricted. Only the owner can change these settings.
    </div>
</div>
@endsection

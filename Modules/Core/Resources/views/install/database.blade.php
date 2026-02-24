@extends('core::layouts.install')

@section('title', 'Database - Wise HRM Installation')

@section('content')
<div class="max-w-md mx-auto">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-4">Database Configuration</h2>
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg">{{ session('error') }}</div>
    @endif
    <form method="POST" action="{{ route('install.database.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Host</label>
            <input type="text" name="db_host" value="127.0.0.1" required
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Port</label>
            <input type="text" name="db_port" value="3306" required
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Database</label>
            <input type="text" name="db_database" value="wise_hrm" required
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white px-3 py-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">The database will be created automatically if it doesn't exist (like WordPress).</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Username</label>
            <input type="text" name="db_username" required
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password</label>
            <input type="password" name="db_password"
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white px-3 py-2">
        </div>
        <div class="flex gap-3 pt-2">
            <a href="{{ route('install.welcome') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Back</a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Next</button>
        </div>
    </form>
</div>
@endsection

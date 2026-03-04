@extends('core::layouts.app')

@section('title', 'Change password')
@section('heading', 'Change password')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <p class="text-sm text-slate-600 dark:text-slate-400">
            Update your login password. For security, you must enter your current password first.
        </p>
        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Current password</label>
                <input type="password" name="current_password" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @error('current_password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">New password</label>
                <input type="password" name="password" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @error('password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Confirm new password</label>
                <input type="password" name="password_confirmation" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
            <div class="flex justify-end gap-2">
                <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg text-sm font-medium">Save password</button>
            </div>
        </form>
    </div>
</div>
@endsection


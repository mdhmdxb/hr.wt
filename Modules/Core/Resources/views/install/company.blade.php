@extends('core::layouts.install')

@section('title', 'Company - Wise HRM Installation')

@section('content')
<div class="max-w-md mx-auto">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-4">Company Setup</h2>
    <form method="POST" action="{{ route('install.company.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Company Name</label>
            <input type="text" name="company_name" value="{{ old('company_name') }}" required
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white px-3 py-2">
            @error('company_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Address</label>
            <textarea name="address" rows="2" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white px-3 py-2">{{ old('address') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white px-3 py-2">
        </div>
        <div class="flex gap-3 pt-2">
            <a href="{{ route('install.admin') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Back</a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Next</button>
        </div>
    </form>
</div>
@endsection

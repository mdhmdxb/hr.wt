@extends('core::layouts.install')

@section('title', 'Finalize - Wise HRM Installation')

@section('content')
<div class="max-w-md mx-auto text-center">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-4">Ready to Install</h2>
    <p class="text-slate-600 dark:text-slate-400 mb-6">Click below to run migrations, create the admin user, and lock the installer.</p>
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg">{{ session('error') }}</div>
    @endif
    <form method="POST" action="{{ route('install.complete') }}">
        @csrf
        <div class="flex gap-3 justify-center">
            <a href="{{ route('install.company') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Back</a>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Complete Installation</button>
        </div>
    </form>
</div>
@endsection

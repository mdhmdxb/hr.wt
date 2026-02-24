@extends('core::layouts.install')

@section('title', 'Welcome - Wise HRM Installation')

@section('content')
<div class="max-w-lg mx-auto text-center">
    <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mb-2">Wise HRM</h1>
    <p class="text-slate-600 dark:text-slate-400 mb-8">Modular HR Management System – Installation Wizard</p>
    <a href="{{ route('install.database') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
        Start Installation
    </a>
</div>
@endsection

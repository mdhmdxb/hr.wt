@extends('core::layouts.app')

@section('title', 'Preview: ' . $template->name)
@section('heading', 'Preview: ' . $template->name)

@section('content')
<div class="mb-4 flex flex-wrap gap-2">
    <a href="{{ route('templates.index') }}" class="wise-link hover:underline">← Templates</a>
    <a href="{{ route('templates.edit', $template) }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-sm">Edit</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-4xl">
    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Sample data used for placeholders. Edit template to change content.</p>
    <div class="prose dark:prose-invert max-w-none border border-slate-200 dark:border-slate-600 rounded-lg p-6 bg-slate-50 dark:bg-slate-900/50 min-h-48">
        {!! $html ?: '<p class="text-slate-500">No content. Add placeholders like @{{employee_name}} in the template.</p>' !!}
    </div>
</div>
@endsection

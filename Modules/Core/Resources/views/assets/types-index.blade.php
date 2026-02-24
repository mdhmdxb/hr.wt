@extends('core::layouts.app')

@section('title', 'Asset types')
@section('heading', 'Asset types')

@section('content')
<div class="mb-4">
    <a href="{{ route('assets.types.create') }}" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add type</a>
    <a href="{{ route('assets.index') }}" class="ml-2 px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-sm">Back to Assets</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Slug</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Assets</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($types as $type)
            <tr>
                <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">{{ $type->name }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $type->slug }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $type->assets_count }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('assets.types.edit', $type) }}" class="wise-link hover:underline text-sm">Edit</a>
                    <form method="POST" action="{{ route('assets.types.destroy', $type) }}" class="inline ml-2" onsubmit="return confirm('Delete this type?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="wise-link text-red-600 dark:text-red-400 text-sm">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No asset types yet. Create one (e.g. Mobile, Sim, Laptop).</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($types->hasPages())
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">{{ $types->links() }}</div>
    @endif
</div>
@endsection

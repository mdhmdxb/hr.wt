@extends('core::layouts.app')

@section('title', $opening->title)
@section('heading', $opening->title)

@section('content')
<div class="mb-4 flex flex-wrap gap-2">
    <a href="{{ route('recruitment.index') }}" class="wise-link hover:underline">← Recruitment</a>
    <a href="{{ route('recruitment.openings.edit', $opening) }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-sm">Edit</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl mb-6">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Department</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $opening->department->name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Status</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ ucfirst($opening->status) }}</dd></div>
    </dl>
    @if($opening->description)
    <div class="mt-4"><dt class="text-sm text-slate-500 dark:text-slate-400">Description</dt><dd class="mt-1 text-slate-700 dark:text-slate-300">{{ $opening->description }}</dd></div>
    @endif
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-4xl">
    <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-4">Candidates (pipeline)</h3>
    <form method="POST" action="{{ route('recruitment.candidates.store', $opening) }}" class="flex flex-wrap gap-2 mb-6 p-3 bg-slate-50 dark:bg-slate-700/30 rounded-lg">
        @csrf
        <input type="text" name="name" required placeholder="Name" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm min-w-32">
        <input type="email" name="email" required placeholder="Email" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm min-w-40">
        <input type="text" name="phone" placeholder="Phone" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm min-w-32">
        <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg text-sm">Add candidate</button>
    </form>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Contact</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Stage</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Interview</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($opening->candidates as $c)
                <tr>
                    <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">{{ $c->name }}</td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $c->email }} @if($c->phone)<br><span class="text-xs">{{ $c->phone }}</span>@endif</td>
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('recruitment.candidates.stage', $c) }}" class="inline">
                            @csrf
                            @method('PUT')
                            <select name="stage" onchange="this.form.submit()" class="text-xs rounded border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1">
                                @foreach(\Modules\Core\Models\JobCandidate::stageOptions() as $val => $label)
                                    <option value="{{ $val }}" {{ $c->stage === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400 text-sm">{{ $c->interview_at?->format('Y-m-d H:i') ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No candidates yet. Add one above.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

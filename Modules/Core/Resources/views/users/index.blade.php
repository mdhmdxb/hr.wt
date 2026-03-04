@extends('core::layouts.app')

@section('title', 'Users & roles')
@section('heading', 'Users & roles')

@section('content')
<div class="w-full space-y-6">
    @if(session('success'))
    <div class="p-4 rounded-xl bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">{{ session('success') }}</div>
    @endif

    <section class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Manage users</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
            This page lists users you can assign a <strong>role</strong> and <strong>privileges</strong> to. Link each user to an <strong>employee</strong> so their department and designation come from that record. Owner and Admin accounts are not shown here.
        </p>

        @if($users->isEmpty())
        <p class="text-slate-500 dark:text-slate-400 py-4">No manageable users. All current users are Owner or Admin.</p>
        @else
        <form method="POST" action="{{ route('users.update') }}" class="space-y-4">
            @csrf
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">
                    <thead class="bg-slate-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="py-3 px-3 text-left text-slate-600 dark:text-slate-300 font-medium">User</th>
                            <th class="py-3 px-3 text-left text-slate-600 dark:text-slate-300 font-medium">Linked employee</th>
                            <th class="py-3 px-3 text-left text-slate-600 dark:text-slate-300 font-medium">Department</th>
                            <th class="py-3 px-3 text-left text-slate-600 dark:text-slate-300 font-medium">Designation</th>
                            <th class="py-3 px-3 text-left text-slate-600 dark:text-slate-300 font-medium">Role</th>
                            <th class="py-3 px-3 text-left text-slate-600 dark:text-slate-300 font-medium">Privileges</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($users as $u)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30">
                            <td class="py-3 px-3">
                                <div class="font-medium text-slate-900 dark:text-slate-100">{{ $u->name }}</div>
                                <div class="text-slate-500 dark:text-slate-400 text-xs">{{ $u->email }}</div>
                            </td>
                            <td class="py-3 px-3">
                                <select name="users[{{ $u->id }}][employee_id]" class="w-full max-w-[200px] rounded border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                                    <option value="">— None —</option>
                                    @foreach($employees as $e)
                                    <option value="{{ $e->id }}" {{ (int) $u->employee_id === (int) $e->id ? 'selected' : '' }}>
                                        {{ $e->full_name ?? ($e->first_name . ' ' . $e->last_name) }}
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="py-3 px-3 text-slate-600 dark:text-slate-300">
                                {{ $u->employee && $u->employee->department ? $u->employee->department->name : '—' }}
                            </td>
                            <td class="py-3 px-3 text-slate-600 dark:text-slate-300">
                                {{ $u->employee && $u->employee->designation ? $u->employee->designation->name : '—' }}
                            </td>
                            <td class="py-3 px-3">
                                <select name="users[{{ $u->id }}][role]" class="rounded border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                                    @foreach($roles as $key => $label)
                                    <option value="{{ $key }}" {{ $u->role === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="py-3 px-3">
                                @php $userPerms = $u->permissions ?? []; @endphp
                                <div class="flex flex-wrap gap-x-4 gap-y-1">
                                    @foreach($permissions as $permKey => $permLabel)
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer text-slate-700 dark:text-slate-300">
                                        <input type="checkbox" name="users[{{ $u->id }}][permissions][]" value="{{ $permKey }}" {{ in_array($permKey, $userPerms, true) ? 'checked' : '' }} class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-xs">{{ $permLabel }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pt-2">
                <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Save changes</button>
            </div>
        </form>
        @endif
    </section>

    <section class="bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">How it works</h3>
        <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-1 list-disc list-inside">
            <li><strong>Linked employee</strong> — One user can be linked to one employee. Department and designation are read from that employee.</li>
            <li><strong>Role</strong> — Management, HR, Manager, Accounts, or Employee. Role controls broad access (e.g. leave approval, payroll).</li>
            <li><strong>Privileges</strong> — Optional extra permissions you can allocate per user (e.g. manage leave, manage templates).</li>
            <li>Owner and Admin accounts are not listed; they have full access and are managed separately.</li>
        </ul>
    </section>
</div>
@endsection

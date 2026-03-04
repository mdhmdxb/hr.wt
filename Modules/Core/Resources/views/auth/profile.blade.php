@extends('core::layouts.app')

@section('title', 'My profile')
@section('heading', 'My profile')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-4xl">
    <section class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <h2 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100">Personal details</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div>
                <dt class="text-slate-500 dark:text-slate-400">Name</dt>
                <dd class="font-medium text-slate-900 dark:text-slate-100">{{ $user->name }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 dark:text-slate-400">Email</dt>
                <dd class="font-medium text-slate-900 dark:text-slate-100">{{ $user->email }}</dd>
            </div>
            @if($employee)
            <div>
                <dt class="text-slate-500 dark:text-slate-400">Employee code</dt>
                <dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->employee_code }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 dark:text-slate-400">Designation</dt>
                <dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->designation->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 dark:text-slate-400">Branch</dt>
                <dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->branch->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 dark:text-slate-400">Department</dt>
                <dd class="font-medium text-slate-900 dark:text-slate-100">{{ $employee->department->name ?? '—' }}</dd>
            </div>
            @endif
        </dl>
        @if($employee)
        <form method="POST" action="{{ route('profile.update') }}" class="mt-4 space-y-3">
            @csrf
            <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100">Contact & emergency</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                    @error('phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Emergency contact person</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Emergency contact phone</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Permanent address</label>
                <textarea name="permanent_address" rows="2" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">{{ old('permanent_address', $employee->permanent_address) }}</textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg text-sm font-medium">Save profile</button>
            </div>
        </form>
        @endif
    </section>

    <section class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <h2 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100">My signature</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Upload your signature image once. It can be used on approvals and letters.</p>
        @php $sigPath = $user->signature_path; @endphp
        @if($sigPath)
            <p class="text-xs text-slate-600 dark:text-slate-300 mb-2">Current signature:</p>
            <img src="{{ asset('storage/' . ltrim($sigPath, '/')) }}" alt="Your signature" class="max-h-20 border border-slate-200 dark:border-slate-600 rounded-lg mb-3">
        @endif
        <form method="POST" action="{{ route('profile.signature.update') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <input type="file" name="signature" accept="image/png,image/jpeg,image/jpg" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
            @error('signature')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg text-sm font-medium">
                {{ $sigPath ? 'Replace signature' : 'Upload signature' }}
            </button>
        </form>

        <div class="border-t border-slate-200 dark:border-slate-700 pt-4 mt-4">
            <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-2">Security</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">You can also change your login password from here.</p>
            <a href="{{ route('password.edit') }}" class="inline-flex items-center px-3 py-1.5 border border-slate-300 dark:border-slate-600 rounded-lg text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                Change password
            </a>
        </div>
    </section>
</div>
@endsection


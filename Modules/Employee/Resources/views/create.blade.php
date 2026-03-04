@extends('core::layouts.app')

@section('title', 'Add Employee')
@section('heading', 'Add Employee')

@section('content')
<form method="POST" action="{{ route('employee.store') }}" class="max-w-2xl space-y-4" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2 flex items-center gap-4 mb-2">
            <div class="w-14 h-14 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-300 text-sm">
                Photo
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Employee photo</label>
                <input type="file" name="photo" accept="image/png,image/jpeg" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Optional. Used on profile screens and reports.</p>
                @error('photo')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Employee Code *</label>
            <input type="text" name="employee_code" value="{{ old('employee_code') }}" required
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            @error('employee_code')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="md:col-span-2 grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">First Name *</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @error('first_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Last Name *</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @error('last_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email *</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nationality</label>
            <input type="text" name="nationality" value="{{ old('nationality') }}" placeholder="e.g. Indian, Filipino" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Gender</label>
            <select name="gender" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <option value="">— Select —</option>
                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Religion</label>
            <select name="religion" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @foreach(\Modules\Core\Models\Employee::religionOptions() as $val => $label)
                    <option value="{{ $val }}" {{ old('religion') === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Break (minutes)</label>
            <input type="number" name="break_minutes" value="{{ old('break_minutes') }}" min="0" max="480" placeholder="e.g. 0 or 30" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Daily break; e.g. 0 for Muslims (Ramadan), 30 for others.</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Branch *</label>
            <select name="branch_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Site</label>
            <select name="site_id" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <option value="">— None —</option>
                @foreach($sites as $s)
                    <option value="{{ $s->id }}" {{ old('site_id') == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->branch->name ?? '' }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Department *</label>
            <select name="department_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }} ({{ $d->branch->name }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Designation *</label>
            <select name="designation_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @foreach($designations as $des)
                    <option value="{{ $des->id }}" {{ old('designation_id') == $des->id ? 'selected' : '' }}>{{ $des->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Reporting Manager</label>
            <select name="reporting_manager_id" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <option value="">— None —</option>
                @foreach($managers as $m)
                    <option value="{{ $m->id }}" {{ old('reporting_manager_id') == $m->id ? 'selected' : '' }}>{{ $m->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Hire Date *</label>
            <input type="date" name="hire_date" value="{{ old('hire_date') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            @error('hire_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Date of birth</label>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Employment Type *</label>
            <select name="employment_type" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <option value="full_time" {{ old('employment_type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                <option value="part_time" {{ old('employment_type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                <option value="intern" {{ old('employment_type') == 'intern' ? 'selected' : '' }}>Intern</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Basic Salary</label>
            <input type="number" step="0.01" name="basic_salary" value="{{ old('basic_salary', 0) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div class="md:col-span-2 flex flex-wrap items-center gap-2">
            <h3 class="wise-heading text-sm font-semibold text-slate-700 dark:text-slate-300">Salary allowances (defaults for payroll)</h3>
            <button type="button" id="use-branch-defaults" class="text-xs px-2 py-1 rounded border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">Use branch defaults</button>
            <button type="button" id="use-site-defaults" class="text-xs px-2 py-1 rounded border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">Use site defaults</button>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Accommodation</label>
            <input type="number" step="0.01" name="accommodation" value="{{ old('accommodation', 0) }}" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Transportation</label>
            <input type="number" step="0.01" name="transportation" value="{{ old('transportation', 0) }}" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Food allowance</label>
            <input type="number" step="0.01" name="food_allowance" value="{{ old('food_allowance', 0) }}" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Other allowances</label>
            <input type="number" step="0.01" name="other_allowances" value="{{ old('other_allowances', 0) }}" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status *</label>
            <select name="status" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Remaining leave (days)</label>
            <input type="number" step="0.5" min="0" name="remaining_leave" value="{{ old('remaining_leave') }}" placeholder="e.g. 30" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Current annual/leave balance. Leave empty if not applicable. Approved leave will deduct from this.</p>
        </div>
    </div>
    <div class="border-t border-slate-200 dark:border-slate-600 pt-4 mt-4">
        <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Address &amp; emergency contact</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Permanent address</label>
                <textarea name="permanent_address" rows="2" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" placeholder="Building, street, city, country">{{ old('permanent_address') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Emergency contact person</label>
                <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="Name" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Emergency contact phone</label>
                <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" placeholder="Phone number" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
        </div>
    </div>

    {{-- Portal login (so employee can sign in) --}}
    <div class="border-t border-slate-200 dark:border-slate-600 pt-4 mt-4">
        <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Portal login</h3>
        <label class="inline-flex items-center gap-2 cursor-pointer mb-3">
            <input type="checkbox" name="create_login" value="1" {{ old('create_login') ? 'checked' : '' }} class="rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700" id="create_login">
            <span class="text-sm text-slate-700 dark:text-slate-300">Create portal login so this employee can sign in to the dashboard</span>
        </label>
        <div id="login-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3" style="display: {{ old('create_login') ? 'grid' : 'none' }};">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password *</label>
                <input type="password" name="password" id="emp_password" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" minlength="8" placeholder="Min 8 characters">
                @error('password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Confirm password *</label>
                <input type="password" name="password_confirmation" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" minlength="8">
            </div>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">They will sign in with the email above and this password.</p>
    </div>
    {{-- Work schedule (site timing / routine) --}}
    <div class="border-t border-slate-200 dark:border-slate-600 pt-4 mt-4">
        <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Work schedule &amp; routine</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Weekly off days</label>
                <div class="flex flex-wrap gap-4">
                    @foreach(\Modules\Core\Models\Employee::weekdayKeys() as $day)
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="weekly_off_{{ $day }}" value="1" {{ old('weekly_off_' . $day) ? 'checked' : '' }} class="rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                            <span class="text-sm text-slate-700 dark:text-slate-300">{{ ucfirst($day) }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Select days when this employee does not work. Used to pre-fill batch attendance.</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Alternate Saturday off (which weeks of month)</label>
                <div class="flex flex-wrap gap-4">
                    @foreach(\Modules\Core\Models\Employee::alternateSaturdayWeekOptions() as $num => $label)
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="alternate_sat_weeks[]" value="{{ $num }}" {{ in_array($num, old('alternate_sat_weeks', [])) ? 'checked' : '' }} class="rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                            <span class="text-sm text-slate-700 dark:text-slate-300">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Select which Saturdays of the month are off. Different employees can have different weeks. Used for &quot;Alt. Saturday off&quot; in attendance.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Default shift start (optional)</label>
                <input type="time" name="shift_start" value="{{ old('shift_start') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Default shift end (optional)</label>
                <input type="time" name="shift_end" value="{{ old('shift_end') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
        </div>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('employee.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        <button type="submit" class="px-6 py-2 wise-btn text-white rounded-lg">Create</button>
    </div>
</form>
<script>
(function() {
    var branchDefaults = @json($branchDefaults);
    var siteDefaults = @json($siteDefaults);
    function applyDefaults(d) {
        if (!d) return;
        var f = document.querySelector('form');
        if (f.querySelector('input[name="shift_start"]')) f.querySelector('input[name="shift_start"]').value = d.shift_start || '';
        if (f.querySelector('input[name="shift_end"]')) f.querySelector('input[name="shift_end"]').value = d.shift_end || '';
        if (f.querySelector('input[name="accommodation"]')) f.querySelector('input[name="accommodation"]').value = d.accommodation != null ? d.accommodation : '';
        if (f.querySelector('input[name="transportation"]')) f.querySelector('input[name="transportation"]').value = d.transportation != null ? d.transportation : '';
        if (f.querySelector('input[name="food_allowance"]')) f.querySelector('input[name="food_allowance"]').value = d.food_allowance != null ? d.food_allowance : '';
        if (f.querySelector('input[name="other_allowances"]')) f.querySelector('input[name="other_allowances"]').value = d.other_allowances != null ? d.other_allowances : '';
    }
    document.getElementById('use-branch-defaults')?.addEventListener('click', function() {
        var id = document.querySelector('select[name="branch_id"]')?.value;
        applyDefaults(branchDefaults[id]);
    });
    document.getElementById('use-site-defaults')?.addEventListener('click', function() {
        var id = document.querySelector('select[name="site_id"]')?.value;
        if (!id) return;
        applyDefaults(siteDefaults[id]);
    });
    document.getElementById('create_login')?.addEventListener('change', function() {
        document.getElementById('login-fields').style.display = this.checked ? 'grid' : 'none';
    });
})();
</script>
@endsection

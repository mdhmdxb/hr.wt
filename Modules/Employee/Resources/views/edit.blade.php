@extends('core::layouts.app')

@section('title', 'Edit ' . $employee->full_name)
@section('heading', 'Edit Employee')

@section('content')
<form method="POST" action="{{ route('employee.update', $employee) }}" class="max-w-2xl space-y-4" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2 flex items-center gap-4 mb-2">
            <div class="w-14 h-14 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden flex items-center justify-center">
                @if($employee->photo_path)
                    <img src="{{ asset('storage/' . ltrim($employee->photo_path, '/')) }}" alt="{{ $employee->full_name }}" class="w-14 h-14 object-cover">
                @else
                    <span class="text-slate-500 dark:text-slate-300 text-sm">Photo</span>
                @endif
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Employee photo</label>
                <input type="file" name="photo" accept="image/png,image/jpeg" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Optional. Upload to replace the current photo.</p>
                @error('photo')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Employee Code *</label>
            <input type="text" name="employee_code" value="{{ old('employee_code', $employee->employee_code) }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            @error('employee_code')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="md:col-span-2 grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">First Name *</label>
                <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Last Name *</label>
                <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email *</label>
            <input type="email" name="email" value="{{ old('email', $employee->email) }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nationality</label>
            <input type="text" name="nationality" value="{{ old('nationality', $employee->nationality) }}" placeholder="e.g. Indian, Filipino" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Gender</label>
            <select name="gender" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @php $g = old('gender', $employee->gender); @endphp
                <option value="">— Select —</option>
                <option value="male" {{ $g === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ $g === 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ $g === 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Religion</label>
            <select name="religion" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @foreach(\Modules\Core\Models\Employee::religionOptions() as $val => $label)
                    <option value="{{ $val }}" {{ old('religion', $employee->religion) === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Break (minutes)</label>
            <input type="number" name="break_minutes" value="{{ old('break_minutes', $employee->break_minutes) }}" min="0" max="480" placeholder="e.g. 0 or 30" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Branch *</label>
            <select name="branch_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ old('branch_id', $employee->branch_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Site</label>
            <select name="site_id" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <option value="">— None —</option>
                @foreach($sites as $s)
                    <option value="{{ $s->id }}" {{ old('site_id', $employee->site_id) == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->branch->name ?? '' }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Department *</label>
            <select name="department_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" {{ old('department_id', $employee->department_id) == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Designation *</label>
            <select name="designation_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @foreach($designations as $des)
                    <option value="{{ $des->id }}" {{ old('designation_id', $employee->designation_id) == $des->id ? 'selected' : '' }}>{{ $des->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Reporting Manager</label>
            <select name="reporting_manager_id" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <option value="">— None —</option>
                @foreach($managers as $m)
                    <option value="{{ $m->id }}" {{ old('reporting_manager_id', $employee->reporting_manager_id) == $m->id ? 'selected' : '' }}>{{ $m->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Hire Date *</label>
            <input type="date" name="hire_date" value="{{ old('hire_date', $employee->hire_date?->format('Y-m-d')) }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Date of birth</label>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth?->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Employment Type *</label>
            <select name="employment_type" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                @php $employmentTypes = ['full_time','part_time','contract','intern']; @endphp
                @foreach($employmentTypes as $t)
                    <option value="{{ $t }}" {{ old('employment_type', $employee->employment_type) == $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Basic Salary</label>
            <input type="number" step="0.01" name="basic_salary" value="{{ old('basic_salary', $employee->basic_salary) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div class="md:col-span-2 flex flex-wrap items-center gap-2">
            <h3 class="wise-heading text-sm font-semibold text-slate-700 dark:text-slate-300">Salary allowances (defaults for payroll)</h3>
            <button type="button" id="use-branch-defaults" class="text-xs px-2 py-1 rounded border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">Use branch defaults</button>
            <button type="button" id="use-site-defaults" class="text-xs px-2 py-1 rounded border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">Use site defaults</button>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Accommodation</label>
            <input type="number" step="0.01" name="accommodation" value="{{ old('accommodation', $employee->accommodation ?? 0) }}" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Transportation</label>
            <input type="number" step="0.01" name="transportation" value="{{ old('transportation', $employee->transportation ?? 0) }}" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Food allowance</label>
            <input type="number" step="0.01" name="food_allowance" value="{{ old('food_allowance', $employee->food_allowance ?? 0) }}" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Other allowances</label>
            <input type="number" step="0.01" name="other_allowances" value="{{ old('other_allowances', $employee->other_allowances ?? 0) }}" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status *</label>
            <select name="status" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Remaining leave (days)</label>
            <input type="number" step="0.5" min="0" name="remaining_leave" value="{{ old('remaining_leave', $employee->remaining_leave !== null ? $employee->remaining_leave : '') }}" placeholder="e.g. 30" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Current annual/leave balance. Approved leave deducts from this.</p>
        </div>
    </div>
    <div class="border-t border-slate-200 dark:border-slate-600 pt-4 mt-4">
        <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Address &amp; emergency contact</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Permanent address</label>
                <textarea name="permanent_address" rows="2" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" placeholder="Building, street, city, country">{{ old('permanent_address', $employee->permanent_address) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Emergency contact person</label>
                <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}" placeholder="Name" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Emergency contact phone</label>
                <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}" placeholder="Phone number" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
        </div>
    </div>

    {{-- Portal login --}}
    <div class="border-t border-slate-200 dark:border-slate-600 pt-4 mt-4">
        <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Portal login</h3>
        @if(isset($portalUser) && $portalUser)
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">This employee can sign in ({{ $portalUser->email }}).</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Set new password (optional)</label>
                    <input type="password" name="password" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" minlength="8" placeholder="Leave blank to keep current">
                    @error('password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Confirm new password</label>
                    <input type="password" name="password_confirmation" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" minlength="8">
                </div>
            </div>
        @else
            <label class="inline-flex items-center gap-2 cursor-pointer mb-3">
                <input type="checkbox" name="create_login" value="1" {{ old('create_login') ? 'checked' : '' }} class="rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700" id="edit_create_login">
                <span class="text-sm text-slate-700 dark:text-slate-300">Create portal login so this employee can sign in</span>
            </label>
            <div id="edit-login-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3" style="display: {{ old('create_login') ? 'grid' : 'none' }};">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password *</label>
                    <input type="password" name="password" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" minlength="8">
                    @error('password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Confirm password *</label>
                    <input type="password" name="password_confirmation" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" minlength="8">
                </div>
            </div>
        @endif
    </div>
    {{-- Work schedule (site timing / routine) --}}
    @php $offList = $employee->getWeeklyOffDaysList(); @endphp
    <div class="border-t border-slate-200 dark:border-slate-600 pt-4 mt-4">
        <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Work schedule &amp; routine</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Weekly off days</label>
                <div class="flex flex-wrap gap-4">
                    @foreach(\Modules\Core\Models\Employee::weekdayKeys() as $day)
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="weekly_off_{{ $day }}" value="1" {{ (old('weekly_off_' . $day) !== null ? old('weekly_off_' . $day) : in_array($day, $offList)) ? 'checked' : '' }} class="rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                            <span class="text-sm text-slate-700 dark:text-slate-300">{{ ucfirst($day) }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Select days when this employee does not work. Used to pre-fill batch attendance.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Alternate Saturday off (which weeks of month)</label>
                <div class="flex flex-wrap gap-4">
                    @php $altSatWeeks = old('alternate_sat_weeks', $employee->getAlternateSaturdayWeeksList()); @endphp
                    @foreach(\Modules\Core\Models\Employee::alternateSaturdayWeekOptions() as $num => $label)
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="alternate_sat_weeks[]" value="{{ $num }}" {{ in_array($num, $altSatWeeks) ? 'checked' : '' }} class="rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                            <span class="text-sm text-slate-700 dark:text-slate-300">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Select which Saturdays of the month are off. Used for &quot;Alt. Saturday off&quot; in attendance.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Default shift start (optional)</label>
                <input type="time" name="shift_start" value="{{ old('shift_start', $employee->shift_start) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Default shift end (optional)</label>
                <input type="time" name="shift_end" value="{{ old('shift_end', $employee->shift_end) }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
        </div>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('employee.show', $employee) }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        <button type="submit" class="px-6 py-2 wise-btn text-white rounded-lg">Update</button>
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
    document.getElementById('edit_create_login')?.addEventListener('change', function() {
        var el = document.getElementById('edit-login-fields');
        if (el) el.style.display = this.checked ? 'grid' : 'none';
    });
})();
</script>
@endsection



<?php $__env->startSection('title', 'Edit ' . $employee->full_name); ?>
<?php $__env->startSection('heading', 'Edit Employee'); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('employee.update', $employee)); ?>" class="max-w-2xl space-y-4">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Employee Code *</label>
            <input type="text" name="employee_code" value="<?php echo e(old('employee_code', $employee->employee_code)); ?>" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            <?php $__errorArgs = ['employee_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="md:col-span-2 grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">First Name *</label>
                <input type="text" name="first_name" value="<?php echo e(old('first_name', $employee->first_name)); ?>" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Last Name *</label>
                <input type="text" name="last_name" value="<?php echo e(old('last_name', $employee->last_name)); ?>" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email *</label>
            <input type="email" name="email" value="<?php echo e(old('email', $employee->email)); ?>" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Phone</label>
            <input type="text" name="phone" value="<?php echo e(old('phone', $employee->phone)); ?>" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Branch *</label>
            <select name="branch_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($b->id); ?>" <?php echo e(old('branch_id', $employee->branch_id) == $b->id ? 'selected' : ''); ?>><?php echo e($b->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Site</label>
            <select name="site_id" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <option value="">— None —</option>
                <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s->id); ?>" <?php echo e(old('site_id', $employee->site_id) == $s->id ? 'selected' : ''); ?>><?php echo e($s->name); ?> (<?php echo e($s->branch->name ?? ''); ?>)</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Department *</label>
            <select name="department_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($d->id); ?>" <?php echo e(old('department_id', $employee->department_id) == $d->id ? 'selected' : ''); ?>><?php echo e($d->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Designation *</label>
            <select name="designation_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <?php $__currentLoopData = $designations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $des): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($des->id); ?>" <?php echo e(old('designation_id', $employee->designation_id) == $des->id ? 'selected' : ''); ?>><?php echo e($des->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Reporting Manager</label>
            <select name="reporting_manager_id" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <option value="">— None —</option>
                <?php $__currentLoopData = $managers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m->id); ?>" <?php echo e(old('reporting_manager_id', $employee->reporting_manager_id) == $m->id ? 'selected' : ''); ?>><?php echo e($m->full_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Hire Date *</label>
            <input type="date" name="hire_date" value="<?php echo e(old('hire_date', $employee->hire_date?->format('Y-m-d'))); ?>" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Employment Type *</label>
            <select name="employment_type" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <?php $employmentTypes = ['full_time','part_time','contract','intern']; ?>
                <?php $__currentLoopData = $employmentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($t); ?>" <?php echo e(old('employment_type', $employee->employment_type) == $t ? 'selected' : ''); ?>><?php echo e(ucfirst(str_replace('_',' ',$t))); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Basic Salary</label>
            <input type="number" step="0.01" name="basic_salary" value="<?php echo e(old('basic_salary', $employee->basic_salary)); ?>" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div class="md:col-span-2 flex flex-wrap items-center gap-2">
            <h3 class="wise-heading text-sm font-semibold text-slate-700 dark:text-slate-300">Salary allowances (defaults for payroll)</h3>
            <button type="button" id="use-branch-defaults" class="text-xs px-2 py-1 rounded border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">Use branch defaults</button>
            <button type="button" id="use-site-defaults" class="text-xs px-2 py-1 rounded border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">Use site defaults</button>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Accommodation</label>
            <input type="number" step="0.01" name="accommodation" value="<?php echo e(old('accommodation', $employee->accommodation ?? 0)); ?>" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Transportation</label>
            <input type="number" step="0.01" name="transportation" value="<?php echo e(old('transportation', $employee->transportation ?? 0)); ?>" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Food allowance</label>
            <input type="number" step="0.01" name="food_allowance" value="<?php echo e(old('food_allowance', $employee->food_allowance ?? 0)); ?>" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Other allowances</label>
            <input type="number" step="0.01" name="other_allowances" value="<?php echo e(old('other_allowances', $employee->other_allowances ?? 0)); ?>" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status *</label>
            <select name="status" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <option value="active" <?php echo e(old('status', $employee->status) == 'active' ? 'selected' : ''); ?>>Active</option>
                <option value="inactive" <?php echo e(old('status', $employee->status) == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
            </select>
        </div>
    </div>
    
    <div class="border-t border-slate-200 dark:border-slate-600 pt-4 mt-4">
        <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Portal login</h3>
        <?php if(isset($portalUser) && $portalUser): ?>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">This employee can sign in (<?php echo e($portalUser->email); ?>).</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Set new password (optional)</label>
                    <input type="password" name="password" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" minlength="8" placeholder="Leave blank to keep current">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Confirm new password</label>
                    <input type="password" name="password_confirmation" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" minlength="8">
                </div>
            </div>
        <?php else: ?>
            <label class="inline-flex items-center gap-2 cursor-pointer mb-3">
                <input type="checkbox" name="create_login" value="1" <?php echo e(old('create_login') ? 'checked' : ''); ?> class="rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700" id="edit_create_login">
                <span class="text-sm text-slate-700 dark:text-slate-300">Create portal login so this employee can sign in</span>
            </label>
            <div id="edit-login-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3" style="display: <?php echo e(old('create_login') ? 'grid' : 'none'); ?>;">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password *</label>
                    <input type="password" name="password" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" minlength="8">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Confirm password *</label>
                    <input type="password" name="password_confirmation" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" minlength="8">
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php $offList = $employee->getWeeklyOffDaysList(); ?>
    <div class="border-t border-slate-200 dark:border-slate-600 pt-4 mt-4">
        <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Work schedule &amp; routine</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Weekly off days</label>
                <div class="flex flex-wrap gap-4">
                    <?php $__currentLoopData = \Modules\Core\Models\Employee::weekdayKeys(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="weekly_off_<?php echo e($day); ?>" value="1" <?php echo e((old('weekly_off_' . $day) !== null ? old('weekly_off_' . $day) : in_array($day, $offList)) ? 'checked' : ''); ?> class="rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                            <span class="text-sm text-slate-700 dark:text-slate-300"><?php echo e(ucfirst($day)); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Select days when this employee does not work. Used to pre-fill batch attendance.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Alternate Saturday off (which weeks of month)</label>
                <div class="flex flex-wrap gap-4">
                    <?php $altSatWeeks = old('alternate_sat_weeks', $employee->getAlternateSaturdayWeeksList()); ?>
                    <?php $__currentLoopData = \Modules\Core\Models\Employee::alternateSaturdayWeekOptions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="alternate_sat_weeks[]" value="<?php echo e($num); ?>" <?php echo e(in_array($num, $altSatWeeks) ? 'checked' : ''); ?> class="rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                            <span class="text-sm text-slate-700 dark:text-slate-300"><?php echo e($label); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Select which Saturdays of the month are off. Used for &quot;Alt. Saturday off&quot; in attendance.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Default shift start (optional)</label>
                <input type="time" name="shift_start" value="<?php echo e(old('shift_start', $employee->shift_start)); ?>" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Default shift end (optional)</label>
                <input type="time" name="shift_end" value="<?php echo e(old('shift_end', $employee->shift_end)); ?>" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
        </div>
    </div>
    <div class="flex gap-3">
        <a href="<?php echo e(route('employee.show', $employee)); ?>" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        <button type="submit" class="px-6 py-2 wise-btn text-white rounded-lg">Update</button>
    </div>
</form>
<script>
(function() {
    var branchDefaults = <?php echo json_encode($branchDefaults, 15, 512) ?>;
    var siteDefaults = <?php echo json_encode($siteDefaults, 15, 512) ?>;
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Employee\Providers/../Resources/views/edit.blade.php ENDPATH**/ ?>
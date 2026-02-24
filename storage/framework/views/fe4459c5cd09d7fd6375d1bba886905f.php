

<?php $__env->startSection('title', 'Edit Attendance'); ?>
<?php $__env->startSection('heading', 'Edit Attendance'); ?>

<?php $__env->startSection('content'); ?>
<?php if($attendance->isLocked() && (auth()->user()->isAdmin() || auth()->user()->isHr())): ?>
<div class="mb-4 p-3 bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 rounded-lg flex items-center justify-between">
    <span class="text-sm">This record is locked. Unlock to allow editing (it will lock again on save).</span>
    <form method="POST" action="<?php echo e(route('attendance.unlock', $attendance)); ?>" class="inline">
        <?php echo csrf_field(); ?>
        <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-200 dark:bg-amber-800 text-amber-900 dark:text-amber-100 text-sm font-medium">Unlock</button>
    </form>
</div>
<?php endif; ?>
<form method="POST" action="<?php echo e(route('attendance.update', $attendance)); ?>" enctype="multipart/form-data" class="max-w-xl space-y-4">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Employee</label>
            <select name="employee_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($e->id); ?>" <?php echo e(old('employee_id', $attendance->employee_id) == $e->id ? 'selected' : ''); ?>><?php echo e($e->full_name); ?> (<?php echo e($e->employee_code); ?>)</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Date</label>
            <input type="date" name="date" value="<?php echo e(old('date', $attendance->date->format('Y-m-d'))); ?>" max="<?php echo e(date('Y-m-d')); ?>" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" title="Future dates are not allowed">
            <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Check In (time)</label>
                <input type="time" name="check_in_at" value="<?php echo e(old('check_in_at', $attendance->check_in_at ? \Carbon\Carbon::parse($attendance->check_in_at)->format('H:i') : '')); ?>" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Check Out (time)</label>
                <input type="time" name="check_out_at" value="<?php echo e(old('check_out_at', $attendance->check_out_at ? \Carbon\Carbon::parse($attendance->check_out_at)->format('H:i') : '')); ?>" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status</label>
            <select name="status" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <?php $__currentLoopData = \Modules\Attendance\Models\Attendance::statusOptions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php echo e(old('status', $attendance->status) === $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Overtime (minutes)</label>
            <input type="number" name="overtime_minutes" value="<?php echo e(old('overtime_minutes', $attendance->overtime_minutes)); ?>" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Attachment</label>
            <?php if($attendance->attachment_path): ?>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-1"><a href="<?php echo e(route('attendance.attachment.download', $attendance)); ?>" class="wise-link">Download current file</a></p>
            <?php endif; ?>
            <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Leave empty to keep current. Max 5 MB.</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Notes</label>
            <textarea name="notes" rows="2" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2"><?php echo e(old('notes', $attendance->notes)); ?></textarea>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Update</button>
            <a href="<?php echo e(route('attendance.index')); ?>" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Attendance\Providers/../Resources/views/edit.blade.php ENDPATH**/ ?>
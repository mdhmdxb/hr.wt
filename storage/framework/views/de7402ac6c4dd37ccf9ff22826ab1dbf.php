

<?php $__env->startSection('title', 'New Payroll Run'); ?>
<?php $__env->startSection('heading', 'New Payroll Run'); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('payroll.store')); ?>" class="max-w-xl space-y-4">
    <?php echo csrf_field(); ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <p class="text-sm text-slate-600 dark:text-slate-400">Creates a payroll run for the period and generates one payslip per active employee (using their basic salary). You can edit payslips after creation.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Period start</label>
                <input type="date" name="period_start" value="<?php echo e(old('period_start')); ?>" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <?php $__errorArgs = ['period_start'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Period end</label>
                <input type="date" name="period_end" value="<?php echo e(old('period_end')); ?>" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <?php $__errorArgs = ['period_end'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Create & Generate Payslips</button>
            <a href="<?php echo e(route('payroll.index')); ?>" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Payroll\Providers/../Resources/views/create.blade.php ENDPATH**/ ?>
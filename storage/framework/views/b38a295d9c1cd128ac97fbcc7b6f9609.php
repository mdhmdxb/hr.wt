

<?php $__env->startSection('title', 'Edit Leave Type'); ?>
<?php $__env->startSection('heading', 'Edit Leave Type'); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('leave.types.update', $type)); ?>" class="max-w-xl space-y-4">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Name</label>
            <input type="text" name="name" value="<?php echo e(old('name', $type->name)); ?>" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Days per year</label>
            <input type="number" name="days_per_year" value="<?php echo e(old('days_per_year', $type->days_per_year)); ?>" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="carry_over" value="1" id="carry_over" <?php echo e(old('carry_over', $type->carry_over) ? 'checked' : ''); ?> class="rounded border-slate-300">
            <label for="carry_over" class="text-sm text-slate-700 dark:text-slate-300">Allow carry over</label>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Color</label>
            <input type="text" name="color" value="<?php echo e(old('color', $type->color)); ?>" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_paid" value="1" id="is_paid" <?php echo e(old('is_paid', $type->is_paid) ? 'checked' : ''); ?> class="rounded border-slate-300">
            <label for="is_paid" class="text-sm text-slate-700 dark:text-slate-300">Paid leave</label>
        </div>
        <div class="mt-2 space-y-2">
            <label class="flex items-start gap-2 cursor-pointer">
                <input type="checkbox" name="allow_document" value="1" <?php echo e(old('allow_document', $type->allow_document) ? 'checked' : ''); ?> class="mt-1 rounded border-slate-300">
                <span class="text-sm text-slate-700 dark:text-slate-300">
                    Allow supporting document upload (e.g. medical note, maternity proof)
                </span>
            </label>
            <label class="flex items-start gap-2 cursor-pointer pl-6">
                <input type="checkbox" name="require_document" value="1" <?php echo e(old('require_document', $type->require_document) ? 'checked' : ''); ?> class="mt-1 rounded border-slate-300">
                <span class="text-sm text-slate-700 dark:text-slate-300">
                    Document is required for this leave type
                    <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">Example: Sick leave must have a medical certificate.</span>
                </span>
            </label>
            <div class="pl-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Document label (optional)</label>
                <input type="text" name="document_label" value="<?php echo e(old('document_label', $type->document_label)); ?>" placeholder="e.g. Medical certificate, Hospital letter"
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
            </div>
        </div>
        <?php
            $rawWf = old('workflow_steps', $type->workflow_steps);
            $wfSteps = is_array($rawWf) && isset($rawWf[0]) && is_array($rawWf[0]) ? array_column($rawWf, 'approver') : (is_array($rawWf) ? $rawWf : []);
            ?>
        <div class="border-t border-slate-200 dark:border-slate-600 pt-4">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Approval workflow (order matters)</label>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Leave empty for single HR approval.</p>
            <?php $__currentLoopData = [1,2,3,4]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-sm text-slate-500 w-16">Step <?php echo e($i); ?></span>
                <select name="workflow_steps[]" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm flex-1">
                    <option value="">— None —</option>
                    <?php $__currentLoopData = \Modules\Leave\Models\LeaveApprovalStep::approverTypeOptions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php echo e(($wfSteps[$i-1] ?? '') == $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Update</button>
            <a href="<?php echo e(route('leave.types.index')); ?>" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Leave\Providers/../Resources/views/types/edit.blade.php ENDPATH**/ ?>
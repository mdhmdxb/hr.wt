

<?php $__env->startSection('title', 'Leave Types'); ?>
<?php $__env->startSection('heading', 'Leave Types'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4 flex justify-between items-center">
    <a href="<?php echo e(route('leave.index')); ?>" class="wise-link hover:underline">← Back to Leave</a>
    <a href="<?php echo e(route('leave.types.create')); ?>" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add Leave Type</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Days/Year</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Carry Over</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Paid</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Workflow</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Requests</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            <?php $__empty_1 = true; $__currentLoopData = $leaveTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100"><?php echo e($lt->name); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($lt->days_per_year); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($lt->carry_over ? 'Yes' : 'No'); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($lt->is_paid ? 'Yes' : 'No'); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400 text-xs">
                    <?php
                        $steps = $lt->getWorkflowStepsNormalized();
                        $labels = \Modules\Leave\Models\LeaveApprovalStep::approverTypeOptions();
                    ?>
                    <?php if(empty($steps)): ?>
                        Single HR approval
                    <?php else: ?>
                        <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($labels[$step['approver'] ?? 'hr'] ?? ($step['approver'] ?? 'HR')); ?><?php if($idx < count($steps)-1): ?> → <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($lt->leave_requests_count); ?></td>
                <td class="px-4 py-3 text-right">
                    <a href="<?php echo e(route('leave.types.edit', $lt)); ?>" class="wise-link hover:underline">Edit</a>
                    <form method="POST" action="<?php echo e(route('leave.types.destroy', $lt)); ?>" class="inline ml-2" onsubmit="return confirm('Delete this leave type?');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No leave types. Add one to allow leave requests.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if($leaveTypes->hasPages()): ?>
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
        <?php echo e($leaveTypes->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Leave\Providers/../Resources/views/types/index.blade.php ENDPATH**/ ?>
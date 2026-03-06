

<?php $__env->startSection('title', 'Payroll'); ?>
<?php $__env->startSection('heading', 'Payroll'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4">
    <a href="<?php echo e(route('payroll.create')); ?>" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">New Payroll Run</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Period</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Payslips</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            <?php $__empty_1 = true; $__currentLoopData = $runs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $run): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100"><?php echo e($run->period_start->format('Y-m-d')); ?> – <?php echo e($run->period_end->format('Y-m-d')); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($run->payslips_count); ?></td>
                <td class="px-4 py-3">
                    <?php if($run->status === 'draft'): ?>
                        <span class="px-2 py-1 text-xs rounded bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">Draft</span>
                    <?php elseif($run->status === 'finalized'): ?>
                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">Finalized</span>
                    <?php else: ?>
                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Paid</span>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="<?php echo e(route('payroll.show', $run)); ?>" class="wise-link hover:underline">View</a>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No payroll runs. Create one to generate payslips for the period.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if($runs->hasPages()): ?>
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
        <?php echo e($runs->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Payroll\Providers/../Resources/views/index.blade.php ENDPATH**/ ?>
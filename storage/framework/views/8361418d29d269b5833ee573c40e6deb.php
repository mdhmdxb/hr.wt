

<?php $__env->startSection('title', 'Employees'); ?>
<?php $__env->startSection('heading', 'Employees'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4">
    <a href="<?php echo e(route('employee.create')); ?>" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add Employee</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Code</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Department</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100"><?php echo e($emp->employee_code); ?></td>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100"><?php echo e($emp->full_name); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($emp->department->name ?? '-'); ?></td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs rounded <?php echo e($emp->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300'); ?>"><?php echo e($emp->status); ?></span>
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="<?php echo e(route('employee.show', $emp)); ?>" class="wise-link hover:underline">View</a>
                    <a href="<?php echo e(route('employee.edit', $emp)); ?>" class="ml-2 wise-link hover:underline">Edit</a>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No employees yet.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if($employees->hasPages()): ?>
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
        <?php echo e($employees->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Employee\Providers/../Resources/views/index.blade.php ENDPATH**/ ?>


<?php $__env->startSection('title', 'Designations'); ?>
<?php $__env->startSection('heading', 'Designations'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4">
    <a href="<?php echo e(route('designation.create')); ?>" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add Designation</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Level</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Employees</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            <?php $__empty_1 = true; $__currentLoopData = $designations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $des): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100"><?php echo e($des->name); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($des->level); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($des->employees_count); ?></td>
                <td class="px-4 py-3 text-right">
                    <a href="<?php echo e(route('designation.edit', $des)); ?>" class="wise-link hover:underline">Edit</a>
                    <form method="POST" action="<?php echo e(route('designation.destroy', $des)); ?>" class="inline ml-2" onsubmit="return confirm('Delete this designation?');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No designations yet. Add job titles (e.g. IT Manager, HR Officer).</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if($designations->hasPages()): ?>
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
        <?php echo e($designations->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Core\Providers/../Resources/views/designations/index.blade.php ENDPATH**/ ?>


<?php $__env->startSection('title', 'Projects'); ?>
<?php $__env->startSection('heading', 'Projects'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4">
    <a href="<?php echo e(route('projects.create')); ?>" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add project</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Code</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Branch</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Employees</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100"><?php echo e($p->name); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($p->code ?? '—'); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($p->branch->name ?? '—'); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($p->employees_count); ?></td>
                <td class="px-4 py-3 text-right">
                    <a href="<?php echo e(route('projects.show', $p)); ?>" class="wise-link hover:underline text-sm">View</a>
                    <a href="<?php echo e(route('projects.edit', $p)); ?>" class="wise-link hover:underline text-sm ml-2">Edit</a>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No projects yet. Create one to filter the dashboard by project.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if($projects->hasPages()): ?>
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700"><?php echo e($projects->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Core\Providers/../Resources/views/projects/index.blade.php ENDPATH**/ ?>


<?php $__env->startSection('title', 'Recruitment'); ?>
<?php $__env->startSection('heading', 'Recruitment'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4">
    <a href="<?php echo e(route('recruitment.openings.create')); ?>" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add job opening</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Title</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Department</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Candidates</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            <?php $__empty_1 = true; $__currentLoopData = $openings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100"><?php echo e($o->title); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($o->department->name ?? '—'); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($o->candidates_count); ?></td>
                <td class="px-4 py-3"><span class="text-xs px-2 py-0.5 rounded <?php echo e($o->status === 'open' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400'); ?>"><?php echo e(ucfirst($o->status)); ?></span></td>
                <td class="px-4 py-3 text-right">
                    <a href="<?php echo e(route('recruitment.show', $o)); ?>" class="wise-link hover:underline text-sm">View</a>
                    <a href="<?php echo e(route('recruitment.openings.edit', $o)); ?>" class="wise-link hover:underline text-sm ml-2">Edit</a>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No job openings yet. Create one to start receiving candidates.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if($openings->hasPages()): ?>
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700"><?php echo e($openings->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Core\Providers/../Resources/views/recruitment/index.blade.php ENDPATH**/ ?>
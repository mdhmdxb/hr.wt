

<?php $__env->startSection('title', 'Document templates'); ?>
<?php $__env->startSection('heading', 'Document templates'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4">
    <a href="<?php echo e(route('templates.create')); ?>" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add template</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Slug</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100"><?php echo e($t->name); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($t->slug); ?></td>
                <td class="px-4 py-3 text-right">
                    <a href="<?php echo e(route('templates.preview', $t)); ?>" class="wise-link hover:underline text-sm">Preview</a>
                    <a href="<?php echo e(route('templates.edit', $t)); ?>" class="wise-link hover:underline text-sm ml-2">Edit</a>
                    <form method="POST" action="<?php echo e(route('templates.destroy', $t)); ?>" class="inline ml-2" onsubmit="return confirm('Delete this template?');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="text-red-600 dark:text-red-400 text-sm">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="3" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No templates yet. Create one with placeholders like {{employee_name}}, {{date}}.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if($templates->hasPages()): ?>
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700"><?php echo e($templates->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Core\Providers/../Resources/views/templates/index.blade.php ENDPATH**/ ?>


<?php $__env->startSection('title', 'Document vault'); ?>
<?php $__env->startSection('heading', 'Document vault'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4 flex flex-wrap items-center gap-4">
    <a href="<?php echo e(route('documents.create')); ?>" class="inline-flex items-center px-4 py-2 wise-btn text-white rounded-lg">Add document</a>
    <?php $expiring = request('expiring'); $q = request()->except('expiring'); ?>
    <a href="<?php echo e(route('documents.index', $expiring ? $q : array_merge($q, ['expiring' => 1]))); ?>" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm <?php echo e($expiring ? 'wise-btn text-white' : ''); ?>"><?php echo e($expiring ? 'Showing expiring soon (click to clear)' : 'Show expiring in 30 days'); ?></a>
    <form method="GET" action="<?php echo e(route('documents.index')); ?>" class="flex flex-wrap gap-2 items-end">
        <?php if(request('expiring')): ?><input type="hidden" name="expiring" value="1"><?php endif; ?>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-0.5">Employee</label>
            <select name="employee_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                <option value="">All</option>
                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($e->id); ?>" <?php echo e(request('employee_id') == $e->id ? 'selected' : ''); ?>><?php echo e($e->full_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-0.5">Type</label>
            <select name="type" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                <option value="">All</option>
                <?php $__currentLoopData = \Modules\Core\Models\EmployeeDocument::typeOptions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php echo e(request('type') === $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm">Filter</button>
    </form>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Employee</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Type</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Title</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Issue</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Expiry</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="<?php echo e($doc->isExpired() ? 'bg-red-50/30 dark:bg-red-900/10' : ($doc->isExpiringSoon() ? 'bg-amber-50/30 dark:bg-amber-900/10' : '')); ?>">
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100"><?php echo e($doc->employee->full_name ?? '—'); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e(\Modules\Core\Models\EmployeeDocument::typeOptions()[$doc->type] ?? $doc->type); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($doc->title ?: '—'); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($doc->issue_date?->format('Y-m-d') ?? '—'); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                    <?php if($doc->expiry_date): ?>
                        <?php echo e($doc->expiry_date->format('Y-m-d')); ?>

                        <?php if($doc->isExpired()): ?>
                            <span class="text-red-600 dark:text-red-400 text-xs">Expired</span>
                        <?php elseif($doc->isExpiringSoon()): ?>
                            <span class="text-amber-600 dark:text-amber-400 text-xs">Soon</span>
                        <?php endif; ?>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="<?php echo e(route('documents.download', $doc)); ?>" class="wise-link hover:underline text-sm">Download</a>
                    <a href="<?php echo e(route('documents.show', $doc)); ?>" class="wise-link hover:underline text-sm ml-2">View</a>
                    <form method="POST" action="<?php echo e(route('documents.destroy', $doc)); ?>" class="inline ml-2" onsubmit="return confirm('Delete this document?');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="wise-link hover:underline text-sm text-red-600 dark:text-red-400">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No documents yet.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if($documents->hasPages()): ?>
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700"><?php echo e($documents->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Core\Providers/../Resources/views/documents/index.blade.php ENDPATH**/ ?>


<?php $__env->startSection('title', 'My Leave'); ?>
<?php $__env->startSection('heading', 'My leave'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4 flex flex-wrap items-center gap-3">
    <a href="<?php echo e(route('dashboard')); ?>" class="wise-link hover:underline">← Back to Dashboard</a>
    <a href="<?php echo e(route('my-leave.create')); ?>" class="px-4 py-2 wise-btn text-white rounded-lg text-sm font-medium">New leave request</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
    <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Your leave requests</h2>
    <?php if($leaveRequests->isEmpty()): ?>
        <p class="text-sm text-slate-500 dark:text-slate-400">No leave requests yet. Click “New leave request” to apply.</p>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Type</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Dates</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Days</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Status</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php $__currentLoopData = $leaveRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="px-4 py-2 text-slate-800 dark:text-slate-100"><?php echo e($lr->leaveType->name ?? '—'); ?></td>
                    <td class="px-4 py-2 text-slate-600 dark:text-slate-300"><?php echo e($lr->start_date->format('Y-m-d')); ?> → <?php echo e($lr->end_date->format('Y-m-d')); ?></td>
                    <td class="px-4 py-2 text-slate-600 dark:text-slate-300"><?php echo e($lr->days); ?></td>
                    <td class="px-4 py-2">
                        <?php if($lr->status === \Modules\Leave\Models\LeaveRequest::STATUS_PENDING): ?>
                            <span class="px-2 py-0.5 text-xs rounded bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Pending</span>
                        <?php elseif($lr->status === \Modules\Leave\Models\LeaveRequest::STATUS_APPROVED): ?>
                            <span class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Approved</span>
                        <?php elseif($lr->status === \Modules\Leave\Models\LeaveRequest::STATUS_CANCELLED): ?>
                            <span class="px-2 py-0.5 text-xs rounded bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-100">Cancelled</span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 text-xs rounded bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Rejected</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-2 text-right">
                        <a href="<?php echo e(route('my-leave.show', $lr)); ?>" class="wise-link text-sm">View</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        <?php echo e($leaveRequests->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Leave\Providers/../Resources/views/my/index.blade.php ENDPATH**/ ?>


<?php $__env->startSection('title', 'Leave calendar'); ?>
<?php $__env->startSection('heading', 'Leave calendar'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4 flex flex-wrap items-center gap-4">
    <a href="<?php echo e(route('leave.index')); ?>" class="wise-link hover:underline">← Back to Leave</a>
    <form method="GET" action="<?php echo e(route('leave.calendar')); ?>" class="flex flex-wrap items-center gap-2">
        <select name="month" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
            <?php for($m = 1; $m <= 12; $m++): ?>
                <option value="<?php echo e($m); ?>" <?php echo e($month == $m ? 'selected' : ''); ?>><?php echo e(\Carbon\Carbon::createFromDate(2000, $m, 1)->format('F')); ?></option>
            <?php endfor; ?>
        </select>
        <select name="year" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
            <?php for($y = now()->year + 1; $y >= now()->year - 2; $y--): ?>
                <option value="<?php echo e($y); ?>" <?php echo e($year == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
            <?php endfor; ?>
        </select>
        <select name="employee_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
            <option value="">All employees</option>
            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($e->id); ?>" <?php echo e(request('employee_id') == $e->id ? 'selected' : ''); ?>><?php echo e($e->full_name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button type="submit" class="px-3 py-1.5 wise-btn text-white rounded-lg text-sm">Apply</button>
    </form>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <div class="p-4 border-b border-slate-200 dark:border-slate-700">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100"><?php echo e($start->format('F Y')); ?></h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Employee</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Leave type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Start</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">End</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Days</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php $__empty_1 = true; $__currentLoopData = $leaveRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="px-4 py-3 text-slate-900 dark:text-slate-100"><?php echo e($lr->employee->full_name ?? '—'); ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs rounded" style="background:<?php echo e($lr->leaveType->color ?? '#94a3b8'); ?>20;color:<?php echo e($lr->leaveType->color ?? '#64748b'); ?>"><?php echo e($lr->leaveType->name ?? '—'); ?></span>
                    </td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($lr->start_date->format('M j, Y')); ?></td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($lr->end_date->format('M j, Y')); ?></td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($lr->days); ?></td>
                    <td class="px-4 py-3 text-right">
                        <a href="<?php echo e(route('leave.show', $lr)); ?>" class="wise-link hover:underline text-sm">View</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No approved leave in this period.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Leave\Providers/../Resources/views/requests/calendar.blade.php ENDPATH**/ ?>
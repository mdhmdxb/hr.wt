

<?php $__env->startSection('title', 'Payroll Run'); ?>
<?php $__env->startSection('heading', 'Payroll Run'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4 flex flex-wrap items-center gap-4">
    <a href="<?php echo e(route('payroll.index')); ?>" class="wise-link hover:underline">← Back to Payroll</a>
    <?php if($payroll->status === 'draft'): ?>
    <form method="POST" action="<?php echo e(route('payroll.finalize', $payroll)); ?>" class="inline" onsubmit="return confirm('Finalize this run? You will not be able to edit after.');">
        <?php echo csrf_field(); ?>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Finalize Run</button>
    </form>
    <?php endif; ?>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-4 mb-4">
    <p class="text-slate-600 dark:text-slate-400"><strong>Period:</strong> <?php echo e($payroll->period_start->format('Y-m-d')); ?> – <?php echo e($payroll->period_end->format('Y-m-d')); ?> &nbsp;|&nbsp; <strong>Status:</strong>
        <?php if($payroll->status === 'draft'): ?>
            <span class="px-2 py-0.5 text-xs rounded bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">Draft</span>
        <?php elseif($payroll->status === 'finalized'): ?>
            <span class="px-2 py-0.5 text-xs rounded bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">Finalized</span>
        <?php else: ?>
            <span class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Paid</span>
        <?php endif; ?>
    </p>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Employee</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Basic</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Allowances</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Deductions</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Net Pay</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">WPS Salary</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            <?php $__empty_1 = true; $__currentLoopData = $payroll->payslips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ps): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100"><?php echo e($ps->employee->full_name ?? '—'); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e(number_format($ps->basic_salary, 2)); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e(number_format($ps->allowances, 2)); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e(number_format($ps->deductions, 2)); ?></td>
                <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($ps->net_pay, 2)); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e(number_format($ps->total_wps_salary ?? $ps->net_pay, 2)); ?></td>
                <td class="px-4 py-3 text-right">
                    <a href="<?php echo e(route('payroll.payslip', $ps)); ?>" class="wise-link hover:underline">View</a>
                    <?php if($payroll->status === 'draft'): ?>
                    <a href="<?php echo e(route('payroll.payslip.edit', $ps)); ?>" class="wise-link hover:underline ml-2">Edit</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No payslips in this run.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Payroll\Providers/../Resources/views/show.blade.php ENDPATH**/ ?>
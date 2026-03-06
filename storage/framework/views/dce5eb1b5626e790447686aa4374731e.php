

<?php $__env->startSection('title', 'Payroll Run'); ?>
<?php $__env->startSection('heading', 'Payroll Run'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4 flex flex-wrap items-center gap-4 print:hidden">
    <a href="<?php echo e(route('payroll.index')); ?>" class="wise-link hover:underline">← Back to Payroll</a>
    <?php if($payroll->status === 'draft'): ?>
    <form method="POST" action="<?php echo e(route('payroll.finalize', $payroll)); ?>" class="inline" onsubmit="return confirm('Finalize this run? You will not be able to edit after.');">
        <?php echo csrf_field(); ?>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Finalize Run</button>
    </form>
    <?php endif; ?>
    <button type="button" onclick="window.print()" class="ml-auto px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 print:hidden">
        Print summary
    </button>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-4 mb-4">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-slate-600 dark:text-slate-400">
            <strong>Period:</strong> <?php echo e($payroll->period_start->format('Y-m-d')); ?> – <?php echo e($payroll->period_end->format('Y-m-d')); ?>

            &nbsp;|&nbsp;
            <strong>Status:</strong>
            <?php if($payroll->status === 'draft'): ?>
                <span class="px-2 py-0.5 text-xs rounded bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">Draft</span>
            <?php elseif($payroll->status === 'finalized'): ?>
                <span class="px-2 py-0.5 text-xs rounded bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">Finalized</span>
            <?php else: ?>
                <span class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Paid</span>
            <?php endif; ?>
        </p>
        <?php if(isset($employeeOptions) && $employeeOptions->count()): ?>
        <form method="GET" action="<?php echo e(route('payroll.show', $payroll)); ?>" class="flex flex-wrap items-center gap-2 text-sm">
            <label class="text-slate-600 dark:text-slate-400">Employees:</label>
            <select name="employees[]" multiple size="1" class="min-w-[180px] rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white px-2 py-1.5">
                <?php $__currentLoopData = $employeeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($emp->id); ?>" <?php echo e(in_array($emp->id, $selectedEmployeeIds ?? [], true) ? 'selected' : ''); ?>>
                        <?php echo e($emp->full_name ?? $emp->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="submit" class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700">
                Apply
            </button>
            <?php if(!empty($selectedEmployeeIds)): ?>
            <a href="<?php echo e(route('payroll.show', $payroll)); ?>" class="text-xs wise-link ml-1">Clear</a>
            <?php endif; ?>
        </form>
        <?php endif; ?>
    </div>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden print-area">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Employee</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Basic</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Basic</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Accommodation</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Transport</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Other Allow.</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Bonus</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Overtime (h)</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Deductions</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Net Pay</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">WPS Salary</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Remarks</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            <?php $__empty_1 = true; $__currentLoopData = $payroll->payslips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ps): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100"><?php echo e($ps->employee->full_name ?? '—'); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e(number_format($ps->basic_salary, 2)); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e(number_format($ps->accommodation ?? 0, 2)); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e(number_format($ps->transportation ?? 0, 2)); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e(number_format(($ps->food_allowance ?? 0) + ($ps->other_allowances ?? 0), 2)); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e(number_format($ps->bonus ?? 0, 2)); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e(number_format($ps->overtime_hours ?? 0, 2)); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e(number_format($ps->deductions, 2)); ?></td>
                <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($ps->net_pay, 2)); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e(number_format($ps->total_wps_salary ?? $ps->net_pay, 2)); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400 text-sm"><?php echo e($ps->remarks ?? '—'); ?></td>
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
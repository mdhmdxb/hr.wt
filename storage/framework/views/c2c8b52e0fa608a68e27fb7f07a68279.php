

<?php $__env->startSection('title', 'Payslip'); ?>
<?php $__env->startSection('heading', 'Payslip'); ?>

<?php $__env->startSection('content'); ?>
<?php $show = function($key) { return in_array($key, $payslipDisplay ?? [], true); }; ?>
<div class="mb-4 flex items-center justify-between gap-4 print:hidden">
    <a href="<?php echo e(route('payroll.show', $payslip->payrollRun)); ?>" class="wise-link hover:underline">← Back to Run</a>
    <button type="button" onclick="window.print()" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 print:hidden">
        Print payslip
    </button>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl print-area">
    <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Payslip – <?php echo e($payslip->payrollRun->period_start->format('M Y')); ?></h2>
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <?php if($show('employee')): ?><div><dt class="text-sm text-slate-500 dark:text-slate-400">Employee</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($payslip->employee->full_name ?? '—'); ?></dd></div><?php endif; ?>
        <?php if($show('period')): ?><div><dt class="text-sm text-slate-500 dark:text-slate-400">Period</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($payslip->payrollRun->period_start->format('Y-m-d')); ?> – <?php echo e($payslip->payrollRun->period_end->format('Y-m-d')); ?></dd></div><?php endif; ?>
    </dl>

    <div class="mt-6 border-t border-slate-200 dark:border-slate-600 pt-4">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Salary breakdown</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <?php if($show('basic')): ?><div><dt class="text-sm text-slate-500 dark:text-slate-400">Basic</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->basic_salary ?? 0, 2)); ?></dd></div><?php endif; ?>
            <?php if($show('accommodation')): ?><div><dt class="text-sm text-slate-500 dark:text-slate-400">Accommodation</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->accommodation ?? 0, 2)); ?></dd></div><?php endif; ?>
            <?php if($show('transportation')): ?><div><dt class="text-sm text-slate-500 dark:text-slate-400">Transportation</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->transportation ?? 0, 2)); ?></dd></div><?php endif; ?>
            <?php if($show('food_allowance')): ?><div><dt class="text-sm text-slate-500 dark:text-slate-400">Food allowance</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->food_allowance ?? 0, 2)); ?></dd></div><?php endif; ?>
            <?php if($show('other_allowances')): ?><div><dt class="text-sm text-slate-500 dark:text-slate-400">Other allowances</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->other_allowances ?? 0, 2)); ?></dd></div><?php endif; ?>
            <?php if($show('bonus')): ?><div><dt class="text-sm text-slate-500 dark:text-slate-400">Bonus</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->bonus ?? 0, 2)); ?></dd></div><?php endif; ?>
            <?php if($show('days_worked')): ?><div><dt class="text-sm text-slate-500 dark:text-slate-400">Days worked</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($payslip->days_worked !== null ? number_format($payslip->days_worked, 2) : '—'); ?></dd></div><?php endif; ?>
            <?php if($show('days_off')): ?><div><dt class="text-sm text-slate-500 dark:text-slate-400">Days off</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($payslip->days_off !== null ? number_format($payslip->days_off, 2) : '—'); ?></dd></div><?php endif; ?>
            <?php if($show('holiday')): ?><div><dt class="text-sm text-slate-500 dark:text-slate-400">Holiday</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($payslip->holiday !== null ? number_format($payslip->holiday, 2) : '—'); ?></dd></div><?php endif; ?>
            <?php if($show('annual_leave')): ?><div><dt class="text-sm text-slate-500 dark:text-slate-400">Annual leave</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($payslip->annual_leave !== null ? number_format($payslip->annual_leave, 2) : '—'); ?></dd></div><?php endif; ?>
            <?php if($show('unpaid_leave')): ?><div><dt class="text-sm text-slate-500 dark:text-slate-400">Unpaid leave</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($payslip->unpaid_leave !== null ? number_format($payslip->unpaid_leave, 2) : '—'); ?></dd></div><?php endif; ?>
            <?php if($show('overtime_hours')): ?><div><dt class="text-sm text-slate-500 dark:text-slate-400">Regular overtime hours</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->overtime_hours ?? 0, 2)); ?></dd></div><?php endif; ?>
            <?php if($show('off_day_hours')): ?>
            <div>
                <dt class="text-sm text-slate-500 dark:text-slate-400">Hours worked on off days</dt>
                <dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($offDayWorkHours ?? 0, 2)); ?></dd>
            </div>
            <?php endif; ?>
            <?php if($show('off_day_details') && !empty($offDayWorkDetails)): ?>
            <div class="sm:col-span-2">
                <dt class="text-sm text-slate-500 dark:text-slate-400">Off days worked (date · hours · type)</dt>
                <dd class="font-medium text-slate-900 dark:text-slate-100 text-sm mt-1">
                    <?php $__currentLoopData = $offDayWorkDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo e($d['date']); ?> · <?php echo e($d['hours']); ?> h · <?php echo e($d['label']); ?><?php if(!$loop->last): ?>; <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </dd>
            </div>
            <?php endif; ?>
            <?php if($show('overtime_premium')): ?><div><dt class="text-sm text-slate-500 dark:text-slate-400">Overtime premium (MOHRE)</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->overtime_premium ?? 0, 2)); ?></dd></div><?php endif; ?>
            <?php if($show('overtime_bonus')): ?><div><dt class="text-sm text-slate-500 dark:text-slate-400">Overtime bonus + transport + food</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->overtime_bonus_transport_food ?? 0, 2)); ?></dd></div><?php endif; ?>
            <?php if($show('salary_adjustment')): ?><div><dt class="text-sm text-slate-500 dark:text-slate-400">Salary adjustment (deduction)</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->salary_adjustment ?? 0, 2)); ?></dd></div><?php endif; ?>
        </dl>
    </div>

    <?php if($show('totals')): ?>
    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-600">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Total allowances</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->allowances ?? 0, 2)); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Deductions</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->deductions ?? 0, 2)); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Total net salary</dt><dd class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->net_pay ?? 0, 2)); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Total WPS salary</dt><dd class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->total_wps_salary ?? $payslip->net_pay ?? 0, 2)); ?></dd></div>
        </dl>
    </div>
    <?php endif; ?>

    <?php if($show('remarks') && $payslip->remarks): ?>
    <p class="mt-4 text-sm text-slate-600 dark:text-slate-400"><strong>Remarks:</strong> <?php echo e($payslip->remarks); ?></p>
    <?php endif; ?>
    <?php if($show('notes') && $payslip->notes): ?>
    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400"><?php echo e($payslip->notes); ?></p>
    <?php endif; ?>

    <?php if($show('qr_verify') && !empty($payslip->verification_token)): ?>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-600">
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Scan to verify this payslip</p>
        <img src="<?php echo e(route('payroll.payslip.qr', $payslip)); ?>" alt="QR Code" class="inline-block w-44 h-44" width="180" height="180">
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Or open: <a href="<?php echo e(route('payroll.verify', ['payslip' => $payslip->id, 'token' => $payslip->verification_token])); ?>" class="wise-link" target="_blank" rel="noopener">Verification link</a></p>
    </div>
    <?php endif; ?>

    <?php if(!empty($letterFooterText)): ?>
    <p class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-600 text-xs text-slate-500 dark:text-slate-400 italic"><?php echo e($letterFooterText); ?></p>
    <?php endif; ?>
    <?php if(!empty($showStamp) && !empty($stampImageUrl)): ?>
    <div class="mt-4 flex justify-end">
        <img src="<?php echo e($stampImageUrl); ?>" alt="Company stamp" class="max-h-16 max-w-28 opacity-90">
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Payroll\Providers/../Resources/views/payslip.blade.php ENDPATH**/ ?>
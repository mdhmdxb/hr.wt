

<?php $__env->startSection('title', 'Payslip'); ?>
<?php $__env->startSection('heading', 'Payslip'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4">
    <a href="<?php echo e(route('payroll.show', $payslip->payrollRun)); ?>" class="wise-link hover:underline">← Back to Run</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl">
    <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Payslip – <?php echo e($payslip->payrollRun->period_start->format('M Y')); ?></h2>
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Employee</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($payslip->employee->full_name ?? '—'); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Period</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($payslip->payrollRun->period_start->format('Y-m-d')); ?> – <?php echo e($payslip->payrollRun->period_end->format('Y-m-d')); ?></dd></div>
    </dl>

    <div class="mt-6 border-t border-slate-200 dark:border-slate-600 pt-4">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Salary breakdown</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Basic</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->basic_salary ?? 0, 2)); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Accommodation</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->accommodation ?? 0, 2)); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Transportation</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->transportation ?? 0, 2)); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Food allowance</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->food_allowance ?? 0, 2)); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Other allowances</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->other_allowances ?? 0, 2)); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Bonus</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->bonus ?? 0, 2)); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Days worked</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($payslip->days_worked !== null ? number_format($payslip->days_worked, 2) : '—'); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Days off</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($payslip->days_off !== null ? number_format($payslip->days_off, 2) : '—'); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Holiday</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($payslip->holiday !== null ? number_format($payslip->holiday, 2) : '—'); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Annual leave</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($payslip->annual_leave !== null ? number_format($payslip->annual_leave, 2) : '—'); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Unpaid leave</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($payslip->unpaid_leave !== null ? number_format($payslip->unpaid_leave, 2) : '—'); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Regular overtime hours</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->overtime_hours ?? 0, 2)); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Overtime premium (MOHRE)</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->overtime_premium ?? 0, 2)); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Overtime bonus + transport + food</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->overtime_bonus_transport_food ?? 0, 2)); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Salary adjustment (deduction)</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->salary_adjustment ?? 0, 2)); ?></dd></div>
        </dl>
    </div>

    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-600">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Total allowances</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->allowances ?? 0, 2)); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Deductions</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->deductions ?? 0, 2)); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Total net salary</dt><dd class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->net_pay ?? 0, 2)); ?></dd></div>
            <div><dt class="text-sm text-slate-500 dark:text-slate-400">Total WPS salary</dt><dd class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->total_wps_salary ?? $payslip->net_pay ?? 0, 2)); ?></dd></div>
        </dl>
    </div>

    <?php if($payslip->remarks): ?>
    <p class="mt-4 text-sm text-slate-600 dark:text-slate-400"><strong>Remarks:</strong> <?php echo e($payslip->remarks); ?></p>
    <?php endif; ?>
    <?php if($payslip->notes): ?>
    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400"><?php echo e($payslip->notes); ?></p>
    <?php endif; ?>

    <?php if(!empty($payslip->verification_token)): ?>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-600">
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Scan to verify this payslip</p>
        <img src="<?php echo e(route('payroll.payslip.qr', $payslip)); ?>" alt="QR Code" class="inline-block w-44 h-44" width="180" height="180">
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Or open: <a href="<?php echo e(route('payroll.verify', ['payslip' => $payslip->id, 'token' => $payslip->verification_token])); ?>" class="wise-link" target="_blank" rel="noopener">Verification link</a></p>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Payroll\Providers/../Resources/views/payslip.blade.php ENDPATH**/ ?>
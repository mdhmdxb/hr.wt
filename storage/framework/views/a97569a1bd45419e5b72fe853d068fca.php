

<?php $__env->startSection('title', 'Verify Payslip'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-md mx-auto bg-white dark:bg-slate-800 rounded-xl shadow p-6 text-center">
    <?php if($valid): ?>
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Valid payslip</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">This payslip has been verified by Wise HRM.</p>
        <dl class="text-left space-y-2 border-t border-slate-200 dark:border-slate-600 pt-4">
            <div><dt class="text-xs text-slate-500 dark:text-slate-400">Employee</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($payslip->employee->full_name ?? '—'); ?></dd></div>
            <div><dt class="text-xs text-slate-500 dark:text-slate-400">Period</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($payslip->payrollRun->period_start->format('M Y')); ?></dd></div>
            <div><dt class="text-xs text-slate-500 dark:text-slate-400">Total net salary</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($payslip->net_pay ?? 0, 2)); ?></dd></div>
        </dl>
    <?php else: ?>
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Verification failed</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400">This link is invalid or has expired. Please use the QR code or link from the original payslip.</p>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.guest', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Payroll\Providers/../Resources/views/verify-payslip.blade.php ENDPATH**/ ?>
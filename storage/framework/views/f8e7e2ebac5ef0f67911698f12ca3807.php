

<?php $__env->startSection('title', 'Reports'); ?>
<?php $__env->startSection('heading', 'Reports'); ?>

<?php $__env->startSection('content'); ?>
<?php $user = auth()->user(); $isAdmin = $user->isAdmin(); $isHr = $user->isHr(); $isAccounts = $user->isAccounts(); ?>
<p class="text-slate-600 dark:text-slate-400 mb-6">View and filter data by attendance, leave, and payroll. Choose a report below.</p>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php if($isAdmin || $isHr): ?>
    <a href="<?php echo e(route('reports.attendance')); ?>" class="block bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 hover:shadow-xl transition-shadow">
        <span class="text-3xl mb-3 block">🕐</span>
        <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-1">Attendance report</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400">Filter by employee and date range. View check-in/out and status.</p>
    </a>
    <a href="<?php echo e(route('reports.leave')); ?>" class="block bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 hover:shadow-xl transition-shadow">
        <span class="text-3xl mb-3 block">📅</span>
        <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-1">Leave report</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400">Filter by employee, status, and dates. View leave requests and approval.</p>
    </a>
    <?php endif; ?>
    <?php if($isAdmin || $isAccounts): ?>
    <a href="<?php echo e(route('reports.payroll')); ?>" class="block bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 hover:shadow-xl transition-shadow">
        <span class="text-3xl mb-3 block">💰</span>
        <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-1">Payroll report</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400">View runs by period. Drill into a run for totals and payslip summary.</p>
    </a>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Core\Providers/../Resources/views/reports/index.blade.php ENDPATH**/ ?>
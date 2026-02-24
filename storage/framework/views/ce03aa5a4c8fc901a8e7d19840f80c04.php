

<?php $__env->startSection('title', 'Payroll report'); ?>
<?php $__env->startSection('heading', 'Payroll report'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4 flex flex-wrap items-center gap-4">
    <a href="<?php echo e(route('reports.index')); ?>" class="wise-link hover:underline">← Reports</a>
</div>
<form method="GET" action="<?php echo e(route('reports.payroll')); ?>" class="mb-6 p-4 bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200 dark:border-slate-700 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-0.5">Year</label>
        <select name="year" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
            <option value="">All</option>
            <?php for($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                <option value="<?php echo e($y); ?>" <?php echo e(request('year') == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div>
        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-0.5">Month</label>
        <select name="month" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
            <option value="">All</option>
            <?php for($m = 1; $m <= 12; $m++): ?>
                <option value="<?php echo e($m); ?>" <?php echo e(request('month') == $m ? 'selected' : ''); ?>><?php echo e(date('F', mktime(0,0,0,$m,1))); ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <button type="submit" class="px-3 py-1.5 wise-btn text-white rounded-lg text-sm">Apply</button>
</form>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Period</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Payslips</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            <?php $__empty_1 = true; $__currentLoopData = $runs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $run): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="px-4 py-3 text-slate-900 dark:text-slate-100"><?php echo e($run->period_start->format('Y-m-d')); ?> – <?php echo e($run->period_end->format('Y-m-d')); ?></td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($run->payslips_count); ?></td>
                <td class="px-4 py-3"><span class="px-2 py-0.5 text-xs rounded <?php echo e($run->status === 'draft' ? 'bg-slate-100 dark:bg-slate-700' : ($run->status === 'finalized' ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-green-100 dark:bg-green-900/30')); ?>"><?php echo e($run->status); ?></span></td>
                <td class="px-4 py-3 text-right">
                    <a href="<?php echo e(route('reports.payroll')); ?>?run_id=<?php echo e($run->id); ?>" class="wise-link hover:underline text-sm">View summary</a>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No payroll runs.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if($runs->hasPages()): ?>
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700"><?php echo e($runs->links()); ?></div>
    <?php endif; ?>
</div>
<?php if($summary): ?>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 border border-slate-200 dark:border-slate-700">
    <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Run summary: <?php echo e($summary['run']->period_start->format('M Y')); ?></h3>
    <dl class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Total basic</dt><dd class="text-lg font-semibold text-slate-900 dark:text-slate-100"><?php echo e(number_format($summary['total_basic'], 2)); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Total allowances</dt><dd class="text-lg font-semibold text-slate-900 dark:text-slate-100"><?php echo e(number_format($summary['total_allowances'], 2)); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Total deductions</dt><dd class="text-lg font-semibold text-slate-900 dark:text-slate-100"><?php echo e(number_format($summary['total_deductions'], 2)); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Total net pay</dt><dd class="text-lg font-semibold text-slate-900 dark:text-slate-100"><?php echo e(number_format($summary['total_net'], 2)); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Total WPS salary</dt><dd class="text-lg font-semibold text-slate-900 dark:text-slate-100"><?php echo e(number_format($summary['total_wps_salary'] ?? $summary['total_net'], 2)); ?></dd></div>
    </dl>
    <a href="<?php echo e(route('payroll.show', $summary['run'])); ?>" class="mt-4 inline-block wise-link hover:underline">View full run →</a>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Core\Providers/../Resources/views/reports/payroll.blade.php ENDPATH**/ ?>
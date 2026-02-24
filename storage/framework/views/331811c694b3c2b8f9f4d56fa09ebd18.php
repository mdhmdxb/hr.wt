

<?php $__env->startSection('title', 'Batch Attendance'); ?>
<?php $__env->startSection('heading', 'Batch attendance entry'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6">
        <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Select month & employee</h2>
        <form method="GET" action="<?php echo e(route('attendance.batch')); ?>" class="flex flex-wrap items-end gap-4">
            <div class="min-w-52">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Employee</label>
                <select name="employee_id" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <option value="">— Select —</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($e->id); ?>" <?php echo e(($employeeId ?? '') == $e->id ? 'selected' : ''); ?>><?php echo e($e->full_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Month</label>
                <select name="month" class="rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <?php for($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo e($m); ?>" <?php echo e(($month ?? Carbon\Carbon::now()->month) == $m ? 'selected' : ''); ?>><?php echo e(\Carbon\Carbon::createFromDate(2000, $m, 1)->format('F')); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Year</label>
                <select name="year" class="rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <?php for($y = now()->year + 1; $y >= now()->year - 5; $y--): ?>
                        <option value="<?php echo e($y); ?>" <?php echo e(($year ?? Carbon\Carbon::now()->year) == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-xl font-medium">Load</button>
        </form>
    </div>

    <?php if($employee && $employeeId): ?>
    
    <form method="POST" action="<?php echo e(route('attendance.batch.store')); ?>" class="space-y-4">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="employee_id" value="<?php echo e($employeeId); ?>">
        <input type="hidden" name="month" value="<?php echo e($month); ?>">
        <input type="hidden" name="year" value="<?php echo e($year); ?>">

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-4">
                <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100">
                    <?php echo e($employee->full_name); ?> — <?php echo e(\Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y')); ?>

                </h2>
                <button type="submit" class="px-5 py-2.5 wise-btn text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all">Save batch</button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-32 whitespace-nowrap">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-20">Day</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-28">Check in</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-28">Check out</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-32">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase w-44 max-w-48">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        <?php for($day = 1; $day <= $daysInMonth; $day++): ?>
                            <?php
                                $date = \Carbon\Carbon::createFromDate($year, $month, $day);
                                $dateStr = $date->format('Y-m-d');
                                $rec = $existing[$dateStr] ?? null;
                                $isWeekend = $date->isWeekend();
                                if ($rec) {
                                    $defaultStatus = $rec->status;
                                } elseif ($employee->isWeeklyOffDay($date)) {
                                    $defaultStatus = 'weekly_off';
                                } elseif ($employee->isAlternateSaturdayOffDay($date)) {
                                    $defaultStatus = 'alt_saturday_off';
                                } else {
                                    $defaultStatus = 'present';
                                }
                            ?>
                            <tr class="<?php echo e($isWeekend ? 'bg-slate-50/50 dark:bg-slate-800/50' : ''); ?>">
                                <td class="px-4 py-2 text-slate-700 dark:text-slate-300 text-sm font-medium whitespace-nowrap"><?php echo e($dateStr); ?></td>
                                <td class="px-4 py-2 text-slate-500 dark:text-slate-400 text-sm"><?php echo e($date->format('D')); ?></td>
                                <td class="px-4 py-2">
                                    <input type="hidden" name="attendance[<?php echo e($day); ?>][date]" value="<?php echo e($dateStr); ?>">
                                    <input type="time" name="attendance[<?php echo e($day); ?>][check_in_at]" value="<?php echo e($rec && $rec->check_in_at ? \Carbon\Carbon::parse($rec->check_in_at)->format('H:i') : ($employee->shift_start ?? '')); ?>"
                                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="time" name="attendance[<?php echo e($day); ?>][check_out_at]" value="<?php echo e($rec && $rec->check_out_at ? \Carbon\Carbon::parse($rec->check_out_at)->format('H:i') : ($employee->shift_end ?? '')); ?>"
                                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                                </td>
                                <td class="px-4 py-2">
                                    <select name="attendance[<?php echo e($day); ?>][status]" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                                        <?php $__currentLoopData = \Modules\Attendance\Models\Attendance::statusOptions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($val); ?>" <?php echo e($defaultStatus === $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" name="attendance[<?php echo e($day); ?>][notes]" value="<?php echo e($rec ? $rec->notes : ''); ?>" placeholder="Note..."
                                        class="w-full max-w-44 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
    <?php else: ?>
    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 text-center">
        <p class="text-slate-500 dark:text-slate-400">Select an employee, month and year, then click <strong>Load</strong> to edit attendance for that month.</p>
    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Attendance\Providers/../Resources/views/batch.blade.php ENDPATH**/ ?>
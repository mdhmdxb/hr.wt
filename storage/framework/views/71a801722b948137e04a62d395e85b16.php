

<?php $__env->startSection('title', $employee->full_name); ?>
<?php $__env->startSection('heading', $employee->full_name); ?>

<?php $__env->startSection('content'); ?>
<div class="flex gap-4 mb-4">
    <a href="<?php echo e(route('employee.edit', $employee)); ?>" class="px-4 py-2 wise-btn text-white rounded-lg">Edit</a>
    <a href="<?php echo e(route('employee.index')); ?>" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Back to list</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Employee Code</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($employee->employee_code); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Email</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($employee->email); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Phone</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($employee->phone ?? '—'); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Branch</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($employee->branch->name ?? '—'); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Site</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($employee->site->name ?? '—'); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Department</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($employee->department->name ?? '—'); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Designation</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($employee->designation->name ?? '—'); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Reporting Manager</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($employee->reportingManager->full_name ?? '—'); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Hire Date</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($employee->hire_date?->format('Y-m-d')); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Employment Type</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($employee->employment_type); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Status</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($employee->status); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Portal login</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php if($employee->user): ?> Yes (<?php echo e($employee->user->email); ?>) <?php else: ?> No — <a href="<?php echo e(route('employee.edit', $employee)); ?>" class="wise-link">Edit employee</a> to create login <?php endif; ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Basic Salary</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($employee->basic_salary ?? 0, 2)); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Accommodation</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($employee->accommodation ?? 0, 2)); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Transportation</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($employee->transportation ?? 0, 2)); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Food allowance</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($employee->food_allowance ?? 0, 2)); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Other allowances</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e(number_format($employee->other_allowances ?? 0, 2)); ?></dd></div>
        <?php if($employee->getWeeklyOffDaysList() || $employee->getAlternateSaturdayWeeksList() || $employee->shift_start || $employee->shift_end): ?>
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500 dark:text-slate-400">Weekly off</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($employee->getWeeklyOffDaysList() ? implode(', ', array_map('ucfirst', $employee->getWeeklyOffDaysList())) : '—'); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Alt. Saturday off</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($employee->getAlternateSaturdayWeeksList() ? implode(', ', array_map(function ($w) { return $w . (['1'=>'st','2'=>'nd','3'=>'rd','4'=>'th','5'=>'th'][$w] ?? 'th') . ' Sat'; }, $employee->getAlternateSaturdayWeeksList())) : 'No'); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Default shift</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($employee->shift_start && $employee->shift_end ? $employee->shift_start . ' – ' . $employee->shift_end : '—'); ?></dd></div>
        <?php endif; ?>
    </dl>
    
</div>
<?php if(auth()->user()->isAdmin() || auth()->user()->isHr()): ?>
<div class="mt-6 bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl">
    <div class="flex items-center justify-between mb-4">
        <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100">Documents</h3>
        <a href="<?php echo e(route('documents.create', ['employee_id' => $employee->id])); ?>" class="text-sm wise-link">Add document</a>
    </div>
    <?php if($documents->isNotEmpty()): ?>
    <ul class="space-y-2">
        <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700/50 last:border-0">
            <span class="text-slate-700 dark:text-slate-300"><?php echo e(\Modules\Core\Models\EmployeeDocument::typeOptions()[$doc->type] ?? $doc->type); ?><?php echo e($doc->title ? ': ' . $doc->title : ''); ?></span>
            <span class="text-slate-500 dark:text-slate-400 text-sm"><?php echo e($doc->expiry_date ? $doc->expiry_date->format('Y-m-d') : '—'); ?></span>
            <a href="<?php echo e(route('documents.show', $doc)); ?>" class="text-sm wise-link">View</a>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <?php else: ?>
    <p class="text-slate-500 dark:text-slate-400 text-sm">No documents yet. <a href="<?php echo e(route('documents.create', ['employee_id' => $employee->id])); ?>" class="wise-link">Add document</a></p>
    <?php endif; ?>
</div>
<div class="mt-6 bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl">
    <div class="flex items-center justify-between mb-4">
        <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100">Assigned assets</h3>
        <a href="<?php echo e(route('assets.index', ['employee_id' => $employee->id])); ?>" class="text-sm wise-link">View all assets</a>
    </div>
    <?php if(isset($assetAssignments) && $assetAssignments->isNotEmpty()): ?>
    <ul class="space-y-2">
        <?php $__currentLoopData = $assetAssignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700/50 last:border-0">
            <span class="text-slate-700 dark:text-slate-300"><?php echo e($a->asset->assetType->name ?? '—'); ?>: <?php echo e($a->asset->name); ?></span>
            <a href="<?php echo e(route('assets.show', $a->asset)); ?>" class="text-sm wise-link">View</a>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <?php else: ?>
    <p class="text-slate-500 dark:text-slate-400 text-sm">No assets currently assigned.</p>
    <?php endif; ?>
</div>
<?php $revisions = $employee->salaryRevisions ?? collect(); ?>
<?php if($revisions->isNotEmpty()): ?>
<div class="mt-6 bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl">
    <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-4">Salary revision history</h3>
    <ul class="space-y-2">
        <?php $__currentLoopData = $revisions->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-700/50 last:border-0 text-sm">
            <span class="text-slate-700 dark:text-slate-300"><?php echo e($rev->effective_from->format('Y-m-d')); ?> — Basic <?php echo e(number_format($rev->basic_salary ?? 0, 2)); ?></span>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Employee\Providers/../Resources/views/show.blade.php ENDPATH**/ ?>


<?php $__env->startSection('title', 'Edit Payslip'); ?>
<?php $__env->startSection('heading', 'Edit Payslip'); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('payroll.payslip.update', $payslip)); ?>" class="max-w-3xl space-y-6" id="payslip-form">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-6">
        <p class="text-sm text-slate-600 dark:text-slate-400"><strong><?php echo e($payslip->employee->full_name ?? '—'); ?></strong> · <?php echo e($payslip->payrollRun->period_start->format('M Y')); ?></p>

        
        <div>
            <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Salary & allowances</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Basic salary *</label>
                    <input type="number" name="basic_salary" value="<?php echo e(old('basic_salary', $payslip->basic_salary)); ?>" step="0.01" min="0" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <?php $__errorArgs = ['basic_salary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Accommodation</label>
                    <input type="number" name="accommodation" value="<?php echo e(old('accommodation', $payslip->accommodation ?? 0)); ?>" step="0.01" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <?php $__errorArgs = ['accommodation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Transportation</label>
                    <input type="number" name="transportation" value="<?php echo e(old('transportation', $payslip->transportation ?? 0)); ?>" step="0.01" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <?php $__errorArgs = ['transportation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Food allowance</label>
                    <input type="number" name="food_allowance" value="<?php echo e(old('food_allowance', $payslip->food_allowance ?? 0)); ?>" step="0.01" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <?php $__errorArgs = ['food_allowance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Other allowances</label>
                    <input type="number" name="other_allowances" value="<?php echo e(old('other_allowances', $payslip->other_allowances ?? 0)); ?>" step="0.01" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <?php $__errorArgs = ['other_allowances'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Bonus</label>
                    <input type="number" name="bonus" value="<?php echo e(old('bonus', $payslip->bonus ?? 0)); ?>" step="0.01" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <?php $__errorArgs = ['bonus'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </div>

        
        <div>
            <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Days</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Days worked</label>
                    <input type="number" name="days_worked" value="<?php echo e(old('days_worked', $payslip->days_worked)); ?>" step="0.01" min="0" placeholder="—" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <?php $__errorArgs = ['days_worked'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Days off</label>
                    <input type="number" name="days_off" value="<?php echo e(old('days_off', $payslip->days_off)); ?>" step="0.01" min="0" placeholder="—" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <?php $__errorArgs = ['days_off'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Holiday</label>
                    <input type="number" name="holiday" value="<?php echo e(old('holiday', $payslip->holiday)); ?>" step="0.01" min="0" placeholder="—" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <?php $__errorArgs = ['holiday'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Annual leave</label>
                    <input type="number" name="annual_leave" value="<?php echo e(old('annual_leave', $payslip->annual_leave)); ?>" step="0.01" min="0" placeholder="—" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <?php $__errorArgs = ['annual_leave'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Unpaid leave</label>
                    <input type="number" name="unpaid_leave" value="<?php echo e(old('unpaid_leave', $payslip->unpaid_leave)); ?>" step="0.01" min="0" placeholder="—" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <?php $__errorArgs = ['unpaid_leave'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </div>

        
        <div>
            <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Overtime</h3>
            <?php if(isset($offDayWorkHours) && ($offDayWorkHours > 0 || !empty($offDayWorkDetails))): ?>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                <strong>Hours worked on off days (from attendance):</strong> <?php echo e(number_format($offDayWorkHours ?? 0, 2)); ?>

                <?php if(!empty($offDayWorkDetails)): ?>
                    —
                    <?php $__currentLoopData = $offDayWorkDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo e($d['date']); ?> (<?php echo e($d['hours']); ?> h, <?php echo e($d['label']); ?>)<?php if(!$loop->last): ?>; <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </p>
            <?php endif; ?>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Regular overtime hours</label>
                    <input type="number" name="overtime_hours" value="<?php echo e(old('overtime_hours', $payslip->overtime_hours ?? 0)); ?>" step="0.01" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <?php $__errorArgs = ['overtime_hours'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Overtime premium (MOHRE)</label>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Off days / night time per MOHRE UAE</p>
                    <input type="number" name="overtime_premium" value="<?php echo e(old('overtime_premium', $payslip->overtime_premium ?? 0)); ?>" step="0.01" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <?php $__errorArgs = ['overtime_premium'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Overtime bonus + transport + food</label>
                    <input type="number" name="overtime_bonus_transport_food" value="<?php echo e(old('overtime_bonus_transport_food', $payslip->overtime_bonus_transport_food ?? 0)); ?>" step="0.01" min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <?php $__errorArgs = ['overtime_bonus_transport_food'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </div>

        
        <div>
            <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Deduction & remarks</h3>
            <div class="grid grid-cols-1 gap-4">
                <div class="max-w-xs">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Salary adjustment (deduction)</label>
                    <input type="number" name="salary_adjustment" value="<?php echo e(old('salary_adjustment', $payslip->salary_adjustment ?? 0)); ?>" step="0.01" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" placeholder="0">
                    <?php $__errorArgs = ['salary_adjustment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Remarks</label>
                    <textarea name="remarks" rows="2" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" placeholder="Optional notes"><?php echo e(old('remarks', $payslip->remarks)); ?></textarea>
                    <?php $__errorArgs = ['remarks'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </div>

        <p class="text-sm text-slate-500 dark:text-slate-400">Total net salary and Total WPS salary are calculated on save (Basic + all allowances − salary adjustment).</p>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Update Payslip</button>
            <a href="<?php echo e(route('payroll.show', $payslip->payrollRun)); ?>" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Payroll\Providers/../Resources/views/edit-payslip.blade.php ENDPATH**/ ?>
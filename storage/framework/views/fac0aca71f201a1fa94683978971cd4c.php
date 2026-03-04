

<?php $__env->startSection('title', 'Leave Request'); ?>
<?php $__env->startSection('heading', 'Leave Request'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4">
    <a href="<?php echo e(route('leave.index')); ?>" class="wise-link hover:underline">← Back to Leave</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 max-w-2xl">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Employee</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($leaveRequest->employee->full_name ?? '—'); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Leave type</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($leaveRequest->leaveType->name ?? '—'); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Start date</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($leaveRequest->start_date->format('Y-m-d')); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">End date</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($leaveRequest->end_date->format('Y-m-d')); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Days</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($leaveRequest->days); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Application date</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($leaveRequest->created_at?->format('Y-m-d H:i')); ?></dd></div>
        <?php if($leaveRequest->actual_return_date): ?>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Actual return date</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($leaveRequest->actual_return_date->format('Y-m-d')); ?></dd></div>
        <?php if($leaveRequest->overstay_days): ?>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Overstay (unpaid)</dt><dd class="font-medium text-amber-700 dark:text-amber-400"><?php echo e($leaveRequest->overstay_days); ?> day(s) — counted as unpaid leave</dd></div>
        <?php endif; ?>
        <?php endif; ?>
        <?php if(!$leaveRequest->leaveType->is_paid && $leaveRequest->employee && $leaveRequest->employee->basic_salary): ?>
        <?php $dailyRate = (float) $leaveRequest->employee->basic_salary / 30; $estDeduction = round($dailyRate * $leaveRequest->days, 2); ?>
        <div class="sm:col-span-2">
            <dt class="text-sm text-slate-500 dark:text-slate-400">Estimated salary impact (unpaid leave)</dt>
            <dd class="font-medium text-amber-700 dark:text-amber-400">− <?php echo e(number_format($estDeduction, 2)); ?> (<?php echo e($leaveRequest->days); ?> days × <?php echo e(number_format($dailyRate, 2)); ?>/day)</dd>
        </div>
        <?php endif; ?>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Status</dt><dd class="font-medium">
            <?php if($leaveRequest->status === 'pending'): ?>
                <span class="px-2 py-1 text-xs rounded bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Pending</span>
            <?php elseif($leaveRequest->status === 'approved'): ?>
                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Approved</span>
            <?php elseif($leaveRequest->status === 'cancelled'): ?>
                <span class="px-2 py-1 text-xs rounded bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-100">Cancelled</span>
            <?php else: ?>
                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Rejected</span>
            <?php endif; ?>
        </dd></div>
        <?php if($leaveRequest->reason): ?>
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500 dark:text-slate-400">Reason</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($leaveRequest->reason); ?></dd></div>
        <?php endif; ?>
        <?php if($leaveRequest->supporting_document_path): ?>
        <div class="sm:col-span-2">
            <dt class="text-sm text-slate-500 dark:text-slate-400">Supporting document</dt>
            <dd class="font-medium text-slate-900 dark:text-slate-100">
                <a href="<?php echo e(route('leave.document', $leaveRequest)); ?>" class="wise-link text-sm" target="_blank">Download document</a>
            </dd>
        </div>
        <?php endif; ?>
        <?php if($leaveRequest->approvalSteps->isNotEmpty()): ?>
        <div class="sm:col-span-2">
            <dt class="text-sm text-slate-500 dark:text-slate-400 mb-2">Approval workflow</dt>
            <dd class="space-y-1">
                <?php $__currentLoopData = $leaveRequest->approvalSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-2 text-sm">
                    <span class="font-medium text-slate-700 dark:text-slate-300"><?php echo e($loop->iteration); ?>. <?php echo e(\Modules\Leave\Models\LeaveApprovalStep::approverTypeOptions()[$step->approver_type] ?? $step->approver_type); ?></span>
                    <?php if($step->status === 'approved'): ?>
                        <span class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Approved</span>
                        <?php if($step->approvedByUser): ?> <span class="text-slate-500 dark:text-slate-400">by <?php echo e($step->approvedByUser->name); ?></span> <?php endif; ?>
                        <span class="text-slate-500 dark:text-slate-400"><?php echo e($step->approved_at?->format('M j, Y H:i')); ?></span>
                    <?php elseif($step->status === 'rejected'): ?>
                        <span class="px-2 py-0.5 text-xs rounded bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Rejected</span>
                        <?php if($step->approvedByUser): ?> <span class="text-slate-500 dark:text-slate-400">by <?php echo e($step->approvedByUser->name); ?></span> <?php endif; ?>
                        <span class="text-slate-500 dark:text-slate-400"><?php echo e($step->approved_at?->format('M j, Y H:i')); ?></span>
                        <?php if($step->approvedByUser): ?> <span class="text-slate-500 dark:text-slate-400">by <?php echo e($step->approvedByUser->name); ?></span> <?php endif; ?>
                    <?php else: ?>
                        <span class="px-2 py-0.5 text-xs rounded bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Pending</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </dd>
        </div>
        <?php endif; ?>
        
        <?php if($leaveRequest->status === 'approved' && $leaveRequest->approved_by): ?>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Approved by</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($leaveRequest->approvedByUser->name ?? '—'); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Approved at</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($leaveRequest->approved_at?->format('Y-m-d H:i')); ?></dd></div>
        <?php elseif($leaveRequest->status === 'rejected' && $leaveRequest->approved_by): ?>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Rejected by</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($leaveRequest->approvedByUser->name ?? '—'); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Rejected at</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($leaveRequest->approved_at?->format('Y-m-d H:i')); ?></dd></div>
        <?php elseif($leaveRequest->status === 'cancelled' && $leaveRequest->cancelled_by): ?>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Cancelled by</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($leaveRequest->cancelledByUser->name ?? '—'); ?></dd></div>
        <div><dt class="text-sm text-slate-500 dark:text-slate-400">Cancelled at</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($leaveRequest->cancelled_at?->format('Y-m-d H:i')); ?></dd></div>
        <?php endif; ?>
        <?php if($leaveRequest->rejection_reason): ?>
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500 dark:text-slate-400">Rejection reason</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($leaveRequest->rejection_reason); ?></dd></div>
        <?php endif; ?>
        <?php if($leaveRequest->cancel_reason): ?>
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500 dark:text-slate-400">Cancel reason</dt><dd class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($leaveRequest->cancel_reason); ?></dd></div>
        <?php endif; ?>
    </dl>
    <?php if($leaveRequest->status === 'approved' && !$leaveRequest->actual_return_date): ?>
    <div class="mt-6 p-4 bg-slate-50 dark:bg-slate-700/30 rounded-lg">
        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 mb-2">Record return from leave</h3>
        <p class="text-xs text-slate-600 dark:text-slate-400 mb-2">When the employee returns: early return adds unused days back to remaining leave; returning after the end date records overstay as unpaid days.</p>
        <form method="POST" action="<?php echo e(route('leave.record-return', $leaveRequest)); ?>" class="flex flex-wrap items-end gap-2">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Actual return date</label>
                <input type="date" name="actual_return_date" required class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 text-sm">Record return</button>
        </form>
    </div>
    <?php endif; ?>
    <?php if($leaveRequest->status === 'approved' || $leaveRequest->status === 'cancelled'): ?>
    <div class="mt-6 flex flex-wrap items-center gap-4">
        <a href="<?php echo e(route('leave.letter', $leaveRequest)); ?>" class="px-4 py-2 wise-btn text-white rounded-lg inline-flex items-center gap-2">
            <span>📄</span> Download letter (PDF)
        </a>
        <?php if(!empty($leaveRequest->verification_token)): ?>
        <div class="flex items-center gap-2">
            <img src="<?php echo e(route('leave.qr', $leaveRequest)); ?>" alt="QR" class="w-16 h-16" width="64" height="64">
            <div class="text-xs text-slate-500 dark:text-slate-400">
                <p>Scan to verify</p>
                <a href="<?php echo e(route('leave.verify', ['leaveRequest' => $leaveRequest->id, 'token' => $leaveRequest->verification_token])); ?>" target="_blank" rel="noopener" class="wise-link">Verification link</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="mt-6 flex flex-wrap gap-2">
        <?php if($leaveRequest->status === 'pending' && $leaveRequest->canBeActedOnBy(auth()->user())): ?>
        <form method="POST" action="<?php echo e(route('leave.approve', $leaveRequest)); ?>" class="inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Approve</button>
        </form>
        <form method="POST" action="<?php echo e(route('leave.reject', $leaveRequest)); ?>" class="inline flex items-center gap-2">
            <?php echo csrf_field(); ?>
            <input type="text" name="rejection_reason" placeholder="Reason (optional)" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Reject</button>
        </form>
        <?php endif; ?>
        <?php if($leaveRequest->canBeCancelledBy(auth()->user())): ?>
        <form method="POST" action="<?php echo e(route('leave.cancel', $leaveRequest)); ?>" class="inline flex items-center gap-2">
            <?php echo csrf_field(); ?>
            <input type="text" name="cancel_reason" placeholder="Cancel reason (optional)" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
            <button type="submit" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700">Cancel request</button>
        </form>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Leave\Providers/../Resources/views/requests/show.blade.php ENDPATH**/ ?>
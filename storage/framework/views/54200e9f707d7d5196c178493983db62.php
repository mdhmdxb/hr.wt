<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Approval Letter</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; line-height: 1.5; max-width: 700px; margin: 0 auto; padding: 24px; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; }
        .header img { max-height: 56px; }
        h1 { font-size: 18px; margin: 0 0 24px; color: #0f172a; }
        .meta { margin-bottom: 20px; }
        .meta p { margin: 4px 0; }
        .approvals { margin: 20px 0; padding: 12px; background: #f8fafc; border-radius: 8px; }
        .approvals p { margin: 4px 0; }
        .footer { margin-top: 32px; font-size: 11px; color: #64748b; }
        .qr { margin-top: 16px; }
        .qr img { width: 100px; height: 100px; }
    </style>
</head>
<body>
    <div class="header">
        <?php if($logoUrl): ?>
        <img src="<?php echo e($logoUrl); ?>" alt="Logo">
        <?php endif; ?>
        <p style="margin:8px 0 0;font-weight:bold;"><?php echo e($companyName ?? 'Company'); ?></p>
    </div>
    <h1>Leave Approval Letter</h1>
    <div class="meta">
        <p><strong>Employee:</strong> <?php echo e($leaveRequest->employee->full_name ?? '—'); ?></p>
        <p><strong>Leave type:</strong> <?php echo e($leaveRequest->leaveType->name ?? '—'); ?></p>
        <p><strong>Start date:</strong> <?php echo e($leaveRequest->start_date->format('F j, Y')); ?></p>
        <p><strong>End date:</strong> <?php echo e($leaveRequest->end_date->format('F j, Y')); ?></p>
        <p><strong>Days:</strong> <?php echo e($leaveRequest->days); ?></p>
        <?php if($leaveRequest->reason): ?>
        <p><strong>Reason:</strong> <?php echo e($leaveRequest->reason); ?></p>
        <?php endif; ?>
    </div>
    <p>This is to certify that the above leave has been <strong>approved</strong>.</p>
    <?php if($leaveRequest->approvalSteps->isNotEmpty()): ?>
    <div class="approvals">
        <p><strong>Approval chain:</strong></p>
        <?php $__currentLoopData = $leaveRequest->approvalSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <p><?php echo e($loop->iteration); ?>. <?php echo e(\Modules\Leave\Models\LeaveApprovalStep::approverTypeOptions()[$step->approver_type] ?? $step->approver_type); ?>: <?php echo e(ucfirst($step->status)); ?><?php if($step->approved_at): ?> (<?php echo e($step->approved_at->format('M j, Y')); ?>)<?php endif; ?></p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
    <p>Generated on <?php echo e(now()->format('F j, Y H:i')); ?>.</p>
    <?php if($verificationUrl && $qrImageUrl): ?>
    <div class="footer qr">
        <p>Scan to verify this letter:</p>
        <img src="<?php echo e($qrImageUrl); ?>" alt="QR Code" width="100" height="100">
        <p style="margin-top:8px;">Or open: <?php echo e($verificationUrl); ?></p>
    </div>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Leave\Providers/../Resources/views/letter.blade.php ENDPATH**/ ?>
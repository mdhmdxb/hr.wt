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
        .approval-row { display: flex; align-items: center; gap: 12px; margin: 6px 0; }
        .approval-sig { max-height: 36px; max-width: 100px; }
        .stamp-seal { position: fixed; bottom: 24px; right: 24px; max-width: 120px; max-height: 80px; opacity: 0.85; }
        .footer { margin-top: 32px; font-size: 11px; color: #64748b; }
        .letter-footer-note { margin-top: 16px; padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 10px; color: #64748b; font-style: italic; }
        .qr { margin-top: 16px; }
        .qr img { width: 100px; height: 100px; }
    </style>
</head>
<body>
    <div class="header">
        @if($logoUrl)
        <img src="{{ $logoUrl }}" alt="Logo">
        @endif
        <p style="margin:8px 0 0;font-weight:bold;">{{ $companyName ?? 'Company' }}</p>
    </div>
    @if($leaveRequest->status === \Modules\Leave\Models\LeaveRequest::STATUS_CANCELLED)
    <div style="position:fixed; top:40%; left:50%; transform:translate(-50%, -50%) rotate(-20deg); font-size:48px; font-weight:bold; color:rgba(220,38,38,0.25); border:4px solid rgba(220,38,38,0.4); padding:16px 32px; text-transform:uppercase; letter-spacing:4px;">
        Cancelled
    </div>
    @endif
    <h1>Leave {{ $leaveRequest->status === \Modules\Leave\Models\LeaveRequest::STATUS_CANCELLED ? 'Cancellation / ' : '' }}Approval Letter</h1>
    <div class="meta">
        <p><strong>Employee:</strong> {{ $leaveRequest->employee->full_name ?? '—' }}</p>
        <p><strong>Leave type:</strong> {{ $leaveRequest->leaveType->name ?? '—' }}</p>
        <p><strong>Start date:</strong> {{ $leaveRequest->start_date->format('F j, Y') }}</p>
        <p><strong>End date:</strong> {{ $leaveRequest->end_date->format('F j, Y') }}</p>
        <p><strong>Days:</strong> {{ $leaveRequest->days }}</p>
        @if($leaveRequest->reason)
        <p><strong>Reason:</strong> {{ $leaveRequest->reason }}</p>
        @endif
        @if($leaveRequest->cancelled_at)
        <p><strong>Cancelled at:</strong> {{ $leaveRequest->cancelled_at->format('F j, Y H:i') }}</p>
        @endif
        @if($leaveRequest->cancelledByUser)
        <p><strong>Cancelled by:</strong> {{ $leaveRequest->cancelledByUser->name }}</p>
        @endif
        @if($leaveRequest->cancel_reason)
        <p><strong>Cancel reason:</strong> {{ $leaveRequest->cancel_reason }}</p>
        @endif
    </div>
    @if(!empty($bodyHtml))
        {!! $bodyHtml !!}
    @else
        @if($leaveRequest->status === \Modules\Leave\Models\LeaveRequest::STATUS_CANCELLED)
        <p>This is to certify that the above leave was previously <strong>approved</strong> and has since been <strong>cancelled</strong>.</p>
        @else
        <p>This is to certify that the above leave has been <strong>approved</strong>.</p>
        @endif
    @endif
    @if($leaveRequest->approvalSteps->isNotEmpty())
    <div class="approvals">
        <p><strong>Approval chain:</strong></p>
        @foreach($leaveRequest->approvalSteps as $step)
        <div class="approval-row">
            <div>
                <p style="margin:0;">
                    {{ $loop->iteration }}.
                    {{ \Modules\Leave\Models\LeaveApprovalStep::approverTypeOptions()[$step->approver_type] ?? $step->approver_type }}:
                    {{ ucfirst($step->status) }}
                    @if($step->approved_at)
                        ({{ $step->approved_at->format('M j, Y') }})
                    @endif
                    @if($step->approvedByUser)
                        — {{ $step->approvedByUser->name }}
                    @endif
                </p>
            </div>
            @if(!empty($showSignature) && isset($approvalStepSignatures[$step->id]))
            <img src="{{ $approvalStepSignatures[$step->id] }}" alt="Signature" class="approval-sig">
            @endif
        </div>
        @endforeach
    </div>
    @endif
    @if(!empty($showStamp) && !empty($stampImageUrl))
    <img src="{{ $stampImageUrl }}" alt="Company stamp" class="stamp-seal">
    @endif
    <p>Generated on {{ now()->format('F j, Y H:i') }}.</p>
    @if($verificationUrl && $qrImageUrl)
    <div class="footer qr">
        <p>Scan to verify this letter:</p>
        <img src="{{ $qrImageUrl }}" alt="QR Code" width="100" height="100">
        <p style="margin-top:8px;">Or open: <a href="{{ $verificationUrl }}">{{ parse_url($verificationUrl, PHP_URL_HOST) ?? 'Verification link' }}</a></p>
    </div>
    @endif
    @if(!empty($letterFooterText))
    <p class="letter-footer-note">{{ $letterFooterText }}</p>
    @endif
</body>
</html>

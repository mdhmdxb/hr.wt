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
        @if($logoUrl)
        <img src="{{ $logoUrl }}" alt="Logo">
        @endif
        <p style="margin:8px 0 0;font-weight:bold;">{{ $companyName ?? 'Company' }}</p>
    </div>
    <h1>Leave Approval Letter</h1>
    <div class="meta">
        <p><strong>Employee:</strong> {{ $leaveRequest->employee->full_name ?? '—' }}</p>
        <p><strong>Leave type:</strong> {{ $leaveRequest->leaveType->name ?? '—' }}</p>
        <p><strong>Start date:</strong> {{ $leaveRequest->start_date->format('F j, Y') }}</p>
        <p><strong>End date:</strong> {{ $leaveRequest->end_date->format('F j, Y') }}</p>
        <p><strong>Days:</strong> {{ $leaveRequest->days }}</p>
        @if($leaveRequest->reason)
        <p><strong>Reason:</strong> {{ $leaveRequest->reason }}</p>
        @endif
    </div>
    <p>This is to certify that the above leave has been <strong>approved</strong>.</p>
    @if($leaveRequest->approvalSteps->isNotEmpty())
    <div class="approvals">
        <p><strong>Approval chain:</strong></p>
        @foreach($leaveRequest->approvalSteps as $step)
        <p>{{ $loop->iteration }}. {{ \Modules\Leave\Models\LeaveApprovalStep::approverTypeOptions()[$step->approver_type] ?? $step->approver_type }}: {{ ucfirst($step->status) }}@if($step->approved_at) ({{ $step->approved_at->format('M j, Y') }})@endif</p>
        @endforeach
    </div>
    @endif
    <p>Generated on {{ now()->format('F j, Y H:i') }}.</p>
    @if($verificationUrl && $qrImageUrl)
    <div class="footer qr">
        <p>Scan to verify this letter:</p>
        <img src="{{ $qrImageUrl }}" alt="QR Code" width="100" height="100">
        <p style="margin-top:8px;">Or open: {{ $verificationUrl }}</p>
    </div>
    @endif
</body>
</html>

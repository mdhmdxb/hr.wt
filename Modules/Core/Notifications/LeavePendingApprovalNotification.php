<?php

namespace Modules\Core\Notifications;

use Illuminate\Notifications\Notification;
use Modules\Leave\Models\LeaveRequest;

class LeavePendingApprovalNotification extends Notification
{

    public function __construct(
        public LeaveRequest $leaveRequest
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'leave_pending',
            'message' => 'Leave request pending approval: ' . ($this->leaveRequest->employee->full_name ?? 'Employee') . ' – ' . ($this->leaveRequest->leaveType->name ?? '') . ' (' . $this->leaveRequest->start_date->format('M j') . ' – ' . $this->leaveRequest->end_date->format('M j, Y') . ')',
            'url' => route('leave.show', $this->leaveRequest),
            'leave_request_id' => $this->leaveRequest->id,
        ];
    }
}

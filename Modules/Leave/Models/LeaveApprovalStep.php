<?php

namespace Modules\Leave\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\User;

class LeaveApprovalStep extends Model
{
    protected $fillable = [
        'leave_request_id',
        'step_order',
        'approver_type',
        'status',
        'approved_at',
        'approved_by',
        'notes',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public const APPROVER_REPORTING_MANAGER = 'reporting_manager';
    public const APPROVER_HR = 'hr';
    public const APPROVER_ACCOUNTS = 'accounts';
    public const APPROVER_ADMIN = 'admin';

    public static function approverTypeOptions(): array
    {
        return [
            self::APPROVER_REPORTING_MANAGER => 'Reporting Manager',
            self::APPROVER_HR => 'HR',
            self::APPROVER_ACCOUNTS => 'Accounts',
            self::APPROVER_ADMIN => 'Admin',
        ];
    }

    public function leaveRequest(): BelongsTo
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

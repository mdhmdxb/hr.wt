<?php

namespace Modules\Leave\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Employee;
use Modules\Core\Models\User;

class LeaveRequest extends Model
{
    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'days',
        'actual_return_date',
        'overstay_days',
        'status',
        'reason',
        'supporting_document_path',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'cancelled_by',
        'cancelled_at',
        'cancel_reason',
        'verification_token',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'actual_return_date' => 'date',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approvalSteps(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LeaveApprovalStep::class)->orderBy('step_order');
    }

    public function cancelledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /** Whether this request uses hierarchical workflow. */
    public function hasWorkflow(): bool
    {
        $steps = $this->leaveType->getWorkflowStepsNormalized();
        return count($steps) > 0;
    }

    /** Current pending step (first step with status pending). */
    public function currentApprovalStep(): ?LeaveApprovalStep
    {
        return $this->approvalSteps()->where('status', LeaveApprovalStep::STATUS_PENDING)->first();
    }

    /** Check if the given user can approve/reject this request (they are the current approver). */
    public function canBeActedOnBy($user): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }
        $step = $this->currentApprovalStep();
        if ($step) {
            return self::userMatchesApproverType($user, $step->approver_type, $this->employee_id);
        }
        // When no step is configured, Admin, HR, or user with manage_leave privilege can approve/reject.
        return $user->isAdmin() || $user->isHr() || $user->hasPermission('manage_leave');
    }

    /** Check if the given user can cancel this request. */
    public function canBeCancelledBy($user): bool
    {
        // Once cancelled, nobody can cancel again
        if ($this->status === self::STATUS_CANCELLED) {
            return false;
        }

        // Employee (applicant) can cancel while it's still pending and no step was approved yet
        if ($this->status === self::STATUS_PENDING && $user->employee_id && $user->employee_id === $this->employee_id) {
            $hasApprovedStep = $this->approvalSteps()->where('status', LeaveApprovalStep::STATUS_APPROVED)->exists();
            if (! $hasApprovedStep) {
                return true;
            }
        }

        // Admin / HR / Manager / Accounts or manage_leave privilege can cancel pending or approved requests
        if ($user->isAdmin() || $user->isHr() || $user->isManager() || $user->isAccounts() || $user->hasPermission('manage_leave')) {
            return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED], true);
        }

        return false;
    }

    public static function userMatchesApproverType($user, string $approverType, int $employeeId): bool
    {
        $emp = Employee::find($employeeId);
        switch ($approverType) {
            case LeaveApprovalStep::APPROVER_ADMIN:
                return $user->isAdmin();
            case LeaveApprovalStep::APPROVER_HR:
                return $user->isHr() || $user->isAdmin() || $user->hasPermission('manage_leave');
            case LeaveApprovalStep::APPROVER_ACCOUNTS:
                return $user->isAccounts() || $user->isAdmin() || $user->hasPermission('manage_leave');
            case LeaveApprovalStep::APPROVER_OWNER:
                return $user->isOwner() || $user->isAdmin() || $user->hasPermission('manage_leave');
            case LeaveApprovalStep::APPROVER_REPORTING_MANAGER:
                // Reporting manager for this employee, or Admin/HR or manage_leave as override.
                return $user->isAdmin()
                    || $user->isHr()
                    || $user->hasPermission('manage_leave')
                    || ($emp && $emp->reporting_manager_id && $user->employee_id == $emp->reporting_manager_id);
            case LeaveApprovalStep::APPROVER_DEPARTMENT_HEAD:
                if (! $emp || ! $emp->department_id || ! $user->employee_id) {
                    return false;
                }
                $approver = Employee::find($user->employee_id);
                // Department head: same department, and either Management/Manager role; Admin/HR/manage_leave always override.
                if ($user->isAdmin() || $user->isHr() || $user->hasPermission('manage_leave')) {
                    return true;
                }
                if (! $approver || $approver->department_id !== $emp->department_id) {
                    return false;
                }
                return $user->isManagement() || $user->isManager();
            default:
                return false;
        }
    }
}

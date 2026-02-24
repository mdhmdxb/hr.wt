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
        'status',
        'reason',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'verification_token',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

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
        return $user->isAdmin() || $user->isHr();
    }

    public static function userMatchesApproverType($user, string $approverType, int $employeeId): bool
    {
        $emp = Employee::find($employeeId);
        switch ($approverType) {
            case LeaveApprovalStep::APPROVER_ADMIN:
                return $user->isAdmin();
            case LeaveApprovalStep::APPROVER_HR:
                return $user->isHr() || $user->isAdmin();
            case LeaveApprovalStep::APPROVER_ACCOUNTS:
                return $user->isAccounts() || $user->isAdmin();
            case LeaveApprovalStep::APPROVER_REPORTING_MANAGER:
                return $emp && $emp->reporting_manager_id && $user->employee_id == $emp->reporting_manager_id;
            default:
                return false;
        }
    }
}

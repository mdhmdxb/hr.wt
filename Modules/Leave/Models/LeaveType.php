<?php

namespace Modules\Leave\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    protected $fillable = [
        'name',
        'days_per_year',
        'carry_over',
        'color',
        'is_paid',
        'allow_document',
        'require_document',
        'document_label',
        'workflow_steps',
    ];

    protected $casts = [
        'carry_over' => 'boolean',
        'is_paid' => 'boolean',
        'allow_document' => 'boolean',
        'require_document' => 'boolean',
        'workflow_steps' => 'array',
    ];

    /** Default workflow: single HR approval (legacy). */
    public static function defaultWorkflowSteps(): array
    {
        return [['order' => 1, 'approver' => 'hr']];
    }

    /** Get workflow steps for this type (array of ['order' => 1, 'approver' => 'hr']). */
    public function getWorkflowStepsNormalized(): array
    {
        $steps = $this->workflow_steps;
        if (empty($steps) || ! is_array($steps)) {
            return self::defaultWorkflowSteps();
        }
        usort($steps, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
        return array_values($steps);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
}

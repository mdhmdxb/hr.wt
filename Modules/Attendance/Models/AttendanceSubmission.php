<?php

namespace Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Employee;

class AttendanceSubmission extends Model
{
    protected $fillable = ['employee_id', 'year', 'month', 'submitted_at', 'allow_edit'];

    protected $casts = [
        'submitted_at' => 'datetime',
        'allow_edit' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /** Check if the employee can edit this month (not submitted, or HR allowed edit). */
    public function canEmployeeEdit(): bool
    {
        if ($this->submitted_at === null) {
            return true;
        }
        return $this->allow_edit === true;
    }

    public static function isLocked(int $employeeId, int $year, int $month): bool
    {
        $sub = self::where('employee_id', $employeeId)->where('year', $year)->where('month', $month)->first();
        if (! $sub) {
            return false;
        }
        return $sub->submitted_at !== null && ! $sub->allow_edit;
    }
}

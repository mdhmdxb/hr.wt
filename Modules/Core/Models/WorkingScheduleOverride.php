<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkingScheduleOverride extends Model
{
    protected $fillable = [
        'name', 'start_date', 'end_date', 'work_start', 'work_end',
        'branch_id', 'site_id', 'project_id', 'employee_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /** Check if a given date falls within this override period. */
    public function coversDate(\Carbon\Carbon $date): bool
    {
        return $date->between($this->start_date->startOfDay(), $this->end_date->endOfDay());
    }

    /** Check if this override applies to the given employee (by scope). */
    public function appliesToEmployee(?Employee $employee): bool
    {
        if ($employee === null) {
            return $this->employee_id === null && $this->project_id === null && $this->site_id === null && $this->branch_id === null;
        }
        if ($this->employee_id !== null) {
            return $this->employee_id === $employee->id;
        }
        if ($this->project_id !== null) {
            return $employee->projects()->where('projects.id', $this->project_id)->exists();
        }
        if ($this->site_id !== null) {
            return $employee->site_id === $this->site_id;
        }
        if ($this->branch_id !== null) {
            return $employee->branch_id === $this->branch_id;
        }
        return true; // global
    }

    /** Get the active override for a date (global only). */
    public static function forDate(\Carbon\Carbon $date): ?self
    {
        return self::query()
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->whereNull('branch_id')->whereNull('site_id')->whereNull('project_id')->whereNull('employee_id')
            ->first();
    }

    /** Get the best matching override for an employee on a date (most specific: employee > project > site > branch > global). */
    public static function forEmployeeOnDate(Employee $employee, \Carbon\Carbon $date): ?self
    {
        $candidates = self::query()
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->get();

        $best = null;
        $bestScore = -1;
        foreach ($candidates as $ov) {
            if (! $ov->appliesToEmployee($employee)) {
                continue;
            }
            $score = $ov->employee_id ? 5 : ($ov->project_id ? 4 : ($ov->site_id ? 3 : ($ov->branch_id ? 2 : 1)));
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $ov;
            }
        }
        return $best;
    }
}

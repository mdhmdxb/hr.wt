<?php

namespace Modules\Attendance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Employee;

class Attendance extends Model
{
    public const STATUS_PRESENT = 'present';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_HALF_DAY = 'half_day';
    public const STATUS_LEAVE = 'leave';
    public const STATUS_HOLIDAY = 'holiday';
    public const STATUS_WEEKLY_OFF = 'weekly_off';
    public const STATUS_ALT_SATURDAY_OFF = 'alt_saturday_off';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PRESENT => 'Present',
            self::STATUS_ABSENT => 'Absent',
            self::STATUS_HALF_DAY => 'Half day',
            self::STATUS_LEAVE => 'Leave',
            self::STATUS_HOLIDAY => 'Holiday',
            self::STATUS_WEEKLY_OFF => 'Weekly off',
            self::STATUS_ALT_SATURDAY_OFF => 'Alt. Saturday off',
        ];
    }

    public static function validStatuses(): array
    {
        return array_keys(self::statusOptions());
    }

    protected $fillable = [
        'employee_id',
        'date',
        'check_in_at',
        'check_out_at',
        'status',
        'notes',
        'attachment_path',
        'overtime_minutes',
        'locked_at',
    ];

    protected $casts = [
        'date' => 'date',
        'locked_at' => 'datetime',
    ];

    public function isLocked(): bool
    {
        return $this->locked_at !== null;
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getTotalHoursAttribute(): ?float
    {
        if ($this->check_in_at && $this->check_out_at) {
            $in = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->check_in_at);
            $out = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->check_out_at);
            return round($in->diffInMinutes($out) / 60, 2);
        }
        return null;
    }

    public function getOvertimeHoursAttribute(): ?float
    {
        return $this->overtime_minutes !== null ? round($this->overtime_minutes / 60, 2) : null;
    }
}

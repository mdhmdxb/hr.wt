<?php

namespace Modules\Core\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Leave\Models\LeaveRequest;

class Employee extends Model
{
    protected $fillable = [
        'employee_code',
        'first_name',
        'last_name',
        'email',
        'photo_path',
        'phone',
        'nationality',
        'gender',
        'religion',
        'permanent_address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'branch_id',
        'site_id',
        'department_id',
        'approval_level',
        'designation_id',
        'reporting_manager_id',
        'hire_date',
        'date_of_birth',
        'employment_type',
        'basic_salary',
        'accommodation',
        'transportation',
        'food_allowance',
        'other_allowances',
        'status',
        'weekly_off_days',
        'alternate_saturday_weeks',
        'shift_start',
        'shift_end',
        'break_minutes',
        'remaining_leave',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'date_of_birth' => 'date',
        'basic_salary' => 'decimal:2',
        'accommodation' => 'decimal:2',
        'transportation' => 'decimal:2',
        'food_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'remaining_leave' => 'decimal:2',
    ];

    /** Common religions for dropdown. */
    public static function religionOptions(): array
    {
        return [
            '' => '— Select —',
            'Islam' => 'Islam',
            'Christianity' => 'Christianity',
            'Hinduism' => 'Hinduism',
            'Buddhism' => 'Buddhism',
            'Sikhism' => 'Sikhism',
            'Judaism' => 'Judaism',
            'Other' => 'Other',
        ];
    }

    /** Weekday names as used in weekly_off_days (lowercase). */
    public static function weekdayKeys(): array
    {
        return ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    }

    /** Get list of weekly off weekdays (e.g. ['saturday', 'sunday']). */
    public function getWeeklyOffDaysList(): array
    {
        if (empty($this->weekly_off_days)) {
            return [];
        }
        $raw = array_map('trim', explode(',', strtolower($this->weekly_off_days)));
        $valid = array_intersect($raw, self::weekdayKeys());
        return array_values($valid);
    }

    /** Check if the given date falls on this employee's weekly off. */
    public function isWeeklyOffDay(\Carbon\Carbon $date): bool
    {
        $off = $this->getWeeklyOffDaysList();
        if (empty($off)) {
            return false;
        }
        $dayName = strtolower($date->format('l'));
        return in_array($dayName, $off, true);
    }

    /** Check if the given date is an alternate Saturday off (weeks chosen by employee: 1st–5th week of month). */
    public function isAlternateSaturdayOffDay(\Carbon\Carbon $date): bool
    {
        if (empty($this->alternate_saturday_weeks) || $date->dayOfWeek !== Carbon::SATURDAY) {
            return false;
        }
        $weeks = array_map('intval', array_filter(explode(',', $this->alternate_saturday_weeks)));
        if (empty($weeks)) {
            return false;
        }
        $weekOfMonth = (int) ceil($date->day / 7);
        return in_array($weekOfMonth, $weeks, true);
    }

    /** Options for alternate Saturday weeks: 1–5 (week of month). */
    public static function alternateSaturdayWeekOptions(): array
    {
        return [1 => '1st Saturday', 2 => '2nd Saturday', 3 => '3rd Saturday', 4 => '4th Saturday', 5 => '5th Saturday'];
    }

    /** Get list of selected alternate Saturday week numbers (e.g. [1, 3, 5]). */
    public function getAlternateSaturdayWeeksList(): array
    {
        if (empty($this->alternate_saturday_weeks)) {
            return [];
        }
        return array_map('intval', array_filter(explode(',', $this->alternate_saturday_weeks)));
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function reportingManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reporting_manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'reporting_manager_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function assetAssignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function projects(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'employee_project')->withPivot('role')->withTimestamps();
    }

    public function salaryRevisions(): HasMany
    {
        return $this->hasMany(EmployeeSalaryRevision::class)->orderByDesc('effective_from');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Approximate UAE/MOHRE annual leave entitlement (days) as of a given date.
     *  - First 6 months: 0 days
     *  - 6–12 months: 2 days per completed month after 6 months
     *  - After 12 months: 30 days per completed year of service
     */
    public function uaeAnnualLeaveEntitlement(?Carbon $asOf = null): int
    {
        if (! $this->hire_date) {
            return 0;
        }
        $asOf = $asOf ?: Carbon::today();
        if ($asOf->lt($this->hire_date)) {
            return 0;
        }
        $months = $this->hire_date->diffInMonths($asOf);
        if ($months <= 6) {
            return 0;
        }
        if ($months <= 12) {
            return max(0, ($months - 6) * 2);
        }
        $years = intdiv($months, 12);
        return $years * 30;
    }

    /**
     * Annual leave days taken (approved) up to a given date, counting only
     * leave types named "Annual Leave" (case-insensitive).
     */
    public function uaeAnnualLeaveTaken(?Carbon $asOf = null): int
    {
        $asOf = $asOf ?: Carbon::today();
        $total = LeaveRequest::where('employee_id', $this->id)
            ->where('status', LeaveRequest::STATUS_APPROVED)
            ->whereDate('end_date', '<=', $asOf->toDateString())
            ->whereHas('leaveType', function ($q) {
                $q->whereRaw('LOWER(name) = ?', ['annual leave']);
            })
            ->sum('days');
        return (int) $total;
    }

    /**
     * Remaining annual leave under UAE/MOHRE rules (entitlement - taken).
     */
    public function uaeAnnualLeaveRemaining(?Carbon $asOf = null): int
    {
        $asOf = $asOf ?: Carbon::today();
        $entitled = $this->uaeAnnualLeaveEntitlement($asOf);
        $taken = $this->uaeAnnualLeaveTaken($asOf);
        return max(0, $entitled - $taken);
    }
}

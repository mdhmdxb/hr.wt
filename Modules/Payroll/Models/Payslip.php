<?php

namespace Modules\Payroll\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Employee;

class Payslip extends Model
{
    protected $fillable = [
        'payroll_run_id',
        'employee_id',
        'basic_salary',
        'accommodation',
        'transportation',
        'food_allowance',
        'other_allowances',
        'bonus',
        'allowances',
        'days_worked',
        'days_off',
        'holiday',
        'annual_leave',
        'unpaid_leave',
        'overtime_hours',
        'overtime_premium',
        'overtime_bonus_transport_food',
        'salary_adjustment',
        'deductions',
        'net_pay',
        'total_wps_salary',
        'notes',
        'remarks',
        'verification_token',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'accommodation' => 'decimal:2',
        'transportation' => 'decimal:2',
        'food_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'bonus' => 'decimal:2',
        'allowances' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_premium' => 'decimal:2',
        'overtime_bonus_transport_food' => 'decimal:2',
        'salary_adjustment' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'total_wps_salary' => 'decimal:2',
    ];

    /** Total of all allowance components (for display/reports). */
    public function getTotalAllowancesBreakdownAttribute(): float
    {
        return (float) (
            ($this->accommodation ?? 0) + ($this->transportation ?? 0) + ($this->food_allowance ?? 0)
            + ($this->other_allowances ?? 0) + ($this->bonus ?? 0)
            + ($this->overtime_premium ?? 0) + ($this->overtime_bonus_transport_food ?? 0)
        );
    }

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}

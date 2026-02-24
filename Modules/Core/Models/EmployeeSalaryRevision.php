<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalaryRevision extends Model
{
    protected $fillable = [
        'employee_id', 'effective_from',
        'basic_salary', 'accommodation', 'transportation', 'food_allowance', 'other_allowances',
        'notes', 'changed_by',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'basic_salary' => 'decimal:2',
        'accommodation' => 'decimal:2',
        'transportation' => 'decimal:2',
        'food_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}

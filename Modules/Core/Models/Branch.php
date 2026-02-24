<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'address',
        'default_shift_start',
        'default_shift_end',
        'default_accommodation',
        'default_transportation',
        'default_food_allowance',
        'default_other_allowances',
    ];

    protected $casts = [
        'default_accommodation' => 'decimal:2',
        'default_transportation' => 'decimal:2',
        'default_food_allowance' => 'decimal:2',
        'default_other_allowances' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'branch_id');
    }
}

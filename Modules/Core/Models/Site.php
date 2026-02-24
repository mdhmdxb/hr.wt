<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $fillable = [
        'branch_id',
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

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'site_id');
    }
}

<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'address',
        'phone',
        'email',
    ];

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }
}

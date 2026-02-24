<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOpening extends Model
{
    protected $fillable = ['title', 'department_id', 'status', 'closed_at', 'description', 'requirements'];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(JobCandidate::class, 'job_opening_id');
    }
}

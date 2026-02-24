<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetAssignment extends Model
{
    protected $fillable = [
        'asset_id', 'employee_id', 'assigned_at', 'returned_at',
        'condition', 'notes', 'assigned_by', 'returned_to',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function returnedToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_to');
    }

    public function isActive(): bool
    {
        return $this->returned_at === null;
    }
}

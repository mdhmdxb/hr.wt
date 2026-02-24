<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_RETIRED = 'retired';
    public const STATUS_MAINTENANCE = 'maintenance';

    protected $fillable = [
        'asset_type_id',
        'name',
        'identifier',
        'status',
        'notes',
        'issue_date',
        'expiry_date',
        'meta',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'meta' => 'array',
    ];

    public function assetType(): BelongsTo
    {
        return $this->belongsTo(AssetType::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function currentAssignment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AssetAssignment::class)->whereNull('returned_at')->latestOfMany('assigned_at');
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_ASSIGNED => 'Assigned',
            self::STATUS_RETIRED => 'Retired',
            self::STATUS_MAINTENANCE => 'Maintenance',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /** Expiring within the given number of days (default 30). */
    public function isExpiringSoon(int $days = 30): bool
    {
        if (! $this->expiry_date) {
            return false;
        }
        return $this->expiry_date->isFuture() && $this->expiry_date->lte(now()->addDays($days));
    }

    /** Type-specific field labels (e.g. vehicle: plate_number, registration_number). */
    public static function metaFieldLabels(string $typeSlug): array
    {
        return match (strtolower($typeSlug)) {
            'vehicle' => [
                'plate_number' => 'Plate number',
                'registration_number' => 'Registration number',
                'chassis' => 'Chassis',
            ],
            'laptop', 'computer' => [
                'serial_number' => 'Serial number',
                'warranty_until' => 'Warranty until',
            ],
            default => [],
        };
    }
}

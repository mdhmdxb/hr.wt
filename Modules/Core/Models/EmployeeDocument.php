<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDocument extends Model
{
    protected $fillable = [
        'employee_id',
        'renewal_of_id',
        'type',
        'title',
        'file_path',
        'issue_date',
        'expiry_date',
        'version',
        'notes',
        'status',
        'uploaded_by',
        'uploaded_at',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'uploaded_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public const TYPE_PASSPORT = 'passport';
    public const TYPE_VISA = 'visa';
    public const TYPE_RESIDENCY = 'residency';
    public const TYPE_CONTRACT = 'contract';
    public const TYPE_CERTIFICATE = 'certificate';
    public const TYPE_INSURANCE = 'insurance';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';

    public static function typeOptions(): array
    {
        return [
            self::TYPE_PASSPORT => 'Passport',
            self::TYPE_VISA => 'Visa',
            self::TYPE_RESIDENCY => 'Residency',
            self::TYPE_CONTRACT => 'Contract',
            self::TYPE_CERTIFICATE => 'Certificate',
            self::TYPE_INSURANCE => 'Insurance',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function renewalOf(): BelongsTo
    {
        return $this->belongsTo(EmployeeDocument::class, 'renewal_of_id');
    }

    public function renewals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EmployeeDocument::class, 'renewal_of_id');
    }

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast() === false && $this->expiry_date->diffInDays(now(), false) <= $days;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}

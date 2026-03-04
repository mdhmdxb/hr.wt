<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_OWNER = 'owner';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MANAGEMENT = 'management';
    public const ROLE_HR = 'hr';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_ACCOUNTS = 'accounts';
    public const ROLE_EMPLOYEE = 'employee';

    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'password',
        'role',
        'permissions',
        'two_factor_enabled',
        'signature_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_enabled' => 'boolean',
        'permissions' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    /** True for full system admin (owner-level access). Includes Management role. */
    public function isAdmin(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_MANAGEMENT], true);
    }

    public function isManagement(): bool
    {
        return $this->role === self::ROLE_MANAGEMENT;
    }

    public function isHr(): bool
    {
        return $this->role === self::ROLE_HR;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isAccounts(): bool
    {
        return $this->role === self::ROLE_ACCOUNTS;
    }

    public function isEmployee(): bool
    {
        return $this->role === self::ROLE_EMPLOYEE;
    }

    /** Role is for access control only; do not expose to employee-facing views. */
    public function canSeeRole(): bool
    {
        return ! $this->isEmployee();
    }

    /** All assignable privilege keys and labels for the Users & roles UI. */
    public static function availablePermissions(): array
    {
        return [
            'manage_employees' => 'Manage employees',
            'manage_documents' => 'Manage documents',
            'manage_leave' => 'Manage leave & approve requests',
            'manage_attendance' => 'Manage attendance',
            'manage_payroll' => 'Access payroll',
            'manage_reports' => 'Access reports',
            'manage_organization' => 'Manage branches, sites, projects',
            'manage_templates' => 'Manage templates',
            'manage_settings' => 'Manage settings',
        ];
    }

    /** Check if user has a specific privilege (from permissions array or role defaults). */
    public function hasPermission(string $key): bool
    {
        $permissions = $this->permissions ?? [];
        if (is_array($permissions) && in_array($key, $permissions, true)) {
            return true;
        }
        return false;
    }
}

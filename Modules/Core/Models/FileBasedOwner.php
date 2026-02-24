<?php

namespace Modules\Core\Models;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Virtual owner for file-based auth. No database record.
 * Used when owner logs in via encrypted credentials file.
 */
class FileBasedOwner implements Authenticatable
{
    /** Fake relation so $user->activityLogs()->exists() / ->latest()->take(5)->get() work. */
    public function activityLogs(): OwnerActivityLogRelation
    {
        return new OwnerActivityLogRelation();
    }

    /** Fake relation so $user->unreadNotifications()->take(5)->get() / ->count() work. */
    public function unreadNotifications(): OwnerEmptyRelation
    {
        return new OwnerEmptyRelation();
    }

    /** Fake relation so $user->notifications()->paginate() work. */
    public function notifications(): OwnerEmptyRelation
    {
        return new OwnerEmptyRelation();
    }

    public const AUTH_ID = 'file-owner';

    public function __construct(
        public string $email,
        public string $name,
    ) {}

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): string
    {
        return self::AUTH_ID;
    }

    public function getAuthPassword(): string
    {
        return '';
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void
    {
    }

    public function getRememberTokenName(): ?string
    {
        return null;
    }

    public function isOwner(): bool
    {
        return true;
    }

    public function isAdmin(): bool
    {
        return false;
    }

    public function isHr(): bool
    {
        return false;
    }

    public function isManager(): bool
    {
        return false;
    }

    public function isAccounts(): bool
    {
        return false;
    }

    public function isEmployee(): bool
    {
        return false;
    }

    public function canSeeRole(): bool
    {
        return true;
    }

    /** For compatibility with User attribute access (e.g. $user->employee_id). */
    public function __get(string $key): mixed
    {
        return $this->getAttribute($key);
    }

    public function getAttribute(string $key): mixed
    {
        if ($key === 'employee_id') {
            return null;
        }
        if ($key === 'role') {
            return 'owner';
        }
        return match ($key) {
            'name' => $this->name,
            'email' => $this->email,
            'id' => self::AUTH_ID,
            'unreadNotifications' => $this->unreadNotifications(),
            'notifications' => $this->notifications(),
            default => null,
        };
    }
}

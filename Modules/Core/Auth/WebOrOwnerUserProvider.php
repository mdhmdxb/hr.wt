<?php

namespace Modules\Core\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Support\Arrayable;
use Modules\Core\Models\FileBasedOwner;
use Modules\Core\Models\User;

/**
 * Provider for the web guard: returns FileBasedOwner when id is file-owner, else User from DB.
 */
class WebOrOwnerUserProvider extends EloquentUserProvider implements UserProvider
{
    public function retrieveById($identifier): ?Authenticatable
    {
        if ($identifier === FileBasedOwner::AUTH_ID) {
            $email = session('owner_email');
            $name = session('owner_name');
            if ($email && $name) {
                return new FileBasedOwner($email, $name);
            }
            return null;
        }

        return User::find($identifier);
    }

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        // Only used for DB users; owner is logged in via LoginController directly
        return parent::retrieveByCredentials($credentials);
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        if ($user instanceof FileBasedOwner) {
            return false; // should not be used for owner
        }
        return parent::validateCredentials($user, $credentials);
    }
}

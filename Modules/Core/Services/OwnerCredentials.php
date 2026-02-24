<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Encrypted file-based owner credentials. No owner record in the database.
 * File: storage/app/owner.enc (add to .gitignore).
 */
class OwnerCredentials
{
    public const FILE = 'owner.enc';

    /**
     * Get decrypted owner data or null if not set.
     * Returns: ['email' => string, 'password_hash' => string, 'name' => string]
     */
    public static function get(): ?array
    {
        if (! Storage::disk('local')->exists(self::FILE)) {
            return null;
        }
        try {
            $raw = Storage::disk('local')->get(self::FILE);
            return Crypt::decrypt($raw);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Check if the given email is the owner email (without revealing to caller).
     */
    public static function isOwnerEmail(string $email): bool
    {
        $data = self::get();
        return $data && isset($data['email']) && strcasecmp($data['email'], $email) === 0;
    }

    /**
     * Verify owner password. Call only after isOwnerEmail() to avoid timing leaks.
     */
    public static function verify(string $email, string $password): bool
    {
        $data = self::get();
        if (! $data || ! isset($data['email'], $data['password_hash']) || strcasecmp($data['email'], $email) !== 0) {
            return false;
        }
        return Hash::check($password, $data['password_hash']);
    }

    /**
     * Store owner credentials (encrypted). Overwrites existing.
     */
    public static function set(string $email, string $password, string $name): void
    {
        $payload = [
            'email' => $email,
            'password_hash' => Hash::make($password),
            'name' => $name,
        ];
        Storage::disk('local')->put(self::FILE, Crypt::encrypt($payload));
    }

    /**
     * Remove owner credentials file.
     */
    public static function remove(): bool
    {
        if (Storage::disk('local')->exists(self::FILE)) {
            return Storage::disk('local')->delete(self::FILE);
        }
        return true;
    }
}

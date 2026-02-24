<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\OwnerCredentials;

class SetOwnerCredentials extends Command
{
    protected $signature = 'owner:set
                            {email : Owner login email}
                            {password : Owner password}
                            {--name= : Display name (default: Owner)}';

    protected $description = 'Store owner credentials in an encrypted file (no DB). Run once after install.';

    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->option('name') ?: 'Owner';

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address.');
            return self::FAILURE;
        }

        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');
            return self::FAILURE;
        }

        OwnerCredentials::set($email, $password, $name);

        $this->info('Owner credentials saved to encrypted file: storage/app/' . OwnerCredentials::FILE);
        $this->comment('No owner record is stored in the database. Add storage/app/' . OwnerCredentials::FILE . ' to .gitignore if needed.');

        return self::SUCCESS;
    }
}

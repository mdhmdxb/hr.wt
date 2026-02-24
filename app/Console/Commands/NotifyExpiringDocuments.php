<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Models\EmployeeDocument;
use Modules\Core\Models\User;
use Modules\Core\Notifications\DocumentExpiringSoonNotification;

class NotifyExpiringDocuments extends Command
{
    protected $signature = 'documents:notify-expiring {--days=30 : Days ahead to check}';

    protected $description = 'Notify HR and Admin users about documents expiring within the given days.';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->addDays($days);

        $expiring = EmployeeDocument::with('employee')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', $cutoff)
            ->orderBy('expiry_date')
            ->get();

        if ($expiring->isEmpty()) {
            $this->info('No documents expiring in the next ' . $days . ' days.');
            return self::SUCCESS;
        }

        $recipients = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_HR])->get();

        foreach ($recipients as $user) {
            $user->notify(new DocumentExpiringSoonNotification($expiring));
        }

        $this->info('Notified ' . $recipients->count() . ' user(s) about ' . $expiring->count() . ' expiring document(s).');
        return self::SUCCESS;
    }
}

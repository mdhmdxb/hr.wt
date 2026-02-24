<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Attendance\Models\Attendance;
use Modules\Core\Models\Employee;
use Modules\Core\Models\User;
use Modules\Core\Notifications\AttendanceReminderNotification;

class AttendanceReminder extends Command
{
    protected $signature = 'attendance:remind {--date= : Date (Y-m-d), default today}';

    protected $description = 'Notify HR/Admin about employees with no attendance recorded for the given date.';

    public function handle(): int
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();
        $dateStr = $date->format('Y-m-d');

        $recordedIds = Attendance::whereDate('date', $dateStr)->pluck('employee_id');
        $missing = Employee::where('status', 'active')->whereNotIn('id', $recordedIds)->get();

        if ($missing->isEmpty()) {
            $this->info('All active employees have attendance recorded for ' . $dateStr . '.');
            return self::SUCCESS;
        }

        $recipients = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_HR])->get();
        foreach ($recipients as $user) {
            $user->notify(new AttendanceReminderNotification($missing->count(), $dateStr));
        }

        $this->info('Notified ' . $recipients->count() . ' user(s): ' . $missing->count() . ' employee(s) with no attendance for ' . $dateStr . '.');
        return self::SUCCESS;
    }
}

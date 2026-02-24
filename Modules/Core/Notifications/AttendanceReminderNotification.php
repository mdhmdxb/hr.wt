<?php

namespace Modules\Core\Notifications;

use Illuminate\Notifications\Notification;

class AttendanceReminderNotification extends Notification
{
    public function __construct(
        public int $count,
        public string $date
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'attendance_reminder',
            'message' => $this->count . ' employee(s) have no attendance recorded for ' . $this->date . '. Consider recording or following up.',
            'url' => route('attendance.index'),
        ];
    }
}

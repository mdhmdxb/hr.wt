<?php

namespace Modules\Core\Notifications;

use Illuminate\Notifications\Notification;
use Modules\Core\Models\EmployeeDocument;

class DocumentExpiringSoonNotification extends Notification
{
    /** @param \Illuminate\Support\Collection<int, EmployeeDocument>|EmployeeDocument $documents */
    public function __construct(
        public \Illuminate\Support\Collection|EmployeeDocument $documents
    ) {
        if ($documents instanceof EmployeeDocument) {
            $this->documents = collect([$documents]);
        }
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $count = $this->documents->count();
        $first = $this->documents->first();
        if ($count === 1 && $first) {
            return [
                'type' => 'document_expiring',
                'message' => 'Document expiring soon: ' . ($first->employee->full_name ?? '') . ' – ' . (EmployeeDocument::typeOptions()[$first->type] ?? $first->type) . ' (' . $first->expiry_date?->format('Y-m-d') . ')',
                'url' => route('documents.show', $first),
                'document_id' => $first->id,
            ];
        }
        return [
            'type' => 'document_expiring_digest',
            'message' => $count . ' document(s) expiring in the next 30 days. Review the document vault.',
            'url' => route('documents.index', ['expiring' => 1]),
            'count' => $count,
        ];
    }
}

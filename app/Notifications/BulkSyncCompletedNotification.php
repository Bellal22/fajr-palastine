<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BulkSyncCompletedNotification extends Notification
{
    use Queueable;

    protected $adminName;
    protected $count;

    public function __construct(string $adminName, int $count)
    {
        $this->adminName = $adminName;
        $this->count = $count;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'bulk_sync_completed',
            'title' => 'مزامنة أفراد',
            'message' => "قام المسؤول {$this->adminName} بعمل مزامنة لعدد {$this->count} فرد.",
        ];
    }
}

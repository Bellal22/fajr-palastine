<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DelegateUpdatedNotification extends Notification
{
    use Queueable;

    protected $actorName;
    protected $count;
    protected $blockName;

    public function __construct(string $actorName, int $count, string $blockName)
    {
        $this->actorName = $actorName;
        $this->count = $count;
        $this->blockName = $blockName;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $label = $this->count === 1 ? 'فرد واحد' : "{$this->count} فرد";
        return [
            'type' => 'delegate_updated',
            'title' => 'تحديث المندوب',
            'message' => "قام المسؤول {$this->actorName} بتحديث المندوب إلى \"{$this->blockName}\" لعدد {$label}.",
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\NeedRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NeedRequestUpdatedNotification extends Notification
{
    use Queueable;

    protected $needRequest;
    protected $action;

    public function __construct(NeedRequest $needRequest, $action = 'update')
    {
        $this->needRequest = $needRequest;
        $this->action = $action;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $verb = $this->action == 'store' ? 'بإضافة' : 'بتحديث';
        $supervisor = $this->needRequest->supervisor->name ?? 'مسؤول المنطقة';
        
        return [
            'type' => 'need_request_updated',
            'title' => 'تحديث طلب احتياج',
            'message' => "قام {$supervisor} {$verb} طلب احتياج جديد.",
            'id' => $this->needRequest->id,
        ];
    }
}

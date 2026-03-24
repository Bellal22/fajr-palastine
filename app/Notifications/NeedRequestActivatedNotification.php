<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NeedRequestActivatedNotification extends Notification
{
    use Queueable;

    protected $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'need_request_activated',
            'title' => 'تفعيل طلب احتياج',
            'message' => "تم تفعيل طلبات الاحتياج لمشروع ({$this->project->name}). يمكنك البدء في رفع الترشيحات الآن.",
            'id' => $this->project->id,
        ];
    }
}

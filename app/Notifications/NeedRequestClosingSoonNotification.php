<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NeedRequestClosingSoonNotification extends Notification
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
        $deadline = optional($this->project->needRequestProject)->deadline;
        
        return [
            'type' => 'need_request_closing',
            'title' => 'إغلاق طلب احتياج قريباً',
            'message' => "تنبيه: طلب الاحتياج لـ ({$this->project->name}) سيغلق قريباً. يرجى استكمال الترشيحات قبل الموعد.",
            'id' => $this->project->id,
            'deadline' => $deadline ? $deadline->format('Y-m-d H:i') : null,
        ];
    }
}

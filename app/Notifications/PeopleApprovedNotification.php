<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\AreaResponsible;
use App\Models\Block;

class PeopleApprovedNotification extends Notification
{
    use Queueable;

    protected $areaResponsible;
    protected $count;
    protected $block;

    public function __construct(AreaResponsible $areaResponsible, int $count, Block $block = null)
    {
        $this->areaResponsible = $areaResponsible;
        $this->count = $count;
        $this->block = $block;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $message = "قام مسؤول المنطقة {$this->areaResponsible->name} باعتماد {$this->count} أفراد";
        if ($this->block) {
            $message .= " وتخصيصهم للمندوب {$this->block->name}.";
        } else {
            $message .= ".";
        }

        return [
            'type' => 'people_approved',
            'title' => 'اعتماد أفراد',
            'message' => $message,
            'url' => route('dashboard.people.index', [
                'area_responsible_id' => $this->areaResponsible->id,
                'block_id' => $this->block ? $this->block->id : null
            ]),
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PersonUpdatedNotification extends Notification
{
    use Queueable;

    protected $person;

    public function __construct(Person $person)
    {
        $this->person = $person;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'person_updated',
            'title' => 'تحديث بيانات فرد',
            'message' => "قام الفرد {$this->person->name} بتحديث بياناته الشخصية.",
            'id' => $this->person->id,
        ];
    }
}

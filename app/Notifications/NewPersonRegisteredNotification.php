<?php

namespace App\Notifications;

use App\Models\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewPersonRegisteredNotification extends Notification
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
            'type' => 'new_person_registered',
            'title' => 'تسجيل فرد جديد',
            'message' => "تم تسجيل فرد جديد ({$this->person->name}) في منطقتك.",
            'id' => $this->person->id,
            'area' => $this->person->area_responsible_id,
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PersonUpdatedNotification extends Notification
{
    use Queueable;

    protected $person;
    protected $changesText;

    public function __construct(Person $person, $changesText = null)
    {
        $this->person = $person;
        $this->changesText = $changesText;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $message = "قام الفرد {$this->person->name} بتحديث بياناته الشخصية.";
        if ($this->changesText) {
            $message .= " الحقول المعدلة: " . $this->changesText;
        }

        return [
            'type' => 'person_updated',
            'title' => 'تحديث بيانات فرد',
            'message' => $message,
            'id' => $this->person->id,
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\KitchenComplaint;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewKitchenComplaintNotification extends Notification
{
    use Queueable;

    protected $complaint;

    public function __construct(KitchenComplaint $complaint)
    {
        $this->complaint = $complaint;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_kitchen_complaint',
            'title' => 'شكوى مطبخ جديدة',
            'message' => "شكوى مطبخ جديدة من مخيم ({$this->complaint->camp_name}).",
            'id' => $this->complaint->id,
            'camp_name' => $this->complaint->camp_name,
        ];
    }
}

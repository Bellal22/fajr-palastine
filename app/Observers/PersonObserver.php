<?php

namespace App\Observers;

use App\Models\Person;

class PersonObserver
{
    /**
     * Handle the Person "created" event.
     */
    public function created(Person $person): void
    {
        Person::clearAllCache();

        // Notify Area Responsible if exists
        if ($person->area_responsible_id) {
            $supervisor = \App\Models\User::where('id', $person->area_responsible_id)
                ->where('type', \App\Models\User::SUPERVISOR_TYPE)
                ->first();
            if ($supervisor) {
                $supervisor->notify(new \App\Notifications\NewPersonRegisteredNotification($person));
            }
        }
    }

    /**
     * Handle the Person "updated" event.
     */
    public function updated(Person $person): void
    {
        Person::clearAllCache();

        $changes = $person->getChanges();
        unset($changes['updated_at']);
        
        // منع إرسال إشعار إذا كان التعديل فقط لحقول المزامنة (لتجنب الإزعاج أثناء مزامنة المشرفين)
        $syncFieldsOnly = count(array_diff(array_keys($changes), ['api_synced_at', 'api_sync_status', 'api_sync_error'])) === 0;
        if ($syncFieldsOnly) {
            return;
        }

        $changesText = null;
        if (!empty($changes)) {
            $translatedFields = [];
            foreach (array_keys($changes) as $field) {
                // If translation exists in people.attributes
                $transKey = "people.attributes.$field";
                if (\Illuminate\Support\Facades\Lang::has($transKey)) {
                    $translatedFields[] = trans($transKey);
                }
            }
            if (!empty($translatedFields)) {
                $changesText = implode('، ', $translatedFields);
            }
        }

        // Notify Admins
        $admins = \App\Models\User::where('type', \App\Models\User::ADMIN_TYPE)->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\PersonUpdatedNotification($person, $changesText));
    }

    /**
     * Handle the Person "deleted" event.
     */
    public function deleted(Person $person): void
    {
        Person::clearAllCache();
    }

    /**
     * Handle the Person "restored" event.
     */
    public function restored(Person $person): void
    {
        Person::clearAllCache();
    }

    /**
     * Handle the Person "force deleted" event.
     */
    public function forceDeleted(Person $person): void
    {
        Person::clearAllCache();
    }
}
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

        // Notify Admins
        $admins = \App\Models\User::where('type', \App\Models\User::ADMIN_TYPE)->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\PersonUpdatedNotification($person));
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
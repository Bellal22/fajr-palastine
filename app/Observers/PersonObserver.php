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
    }

    /**
     * Handle the Person "updated" event.
     */
    public function updated(Person $person): void
    {
        Person::clearAllCache();
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
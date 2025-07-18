<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Person;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class PersonPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any people.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.people')
            || $user->isSupervisor();
    }

    /**
     * Determine whether the user can view the person.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Person $person
     * @return mixed
     */
    public function view(User $user, Person $person)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.people')
            || ($user->isSupervisor() && $person->area_responsible_id == $user->id);
    }

    /**
     * Determine whether the user can create people.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.people');
    }

    /**
     * Determine whether the user can update the person.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Person $person
     * @return mixed
     */
    public function update(User $user, Person $person)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.people')
            || ($user->isSupervisor() && $person->area_responsible_id == $user->id))
            && ! $this->trashed($person);
    }

    /**
     * Determine whether the user can delete the person.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Person $person
     * @return mixed
     */
    public function delete(User $user, Person $person)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.people')
            || ($user->isSupervisor() && $person->area_responsible_id == $user->id))
            && ! $this->trashed($person);
    }

    /**
     * Determine whether the user can view trashed people.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.people'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed person.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Person $person
     * @return mixed
     */
    public function viewTrash(User $user, Person $person)
    {
        return $this->view($user, $person)
            && $person->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Person $person
     * @return mixed
     */
    public function restore(User $user, Person $person)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.people'))
            && $this->trashed($person);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Person $person
     * @return mixed
     */
    public function forceDelete(User $user, Person $person)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.people'))
            && $this->trashed($person)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given person is trashed.
     *
     * @param $person
     * @return bool
     */
    public function trashed($person)
    {
        return $this->hasSoftDeletes() && method_exists($person, 'trashed') && $person->trashed();
    }

    /**
     * Determine wither the model use soft deleting trait.
     *
     * @return bool
     */
    public function hasSoftDeletes()
    {
        return in_array(
            SoftDeletes::class,
            array_keys((new \ReflectionClass(Person::class))->getTraits())
        );
    }
}

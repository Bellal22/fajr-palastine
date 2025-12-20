<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any locations.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.locations');
    }

    /**
     * Determine whether the user can view the location.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Location $location
     * @return mixed
     */
    public function view(User $user, Location $location)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.locations');
    }

    /**
     * Determine whether the user can create locations.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.locations');
    }

    /**
     * Determine whether the user can update the location.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Location $location
     * @return mixed
     */
    public function update(User $user, Location $location)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.locations'))
            && ! $this->trashed($location);
    }

    /**
     * Determine whether the user can delete the location.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Location $location
     * @return mixed
     */
    public function delete(User $user, Location $location)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.locations'))
            && ! $this->trashed($location);
    }

    /**
     * Determine whether the user can view trashed locations.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.locations'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed location.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Location $location
     * @return mixed
     */
    public function viewTrash(User $user, Location $location)
    {
        return $this->view($user, $location)
            && $location->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Location $location
     * @return mixed
     */
    public function restore(User $user, Location $location)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.locations'))
            && $this->trashed($location);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Location $location
     * @return mixed
     */
    public function forceDelete(User $user, Location $location)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.locations'))
            && $this->trashed($location)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given location is trashed.
     *
     * @param $location
     * @return bool
     */
    public function trashed($location)
    {
        return $this->hasSoftDeletes() && method_exists($location, 'trashed') && $location->trashed();
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
            array_keys((new \ReflectionClass(Location::class))->getTraits())
        );
    }
}

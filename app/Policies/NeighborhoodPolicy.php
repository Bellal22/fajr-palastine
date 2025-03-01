<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Neighborhood;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class NeighborhoodPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any neighborhoods.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.neighborhoods');
    }

    /**
     * Determine whether the user can view the neighborhood.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Neighborhood $neighborhood
     * @return mixed
     */
    public function view(User $user, Neighborhood $neighborhood)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.neighborhoods');
    }

    /**
     * Determine whether the user can create neighborhoods.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.neighborhoods');
    }

    /**
     * Determine whether the user can update the neighborhood.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Neighborhood $neighborhood
     * @return mixed
     */
    public function update(User $user, Neighborhood $neighborhood)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.neighborhoods'))
            && ! $this->trashed($neighborhood);
    }

    /**
     * Determine whether the user can delete the neighborhood.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Neighborhood $neighborhood
     * @return mixed
     */
    public function delete(User $user, Neighborhood $neighborhood)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.neighborhoods'))
            && ! $this->trashed($neighborhood);
    }

    /**
     * Determine whether the user can view trashed neighborhoods.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.neighborhoods'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed neighborhood.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Neighborhood $neighborhood
     * @return mixed
     */
    public function viewTrash(User $user, Neighborhood $neighborhood)
    {
        return $this->view($user, $neighborhood)
            && $neighborhood->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Neighborhood $neighborhood
     * @return mixed
     */
    public function restore(User $user, Neighborhood $neighborhood)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.neighborhoods'))
            && $this->trashed($neighborhood);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Neighborhood $neighborhood
     * @return mixed
     */
    public function forceDelete(User $user, Neighborhood $neighborhood)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.neighborhoods'))
            && $this->trashed($neighborhood)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given neighborhood is trashed.
     *
     * @param $neighborhood
     * @return bool
     */
    public function trashed($neighborhood)
    {
        return $this->hasSoftDeletes() && method_exists($neighborhood, 'trashed') && $neighborhood->trashed();
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
            array_keys((new \ReflectionClass(Neighborhood::class))->getTraits())
        );
    }
}

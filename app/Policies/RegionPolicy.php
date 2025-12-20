<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Region;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any regions.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.regions');
    }

    /**
     * Determine whether the user can view the region.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Region $region
     * @return mixed
     */
    public function view(User $user, Region $region)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.regions');
    }

    /**
     * Determine whether the user can create regions.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.regions');
    }

    /**
     * Determine whether the user can update the region.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Region $region
     * @return mixed
     */
    public function update(User $user, Region $region)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.regions'))
            && ! $this->trashed($region);
    }

    /**
     * Determine whether the user can delete the region.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Region $region
     * @return mixed
     */
    public function delete(User $user, Region $region)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.regions'))
            && ! $this->trashed($region);
    }

    /**
     * Determine whether the user can view trashed regions.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.regions'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed region.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Region $region
     * @return mixed
     */
    public function viewTrash(User $user, Region $region)
    {
        return $this->view($user, $region)
            && $region->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Region $region
     * @return mixed
     */
    public function restore(User $user, Region $region)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.regions'))
            && $this->trashed($region);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Region $region
     * @return mixed
     */
    public function forceDelete(User $user, Region $region)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.regions'))
            && $this->trashed($region)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given region is trashed.
     *
     * @param $region
     * @return bool
     */
    public function trashed($region)
    {
        return $this->hasSoftDeletes() && method_exists($region, 'trashed') && $region->trashed();
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
            array_keys((new \ReflectionClass(Region::class))->getTraits())
        );
    }
}

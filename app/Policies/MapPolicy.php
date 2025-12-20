<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Map;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class MapPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any maps.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.maps');
    }

    /**
     * Determine whether the user can view the map.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Map $map
     * @return mixed
     */
    public function view(User $user, Map $map)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.maps');
    }

    /**
     * Determine whether the user can create maps.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.maps');
    }

    /**
     * Determine whether the user can update the map.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Map $map
     * @return mixed
     */
    public function update(User $user, Map $map)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.maps'))
            && ! $this->trashed($map);
    }

    /**
     * Determine whether the user can delete the map.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Map $map
     * @return mixed
     */
    public function delete(User $user, Map $map)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.maps'))
            && ! $this->trashed($map);
    }

    /**
     * Determine whether the user can view trashed maps.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.maps'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed map.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Map $map
     * @return mixed
     */
    public function viewTrash(User $user, Map $map)
    {
        return $this->view($user, $map)
            && $map->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Map $map
     * @return mixed
     */
    public function restore(User $user, Map $map)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.maps'))
            && $this->trashed($map);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Map $map
     * @return mixed
     */
    public function forceDelete(User $user, Map $map)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.maps'))
            && $this->trashed($map)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given map is trashed.
     *
     * @param $map
     * @return bool
     */
    public function trashed($map)
    {
        return $this->hasSoftDeletes() && method_exists($map, 'trashed') && $map->trashed();
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
            array_keys((new \ReflectionClass(Map::class))->getTraits())
        );
    }
}

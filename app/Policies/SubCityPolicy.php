<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SubCity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubCityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any sub cities.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.sub_cities');
    }

    /**
     * Determine whether the user can view the sub city.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\SubCity $sub_city
     * @return mixed
     */
    public function view(User $user, SubCity $sub_city)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.sub_cities');
    }

    /**
     * Determine whether the user can create sub cities.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.sub_cities');
    }

    /**
     * Determine whether the user can update the sub city.
     *
     * @param \App\Models\User $user
     * @param \App\Models\SubCity $sub_city
     * @return mixed
     */
    public function update(User $user, SubCity $sub_city)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.sub_cities'))
            && ! $this->trashed($sub_city);
    }

    /**
     * Determine whether the user can delete the sub city.
     *
     * @param \App\Models\User $user
     * @param \App\Models\SubCity $sub_city
     * @return mixed
     */
    public function delete(User $user, SubCity $sub_city)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.sub_cities'))
            && ! $this->trashed($sub_city);
    }

    /**
     * Determine whether the user can view trashed sub_cities.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.sub_cities'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed sub_city.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\SubCity $sub_city
     * @return mixed
     */
    public function viewTrash(User $user, SubCity $sub_city)
    {
        return $this->view($user, $sub_city)
            && $sub_city->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\SubCity $sub_city
     * @return mixed
     */
    public function restore(User $user, SubCity $sub_city)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.sub_cities'))
            && $this->trashed($sub_city);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\SubCity $sub_city
     * @return mixed
     */
    public function forceDelete(User $user, SubCity $sub_city)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.sub_cities'))
            && $this->trashed($sub_city)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given sub_city is trashed.
     *
     * @param $sub_city
     * @return bool
     */
    public function trashed($sub_city)
    {
        return $this->hasSoftDeletes() && method_exists($sub_city, 'trashed') && $sub_city->trashed();
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
            array_keys((new \ReflectionClass(SubCity::class))->getTraits())
        );
    }
}

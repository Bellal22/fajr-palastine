<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AreaResponsible;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class AreaResponsiblePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any area responsibles.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.area_responsibles');
    }

    /**
     * Determine whether the user can view the area responsible.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\AreaResponsible $area_responsible
     * @return mixed
     */
    public function view(User $user, AreaResponsible $area_responsible)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.area_responsibles');
    }

    /**
     * Determine whether the user can create area responsibles.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.area_responsibles');
    }

    /**
     * Determine whether the user can update the area responsible.
     *
     * @param \App\Models\User $user
     * @param \App\Models\AreaResponsible $area_responsible
     * @return mixed
     */
    public function update(User $user, AreaResponsible $area_responsible)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.area_responsibles'))
            && ! $this->trashed($area_responsible);
    }

    /**
     * Determine whether the user can delete the area responsible.
     *
     * @param \App\Models\User $user
     * @param \App\Models\AreaResponsible $area_responsible
     * @return mixed
     */
    public function delete(User $user, AreaResponsible $area_responsible)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.area_responsibles'))
            && ! $this->trashed($area_responsible);
    }

    /**
     * Determine whether the user can view trashed area_responsibles.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.area_responsibles'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed area_responsible.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\AreaResponsible $area_responsible
     * @return mixed
     */
    public function viewTrash(User $user, AreaResponsible $area_responsible)
    {
        return $this->view($user, $area_responsible)
            && $area_responsible->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\AreaResponsible $area_responsible
     * @return mixed
     */
    public function restore(User $user, AreaResponsible $area_responsible)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.area_responsibles'))
            && $this->trashed($area_responsible);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\AreaResponsible $area_responsible
     * @return mixed
     */
    public function forceDelete(User $user, AreaResponsible $area_responsible)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.area_responsibles'))
            && $this->trashed($area_responsible)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given area_responsible is trashed.
     *
     * @param $area_responsible
     * @return bool
     */
    public function trashed($area_responsible)
    {
        return $this->hasSoftDeletes() && method_exists($area_responsible, 'trashed') && $area_responsible->trashed();
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
            array_keys((new \ReflectionClass(AreaResponsible::class))->getTraits())
        );
    }
}

<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SubWarehouse;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubWarehousePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any sub warehouses.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.sub_warehouses');
    }

    /**
     * Determine whether the user can view the sub warehouse.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\SubWarehouse $sub_warehouse
     * @return mixed
     */
    public function view(User $user, SubWarehouse $sub_warehouse)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.sub_warehouses');
    }

    /**
     * Determine whether the user can create sub warehouses.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.sub_warehouses');
    }

    /**
     * Determine whether the user can update the sub warehouse.
     *
     * @param \App\Models\User $user
     * @param \App\Models\SubWarehouse $sub_warehouse
     * @return mixed
     */
    public function update(User $user, SubWarehouse $sub_warehouse)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.sub_warehouses'))
            && ! $this->trashed($sub_warehouse);
    }

    /**
     * Determine whether the user can delete the sub warehouse.
     *
     * @param \App\Models\User $user
     * @param \App\Models\SubWarehouse $sub_warehouse
     * @return mixed
     */
    public function delete(User $user, SubWarehouse $sub_warehouse)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.sub_warehouses'))
            && ! $this->trashed($sub_warehouse);
    }

    /**
     * Determine whether the user can view trashed sub_warehouses.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.sub_warehouses'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed sub_warehouse.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\SubWarehouse $sub_warehouse
     * @return mixed
     */
    public function viewTrash(User $user, SubWarehouse $sub_warehouse)
    {
        return $this->view($user, $sub_warehouse)
            && $sub_warehouse->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\SubWarehouse $sub_warehouse
     * @return mixed
     */
    public function restore(User $user, SubWarehouse $sub_warehouse)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.sub_warehouses'))
            && $this->trashed($sub_warehouse);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\SubWarehouse $sub_warehouse
     * @return mixed
     */
    public function forceDelete(User $user, SubWarehouse $sub_warehouse)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.sub_warehouses'))
            && $this->trashed($sub_warehouse)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given sub_warehouse is trashed.
     *
     * @param $sub_warehouse
     * @return bool
     */
    public function trashed($sub_warehouse)
    {
        return $this->hasSoftDeletes() && method_exists($sub_warehouse, 'trashed') && $sub_warehouse->trashed();
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
            array_keys((new \ReflectionClass(SubWarehouse::class))->getTraits())
        );
    }
}

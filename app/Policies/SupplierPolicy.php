<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any suppliers.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.suppliers');
    }

    /**
     * Determine whether the user can view the supplier.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Supplier $supplier
     * @return mixed
     */
    public function view(User $user, Supplier $supplier)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.suppliers');
    }

    /**
     * Determine whether the user can create suppliers.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.suppliers');
    }

    /**
     * Determine whether the user can update the supplier.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Supplier $supplier
     * @return mixed
     */
    public function update(User $user, Supplier $supplier)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.suppliers'))
            && ! $this->trashed($supplier);
    }

    /**
     * Determine whether the user can delete the supplier.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Supplier $supplier
     * @return mixed
     */
    public function delete(User $user, Supplier $supplier)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.suppliers'))
            && ! $this->trashed($supplier);
    }

    /**
     * Determine whether the user can view trashed suppliers.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.suppliers'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed supplier.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Supplier $supplier
     * @return mixed
     */
    public function viewTrash(User $user, Supplier $supplier)
    {
        return $this->view($user, $supplier)
            && $supplier->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Supplier $supplier
     * @return mixed
     */
    public function restore(User $user, Supplier $supplier)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.suppliers'))
            && $this->trashed($supplier);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Supplier $supplier
     * @return mixed
     */
    public function forceDelete(User $user, Supplier $supplier)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.suppliers'))
            && $this->trashed($supplier)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given supplier is trashed.
     *
     * @param $supplier
     * @return bool
     */
    public function trashed($supplier)
    {
        return $this->hasSoftDeletes() && method_exists($supplier, 'trashed') && $supplier->trashed();
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
            array_keys((new \ReflectionClass(Supplier::class))->getTraits())
        );
    }
}

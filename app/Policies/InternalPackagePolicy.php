<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InternalPackage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class InternalPackagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any internal packages.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.internal_packages');
    }

    /**
     * Determine whether the user can view the internal package.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\InternalPackage $internal_package
     * @return mixed
     */
    public function view(User $user, InternalPackage $internal_package)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.internal_packages');
    }

    /**
     * Determine whether the user can create internal packages.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.internal_packages');
    }

    /**
     * Determine whether the user can update the internal package.
     *
     * @param \App\Models\User $user
     * @param \App\Models\InternalPackage $internal_package
     * @return mixed
     */
    public function update(User $user, InternalPackage $internal_package)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.internal_packages'))
            && ! $this->trashed($internal_package);
    }

    /**
     * Determine whether the user can delete the internal package.
     *
     * @param \App\Models\User $user
     * @param \App\Models\InternalPackage $internal_package
     * @return mixed
     */
    public function delete(User $user, InternalPackage $internal_package)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.internal_packages'))
            && ! $this->trashed($internal_package);
    }

    /**
     * Determine whether the user can view trashed internal_packages.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.internal_packages'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed internal_package.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\InternalPackage $internal_package
     * @return mixed
     */
    public function viewTrash(User $user, InternalPackage $internal_package)
    {
        return $this->view($user, $internal_package)
            && $internal_package->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\InternalPackage $internal_package
     * @return mixed
     */
    public function restore(User $user, InternalPackage $internal_package)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.internal_packages'))
            && $this->trashed($internal_package);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\InternalPackage $internal_package
     * @return mixed
     */
    public function forceDelete(User $user, InternalPackage $internal_package)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.internal_packages'))
            && $this->trashed($internal_package)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given internal_package is trashed.
     *
     * @param $internal_package
     * @return bool
     */
    public function trashed($internal_package)
    {
        return $this->hasSoftDeletes() && method_exists($internal_package, 'trashed') && $internal_package->trashed();
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
            array_keys((new \ReflectionClass(InternalPackage::class))->getTraits())
        );
    }
}

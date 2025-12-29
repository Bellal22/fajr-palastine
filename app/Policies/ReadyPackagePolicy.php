<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ReadyPackage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReadyPackagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any ready packages.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.ready_packages');
    }

    /**
     * Determine whether the user can view the ready package.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\ReadyPackage $ready_package
     * @return mixed
     */
    public function view(User $user, ReadyPackage $ready_package)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.ready_packages');
    }

    /**
     * Determine whether the user can create ready packages.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.ready_packages');
    }

    /**
     * Determine whether the user can update the ready package.
     *
     * @param \App\Models\User $user
     * @param \App\Models\ReadyPackage $ready_package
     * @return mixed
     */
    public function update(User $user, ReadyPackage $ready_package)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.ready_packages'))
            && ! $this->trashed($ready_package);
    }

    /**
     * Determine whether the user can delete the ready package.
     *
     * @param \App\Models\User $user
     * @param \App\Models\ReadyPackage $ready_package
     * @return mixed
     */
    public function delete(User $user, ReadyPackage $ready_package)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.ready_packages'))
            && ! $this->trashed($ready_package);
    }

    /**
     * Determine whether the user can view trashed ready_packages.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.ready_packages'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed ready_package.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\ReadyPackage $ready_package
     * @return mixed
     */
    public function viewTrash(User $user, ReadyPackage $ready_package)
    {
        return $this->view($user, $ready_package)
            && $ready_package->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\ReadyPackage $ready_package
     * @return mixed
     */
    public function restore(User $user, ReadyPackage $ready_package)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.ready_packages'))
            && $this->trashed($ready_package);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\ReadyPackage $ready_package
     * @return mixed
     */
    public function forceDelete(User $user, ReadyPackage $ready_package)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.ready_packages'))
            && $this->trashed($ready_package)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given ready_package is trashed.
     *
     * @param $ready_package
     * @return bool
     */
    public function trashed($ready_package)
    {
        return $this->hasSoftDeletes() && method_exists($ready_package, 'trashed') && $ready_package->trashed();
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
            array_keys((new \ReflectionClass(ReadyPackage::class))->getTraits())
        );
    }
}

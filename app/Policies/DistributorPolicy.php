<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Distributor;
use Illuminate\Auth\Access\HandlesAuthorization;
use Laraeast\LaravelSettings\Facades\Settings;

class DistributorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any distributors.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermission('manage.distributors');
    }

    /**
     * Determine whether the user can view the distributor.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Distributor $distributor
     * @return mixed
     */
    public function view(User $user, Distributor $distributor)
    {
        return $user->isAdmin() || $user->hasPermission('manage.distributors');
    }

    /**
     * Determine whether the user can create distributors.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermission('manage.distributors');
    }

    /**
     * Determine whether the user can update the distributor.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Distributor $distributor
     * @return mixed
     */
    public function update(User $user, Distributor $distributor)
    {
        return $user->isAdmin() || $user->hasPermission('manage.distributors');
    }

    /**
     * Determine whether the user can delete the distributor.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Distributor $distributor
     * @return mixed
     */
    public function delete(User $user, Distributor $distributor)
    {
        return $user->isAdmin() || $user->hasPermission('manage.distributors');
    }

    /**
     * Determine whether the user can view trashed distributors.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return $user->isAdmin() || $user->hasPermission('manage.distributors');
    }

    /**
     * Determine whether the user can view trashed distributor.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Distributor $distributor
     * @return mixed
     */
    public function viewTrash(User $user, Distributor $distributor)
    {
        return $user->isAdmin() || $user->hasPermission('manage.distributors');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Distributor $distributor
     * @return mixed
     */
    public function restore(User $user, Distributor $distributor)
    {
        return $user->isAdmin() || $user->hasPermission('manage.distributors');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Distributor $distributor
     * @return mixed
     */
    public function forceDelete(User $user, Distributor $distributor)
    {
        return $user->isAdmin() || $user->hasPermission('manage.distributors');
    }

    /**
     * Determine wither the given distributor is trashed.
     *
     * @param $distributor
     * @return bool
     */
    public function trashed($distributor)
    {
        return method_exists($distributor, 'trashed')
            && $distributor->trashed();
    }
}

<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Choose;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChoosePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any chooses.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.chooses');
    }

    /**
     * Determine whether the user can view the choose.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Choose $choose
     * @return mixed
     */
    public function view(User $user, Choose $choose)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.chooses');
    }

    /**
     * Determine whether the user can create chooses.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.chooses');
    }

    /**
     * Determine whether the user can update the choose.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Choose $choose
     * @return mixed
     */
    public function update(User $user, Choose $choose)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.chooses'))
            && ! $this->trashed($choose);
    }

    /**
     * Determine whether the user can delete the choose.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Choose $choose
     * @return mixed
     */
    public function delete(User $user, Choose $choose)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.chooses'))
            && ! $this->trashed($choose);
    }

    /**
     * Determine whether the user can view trashed chooses.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.chooses'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed choose.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Choose $choose
     * @return mixed
     */
    public function viewTrash(User $user, Choose $choose)
    {
        return $this->view($user, $choose)
            && $choose->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Choose $choose
     * @return mixed
     */
    public function restore(User $user, Choose $choose)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.chooses'))
            && $this->trashed($choose);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Choose $choose
     * @return mixed
     */
    public function forceDelete(User $user, Choose $choose)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.chooses'))
            && $this->trashed($choose)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given choose is trashed.
     *
     * @param $choose
     * @return bool
     */
    public function trashed($choose)
    {
        return $this->hasSoftDeletes() && method_exists($choose, 'trashed') && $choose->trashed();
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
            array_keys((new \ReflectionClass(Choose::class))->getTraits())
        );
    }
}

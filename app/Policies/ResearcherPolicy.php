<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Researcher;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Access\HandlesAuthorization;
use Laraeast\LaravelSettings\Facades\Settings;

class ResearcherPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any researchers.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.researchers');
    }

    /**
     * Determine whether the user can view the researcher.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Researcher $researcher
     * @return mixed
     */
    public function view(User $user, Researcher $researcher)
    {
        return $user->isAdmin()
            || $user->is($researcher)
            || $user->hasPermissionTo('manage.researchers');
    }

    /**
     * Determine whether the user can create researchers.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.researchers');
    }

    /**
     * Determine whether the user can update the researcher.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Researcher $researcher
     * @return mixed
     */
    public function update(User $user, Researcher $researcher)
    {
        return (
                $user->isAdmin()
                || $user->is($researcher)
                || $user->hasPermissionTo('manage.researchers')
            )
            && ! $this->trashed($researcher);
    }

    /**
     * Determine whether the user can delete the researcher.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Researcher $researcher
     * @return mixed
     */
    public function delete(User $user, Researcher $researcher)
    {
        return (
                $user->isAdmin()
                && $user->isNot($researcher)
                || $user->hasPermissionTo('manage.researchers')
            )
            && ! $this->trashed($researcher);
    }

    /**
     * Determine whether the user can view trashed researchers.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return (
                $user->isAdmin()
                || $user->hasPermissionTo('manage.researchers')
            )
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view trashed researcher.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Researcher $researcher
     * @return mixed
     */
    public function viewTrash(User $user, Researcher $researcher)
    {
        return $this->view($user, $researcher) && $this->trashed($researcher);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Researcher $researcher
     * @return mixed
     */
    public function restore(User $user, Researcher $researcher)
    {
        return (
                $user->isAdmin()
                || $user->hasPermissionTo('manage.researchers')
            )
            && $this->trashed($researcher);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Researcher $researcher
     * @return mixed
     */
    public function forceDelete(User $user, Researcher $researcher)
    {
        return (
                $user->isAdmin()
                && $user->isNot($researcher)
                || $user->hasPermissionTo('manage.researchers')
            )
            && $this->trashed($researcher)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given researcher is trashed.
     *
     * @param $researcher
     * @return bool
     */
    public function trashed($researcher)
    {
        return $this->hasSoftDeletes()
            && method_exists($researcher, 'trashed')
            && $researcher->trashed();
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
            array_keys((new \ReflectionClass(Researcher::class))->getTraits())
        );
    }
}

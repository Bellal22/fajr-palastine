<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Family;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class FamilyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any families.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.families');
    }

    /**
     * Determine whether the user can view the family.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Family $family
     * @return mixed
     */
    public function view(User $user, Family $family)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.families');
    }

    /**
     * Determine whether the user can create families.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.families');
    }

    /**
     * Determine whether the user can update the family.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Family $family
     * @return mixed
     */
    public function update(User $user, Family $family)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.families'))
            && ! $this->trashed($family);
    }

    /**
     * Determine whether the user can delete the family.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Family $family
     * @return mixed
     */
    public function delete(User $user, Family $family)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.families'))
            && ! $this->trashed($family);
    }

    /**
     * Determine whether the user can view trashed families.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.families'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed family.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Family $family
     * @return mixed
     */
    public function viewTrash(User $user, Family $family)
    {
        return $this->view($user, $family)
            && $family->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Family $family
     * @return mixed
     */
    public function restore(User $user, Family $family)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.families'))
            && $this->trashed($family);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Family $family
     * @return mixed
     */
    public function forceDelete(User $user, Family $family)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.families'))
            && $this->trashed($family)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given family is trashed.
     *
     * @param $family
     * @return bool
     */
    public function trashed($family)
    {
        return $this->hasSoftDeletes() && method_exists($family, 'trashed') && $family->trashed();
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
            array_keys((new \ReflectionClass(Family::class))->getTraits())
        );
    }
}

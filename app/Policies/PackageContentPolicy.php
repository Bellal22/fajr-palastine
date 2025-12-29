<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PackageContent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class PackageContentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any package contents.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.package_contents');
    }

    /**
     * Determine whether the user can view the package content.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\PackageContent $package_content
     * @return mixed
     */
    public function view(User $user, PackageContent $package_content)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.package_contents');
    }

    /**
     * Determine whether the user can create package contents.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.package_contents');
    }

    /**
     * Determine whether the user can update the package content.
     *
     * @param \App\Models\User $user
     * @param \App\Models\PackageContent $package_content
     * @return mixed
     */
    public function update(User $user, PackageContent $package_content)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.package_contents'))
            && ! $this->trashed($package_content);
    }

    /**
     * Determine whether the user can delete the package content.
     *
     * @param \App\Models\User $user
     * @param \App\Models\PackageContent $package_content
     * @return mixed
     */
    public function delete(User $user, PackageContent $package_content)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.package_contents'))
            && ! $this->trashed($package_content);
    }

    /**
     * Determine whether the user can view trashed package_contents.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.package_contents'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed package_content.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\PackageContent $package_content
     * @return mixed
     */
    public function viewTrash(User $user, PackageContent $package_content)
    {
        return $this->view($user, $package_content)
            && $package_content->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\PackageContent $package_content
     * @return mixed
     */
    public function restore(User $user, PackageContent $package_content)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.package_contents'))
            && $this->trashed($package_content);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\PackageContent $package_content
     * @return mixed
     */
    public function forceDelete(User $user, PackageContent $package_content)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.package_contents'))
            && $this->trashed($package_content)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given package_content is trashed.
     *
     * @param $package_content
     * @return bool
     */
    public function trashed($package_content)
    {
        return $this->hasSoftDeletes() && method_exists($package_content, 'trashed') && $package_content->trashed();
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
            array_keys((new \ReflectionClass(PackageContent::class))->getTraits())
        );
    }
}

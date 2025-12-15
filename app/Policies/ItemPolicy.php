<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any items.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.items');
    }

    /**
     * Determine whether the user can view the item.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Item $item
     * @return mixed
     */
    public function view(User $user, Item $item)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.items');
    }

    /**
     * Determine whether the user can create items.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.items');
    }

    /**
     * Determine whether the user can update the item.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Item $item
     * @return mixed
     */
    public function update(User $user, Item $item)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.items'))
            && ! $this->trashed($item);
    }

    /**
     * Determine whether the user can delete the item.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Item $item
     * @return mixed
     */
    public function delete(User $user, Item $item)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.items'))
            && ! $this->trashed($item);
    }

    /**
     * Determine whether the user can view trashed items.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.items'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed item.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Item $item
     * @return mixed
     */
    public function viewTrash(User $user, Item $item)
    {
        return $this->view($user, $item)
            && $item->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Item $item
     * @return mixed
     */
    public function restore(User $user, Item $item)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.items'))
            && $this->trashed($item);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Item $item
     * @return mixed
     */
    public function forceDelete(User $user, Item $item)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.items'))
            && $this->trashed($item)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given item is trashed.
     *
     * @param $item
     * @return bool
     */
    public function trashed($item)
    {
        return $this->hasSoftDeletes() && method_exists($item, 'trashed') && $item->trashed();
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
            array_keys((new \ReflectionClass(Item::class))->getTraits())
        );
    }
}

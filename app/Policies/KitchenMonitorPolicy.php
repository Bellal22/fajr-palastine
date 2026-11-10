<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KitchenMonitor;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Access\HandlesAuthorization;
use Laraeast\LaravelSettings\Facades\Settings;

class KitchenMonitorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any kitchen monitors.
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermission('manage.kitchen_monitors') || $user->hasPermission('manage.supervisors');
    }

    /**
     * Determine whether the user can view the kitchen monitor.
     */
    public function view(User $user, KitchenMonitor $kitchenMonitor)
    {
        return $user->isAdmin()
            || $user->is($kitchenMonitor)
            || $user->hasPermission('manage.kitchen_monitors') || $user->hasPermission('manage.supervisors');
    }

    /**
     * Determine whether the user can create kitchen monitors.
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermission('manage.kitchen_monitors') || $user->hasPermission('manage.supervisors');
    }

    /**
     * Determine whether the user can update the kitchen monitor.
     */
    public function update(User $user, KitchenMonitor $kitchenMonitor)
    {
        return (
                $user->isAdmin()
                || $user->is($kitchenMonitor)
                || $user->hasPermission('manage.kitchen_monitors') || $user->hasPermission('manage.supervisors')
            )
            && ! $this->trashed($kitchenMonitor);
    }

    /**
     * Determine whether the user can update the type of the kitchen monitor.
     */
    public function updateType(User $user, KitchenMonitor $kitchenMonitor)
    {
        return $user->isAdmin()
            && $user->isNot($kitchenMonitor)
            || $user->hasPermission('manage.kitchen_monitors') || $user->hasPermission('manage.supervisors');
    }

    /**
     * Determine whether the user can delete the kitchen monitor.
     */
    public function delete(User $user, KitchenMonitor $kitchenMonitor)
    {
        return (
                $user->isAdmin()
                && $user->isNot($kitchenMonitor)
                || $user->hasPermission('manage.kitchen_monitors') || $user->hasPermission('manage.supervisors')
            )
            && ! $this->trashed($kitchenMonitor);
    }

    /**
     * Determine whether the user can view trashed kitchen monitors.
     */
    public function viewAnyTrash(User $user)
    {
        return (
                $user->isAdmin()
                || $user->hasPermission('manage.kitchen_monitors') || $user->hasPermission('manage.supervisors')
            )
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view trashed kitchen monitor.
     */
    public function viewTrash(User $user, KitchenMonitor $kitchenMonitor)
    {
        return $this->view($user, $kitchenMonitor) && $this->trashed($kitchenMonitor);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, KitchenMonitor $kitchenMonitor)
    {
        return (
                $user->isAdmin()
                || $user->hasPermission('manage.kitchen_monitors') || $user->hasPermission('manage.supervisors')
            )
            && $this->trashed($kitchenMonitor);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, KitchenMonitor $kitchenMonitor)
    {
        return (
                $user->isAdmin()
                && $user->isNot($kitchenMonitor)
                || $user->hasPermission('manage.kitchen_monitors') || $user->hasPermission('manage.supervisors')
            )
            && $this->trashed($kitchenMonitor)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given kitchen monitor is trashed.
     */
    public function trashed($kitchenMonitor)
    {
        return $this->hasSoftDeletes()
            && method_exists($kitchenMonitor, 'trashed')
            && $kitchenMonitor->trashed();
    }

    /**
     * Determine wither the model use soft deleting trait.
     */
    public function hasSoftDeletes()
    {
        return in_array(
            SoftDeletes::class,
            array_keys((new \ReflectionClass(KitchenMonitor::class))->getTraits())
        );
    }
}

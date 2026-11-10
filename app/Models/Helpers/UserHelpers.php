<?php

namespace App\Models\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;

trait UserHelpers
{
    /**
     * Determine whether the user type is admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->type == User::ADMIN_TYPE;
    }

    /**
     * Determine whether the user type is supervisor.
     *
     * @return bool
     */
    public function isSupervisor()
    {
        return $this->type == User::SUPERVISOR_TYPE;
    }

    /**
     * Determine whether the user type is customer.
     *
     * @return bool
     */
    public function isCustomer()
    {
        return $this->type == User::CUSTOMER_TYPE;
    }

    /**
     * Determine whether the user type is general search only.
     *
     * @return bool
     */
    public function isGeneralSearchOnly()
    {
        return $this->type == User::GENERAL_SEARCH_TYPE;
    }

    /**
     * Determine whether the user type is kitchen monitor.
     *
     * @return bool
     */
    public function isKitchenMonitor()
    {
        return $this->type == User::KITCHEN_MONITOR_TYPE;
    }

    /**
     * Set the user type.
     *
     * @return $this
     */
    public function setType($type)
    {
        if (Gate::allows('updateType', $this)
            && in_array($type, array_keys(trans('users.types')))) {
            $this->forceFill([
                'type' => $type,
            ])->save();
        }

        return $this;
    }

    /**
     * Determine whether the user can access dashboard.
     *
     * @return bool
     */
    public function canAccessDashboard()
    {
        return $this->isAdmin() || $this->isSupervisor() || $this->isKitchenMonitor();
    }

    /**
     * Check if the user has a given permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission)
    {
        try {
            return $this->hasPermissionTo($permission);
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            \Spatie\Permission\Models\Permission::findOrCreate($permission, 'web');
            return $this->hasPermissionTo($permission);
        }
    }

    /**
     * Ensure all given permissions exist in DB before syncing them.
     *
     * @param array $permissions
     * @return $this
     */
    public function syncUserPermissions(array $permissions = [])
    {
        foreach ($permissions as $permission) {
            if (is_string($permission) && $permission !== '') {
                \Spatie\Permission\Models\Permission::findOrCreate($permission, 'web');
            }
        }

        return $this->syncPermissions($permissions);
    }

    /**
     * The user profile image url.
     *
     * @return bool
     */
    public function getAvatar()
    {
        return $this->getFirstMediaUrl('avatars');
    }
}
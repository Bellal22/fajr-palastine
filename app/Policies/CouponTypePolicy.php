<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CouponType;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class CouponTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any coupon types.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.coupon_types');
    }

    /**
     * Determine whether the user can view the coupon type.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\CouponType $coupon_type
     * @return mixed
     */
    public function view(User $user, CouponType $coupon_type)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.coupon_types');
    }

    /**
     * Determine whether the user can create coupon types.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.coupon_types');
    }

    /**
     * Determine whether the user can update the coupon type.
     *
     * @param \App\Models\User $user
     * @param \App\Models\CouponType $coupon_type
     * @return mixed
     */
    public function update(User $user, CouponType $coupon_type)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.coupon_types'))
            && ! $this->trashed($coupon_type);
    }

    /**
     * Determine whether the user can delete the coupon type.
     *
     * @param \App\Models\User $user
     * @param \App\Models\CouponType $coupon_type
     * @return mixed
     */
    public function delete(User $user, CouponType $coupon_type)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.coupon_types'))
            && ! $this->trashed($coupon_type);
    }

    /**
     * Determine whether the user can view trashed coupon_types.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.coupon_types'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed coupon_type.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\CouponType $coupon_type
     * @return mixed
     */
    public function viewTrash(User $user, CouponType $coupon_type)
    {
        return $this->view($user, $coupon_type)
            && $coupon_type->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\CouponType $coupon_type
     * @return mixed
     */
    public function restore(User $user, CouponType $coupon_type)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.coupon_types'))
            && $this->trashed($coupon_type);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\CouponType $coupon_type
     * @return mixed
     */
    public function forceDelete(User $user, CouponType $coupon_type)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.coupon_types'))
            && $this->trashed($coupon_type)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given coupon_type is trashed.
     *
     * @param $coupon_type
     * @return bool
     */
    public function trashed($coupon_type)
    {
        return $this->hasSoftDeletes() && method_exists($coupon_type, 'trashed') && $coupon_type->trashed();
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
            array_keys((new \ReflectionClass(CouponType::class))->getTraits())
        );
    }
}

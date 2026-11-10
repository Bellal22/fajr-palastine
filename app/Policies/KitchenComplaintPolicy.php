<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KitchenComplaint;
use Illuminate\Auth\Access\HandlesAuthorization;

class KitchenComplaintPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.kitchen_complaints');
    }

    public function view(User $user, KitchenComplaint $kitchenComplaint)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.kitchen_complaints');
    }

    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.kitchen_complaints');
    }

    public function update(User $user, KitchenComplaint $kitchenComplaint)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.kitchen_complaints');
    }

    public function delete(User $user, KitchenComplaint $kitchenComplaint)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.kitchen_complaints');
    }
}

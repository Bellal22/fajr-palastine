<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KitchenCard;
use Illuminate\Auth\Access\HandlesAuthorization;

class KitchenCardPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.kitchen_cards');
    }

    public function view(User $user, KitchenCard $kitchenCard)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.kitchen_cards');
    }

    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.kitchen_cards');
    }

    public function update(User $user, KitchenCard $kitchenCard)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.kitchen_cards');
    }

    public function delete(User $user, KitchenCard $kitchenCard)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.kitchen_cards');
    }
}

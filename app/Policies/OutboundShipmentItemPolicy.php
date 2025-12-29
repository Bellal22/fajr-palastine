<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OutboundShipmentItem;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class OutboundShipmentItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any outbound shipment items.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.outbound_shipment_items');
    }

    /**
     * Determine whether the user can view the outbound shipment item.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\OutboundShipmentItem $outbound_shipment_item
     * @return mixed
     */
    public function view(User $user, OutboundShipmentItem $outbound_shipment_item)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.outbound_shipment_items');
    }

    /**
     * Determine whether the user can create outbound shipment items.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.outbound_shipment_items');
    }

    /**
     * Determine whether the user can update the outbound shipment item.
     *
     * @param \App\Models\User $user
     * @param \App\Models\OutboundShipmentItem $outbound_shipment_item
     * @return mixed
     */
    public function update(User $user, OutboundShipmentItem $outbound_shipment_item)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.outbound_shipment_items'))
            && ! $this->trashed($outbound_shipment_item);
    }

    /**
     * Determine whether the user can delete the outbound shipment item.
     *
     * @param \App\Models\User $user
     * @param \App\Models\OutboundShipmentItem $outbound_shipment_item
     * @return mixed
     */
    public function delete(User $user, OutboundShipmentItem $outbound_shipment_item)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.outbound_shipment_items'))
            && ! $this->trashed($outbound_shipment_item);
    }

    /**
     * Determine whether the user can view trashed outbound_shipment_items.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.outbound_shipment_items'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed outbound_shipment_item.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\OutboundShipmentItem $outbound_shipment_item
     * @return mixed
     */
    public function viewTrash(User $user, OutboundShipmentItem $outbound_shipment_item)
    {
        return $this->view($user, $outbound_shipment_item)
            && $outbound_shipment_item->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\OutboundShipmentItem $outbound_shipment_item
     * @return mixed
     */
    public function restore(User $user, OutboundShipmentItem $outbound_shipment_item)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.outbound_shipment_items'))
            && $this->trashed($outbound_shipment_item);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\OutboundShipmentItem $outbound_shipment_item
     * @return mixed
     */
    public function forceDelete(User $user, OutboundShipmentItem $outbound_shipment_item)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.outbound_shipment_items'))
            && $this->trashed($outbound_shipment_item)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given outbound_shipment_item is trashed.
     *
     * @param $outbound_shipment_item
     * @return bool
     */
    public function trashed($outbound_shipment_item)
    {
        return $this->hasSoftDeletes() && method_exists($outbound_shipment_item, 'trashed') && $outbound_shipment_item->trashed();
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
            array_keys((new \ReflectionClass(OutboundShipmentItem::class))->getTraits())
        );
    }
}

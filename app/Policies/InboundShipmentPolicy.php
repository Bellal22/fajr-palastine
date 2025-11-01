<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InboundShipment;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class InboundShipmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any inbound shipments.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.inbound_shipments');
    }

    /**
     * Determine whether the user can view the inbound shipment.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\InboundShipment $inbound_shipment
     * @return mixed
     */
    public function view(User $user, InboundShipment $inbound_shipment)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.inbound_shipments');
    }

    /**
     * Determine whether the user can create inbound shipments.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.inbound_shipments');
    }

    /**
     * Determine whether the user can update the inbound shipment.
     *
     * @param \App\Models\User $user
     * @param \App\Models\InboundShipment $inbound_shipment
     * @return mixed
     */
    public function update(User $user, InboundShipment $inbound_shipment)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.inbound_shipments'))
            && ! $this->trashed($inbound_shipment);
    }

    /**
     * Determine whether the user can delete the inbound shipment.
     *
     * @param \App\Models\User $user
     * @param \App\Models\InboundShipment $inbound_shipment
     * @return mixed
     */
    public function delete(User $user, InboundShipment $inbound_shipment)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.inbound_shipments'))
            && ! $this->trashed($inbound_shipment);
    }

    /**
     * Determine whether the user can view trashed inbound_shipments.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.inbound_shipments'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed inbound_shipment.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\InboundShipment $inbound_shipment
     * @return mixed
     */
    public function viewTrash(User $user, InboundShipment $inbound_shipment)
    {
        return $this->view($user, $inbound_shipment)
            && $inbound_shipment->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\InboundShipment $inbound_shipment
     * @return mixed
     */
    public function restore(User $user, InboundShipment $inbound_shipment)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.inbound_shipments'))
            && $this->trashed($inbound_shipment);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\InboundShipment $inbound_shipment
     * @return mixed
     */
    public function forceDelete(User $user, InboundShipment $inbound_shipment)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.inbound_shipments'))
            && $this->trashed($inbound_shipment)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given inbound_shipment is trashed.
     *
     * @param $inbound_shipment
     * @return bool
     */
    public function trashed($inbound_shipment)
    {
        return $this->hasSoftDeletes() && method_exists($inbound_shipment, 'trashed') && $inbound_shipment->trashed();
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
            array_keys((new \ReflectionClass(InboundShipment::class))->getTraits())
        );
    }
}

<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OutboundShipment;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class OutboundShipmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any outbound shipments.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.outbound_shipments');
    }

    /**
     * Determine whether the user can view the outbound shipment.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\OutboundShipment $outbound_shipment
     * @return mixed
     */
    public function view(User $user, OutboundShipment $outbound_shipment)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.outbound_shipments');
    }

    /**
     * Determine whether the user can create outbound shipments.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.outbound_shipments');
    }

    /**
     * Determine whether the user can update the outbound shipment.
     *
     * @param \App\Models\User $user
     * @param \App\Models\OutboundShipment $outbound_shipment
     * @return mixed
     */
    public function update(User $user, OutboundShipment $outbound_shipment)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.outbound_shipments'))
            && ! $this->trashed($outbound_shipment);
    }

    /**
     * Determine whether the user can delete the outbound shipment.
     *
     * @param \App\Models\User $user
     * @param \App\Models\OutboundShipment $outbound_shipment
     * @return mixed
     */
    public function delete(User $user, OutboundShipment $outbound_shipment)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.outbound_shipments'))
            && ! $this->trashed($outbound_shipment);
    }

    /**
     * Determine whether the user can view trashed outbound_shipments.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.outbound_shipments'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed outbound_shipment.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\OutboundShipment $outbound_shipment
     * @return mixed
     */
    public function viewTrash(User $user, OutboundShipment $outbound_shipment)
    {
        return $this->view($user, $outbound_shipment)
            && $outbound_shipment->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\OutboundShipment $outbound_shipment
     * @return mixed
     */
    public function restore(User $user, OutboundShipment $outbound_shipment)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.outbound_shipments'))
            && $this->trashed($outbound_shipment);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\OutboundShipment $outbound_shipment
     * @return mixed
     */
    public function forceDelete(User $user, OutboundShipment $outbound_shipment)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.outbound_shipments'))
            && $this->trashed($outbound_shipment)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given outbound_shipment is trashed.
     *
     * @param $outbound_shipment
     * @return bool
     */
    public function trashed($outbound_shipment)
    {
        return $this->hasSoftDeletes() && method_exists($outbound_shipment, 'trashed') && $outbound_shipment->trashed();
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
            array_keys((new \ReflectionClass(OutboundShipment::class))->getTraits())
        );
    }
}

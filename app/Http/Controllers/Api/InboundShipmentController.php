<?php

namespace App\Http\Controllers\Api;

use App\Models\InboundShipment;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\InboundShipmentResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InboundShipmentController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the inbound_shipments.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $inbound_shipments = InboundShipment::filter()->simplePaginate();

        return InboundShipmentResource::collection($inbound_shipments);
    }

    /**
     * Display the specified inbound_shipment.
     *
     * @param \App\Models\InboundShipment $inbound_shipment
     * @return \App\Http\Resources\InboundShipmentResource
     */
    public function show(InboundShipment $inbound_shipment)
    {
        return new InboundShipmentResource($inbound_shipment);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $inbound_shipments = InboundShipment::filter()->simplePaginate();

        return SelectResource::collection($inbound_shipments);
    }
}

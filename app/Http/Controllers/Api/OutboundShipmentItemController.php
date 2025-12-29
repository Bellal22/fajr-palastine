<?php

namespace App\Http\Controllers\Api;

use App\Models\OutboundShipmentItem;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\OutboundShipmentItemResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OutboundShipmentItemController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the outbound_shipment_items.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $outbound_shipment_items = OutboundShipmentItem::filter()->simplePaginate();

        return OutboundShipmentItemResource::collection($outbound_shipment_items);
    }

    /**
     * Display the specified outbound_shipment_item.
     *
     * @param \App\Models\OutboundShipmentItem $outbound_shipment_item
     * @return \App\Http\Resources\OutboundShipmentItemResource
     */
    public function show(OutboundShipmentItem $outbound_shipment_item)
    {
        return new OutboundShipmentItemResource($outbound_shipment_item);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $outbound_shipment_items = OutboundShipmentItem::filter()->simplePaginate();

        return SelectResource::collection($outbound_shipment_items);
    }
}

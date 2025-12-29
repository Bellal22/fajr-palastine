<?php

namespace App\Http\Controllers\Api;

use App\Models\OutboundShipment;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\OutboundShipmentResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OutboundShipmentController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the outbound_shipments.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $outbound_shipments = OutboundShipment::filter()->simplePaginate();

        return OutboundShipmentResource::collection($outbound_shipments);
    }

    /**
     * Display the specified outbound_shipment.
     *
     * @param \App\Models\OutboundShipment $outbound_shipment
     * @return \App\Http\Resources\OutboundShipmentResource
     */
    public function show(OutboundShipment $outbound_shipment)
    {
        return new OutboundShipmentResource($outbound_shipment);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $outbound_shipments = OutboundShipment::filter()->simplePaginate();

        return SelectResource::collection($outbound_shipments);
    }

    public function exportPdf(OutboundShipment $outboundShipment)
    {
        $this->authorize('view', $outboundShipment);

        $outboundShipment->load(['project', 'subWarehouse', 'items.shippable']);

        $pdf = Pdf::loadView('dashboard.outbound_shipments.pdf', compact('outboundShipment'));

        return $pdf->download('outbound-shipment-' . $outboundShipment->shipment_number . '.pdf');
    }
}
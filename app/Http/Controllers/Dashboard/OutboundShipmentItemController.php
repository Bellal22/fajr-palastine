<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\OutboundShipmentItem;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\OutboundShipmentItemRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OutboundShipmentItemController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * OutboundShipmentItemController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(OutboundShipmentItem::class, 'outbound_shipment_item');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $outbound_shipment_items = OutboundShipmentItem::filter()->latest()->paginate();

        return view('dashboard.outbound_shipment_items.index', compact('outbound_shipment_items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.outbound_shipment_items.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\OutboundShipmentItemRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(OutboundShipmentItemRequest $request)
    {
        $outbound_shipment_item = OutboundShipmentItem::create($request->all());

        flash()->success(trans('outbound_shipment_items.messages.created'));

        return redirect()->route('dashboard.outbound_shipment_items.show', $outbound_shipment_item);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\OutboundShipmentItem $outbound_shipment_item
     * @return \Illuminate\Http\Response
     */
    public function show(OutboundShipmentItem $outbound_shipment_item)
    {
        return view('dashboard.outbound_shipment_items.show', compact('outbound_shipment_item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\OutboundShipmentItem $outbound_shipment_item
     * @return \Illuminate\Http\Response
     */
    public function edit(OutboundShipmentItem $outbound_shipment_item)
    {
        return view('dashboard.outbound_shipment_items.edit', compact('outbound_shipment_item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\OutboundShipmentItemRequest $request
     * @param \App\Models\OutboundShipmentItem $outbound_shipment_item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(OutboundShipmentItemRequest $request, OutboundShipmentItem $outbound_shipment_item)
    {
        $outbound_shipment_item->update($request->all());

        flash()->success(trans('outbound_shipment_items.messages.updated'));

        return redirect()->route('dashboard.outbound_shipment_items.show', $outbound_shipment_item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\OutboundShipmentItem $outbound_shipment_item
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(OutboundShipmentItem $outbound_shipment_item)
    {
        $outbound_shipment_item->delete();

        flash()->success(trans('outbound_shipment_items.messages.deleted'));

        return redirect()->route('dashboard.outbound_shipment_items.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', OutboundShipmentItem::class);

        $outbound_shipment_items = OutboundShipmentItem::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.outbound_shipment_items.trashed', compact('outbound_shipment_items'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\OutboundShipmentItem $outbound_shipment_item
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(OutboundShipmentItem $outbound_shipment_item)
    {
        $this->authorize('viewTrash', $outbound_shipment_item);

        return view('dashboard.outbound_shipment_items.show', compact('outbound_shipment_item'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\OutboundShipmentItem $outbound_shipment_item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(OutboundShipmentItem $outbound_shipment_item)
    {
        $this->authorize('restore', $outbound_shipment_item);

        $outbound_shipment_item->restore();

        flash()->success(trans('outbound_shipment_items.messages.restored'));

        return redirect()->route('dashboard.outbound_shipment_items.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\OutboundShipmentItem $outbound_shipment_item
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(OutboundShipmentItem $outbound_shipment_item)
    {
        $this->authorize('forceDelete', $outbound_shipment_item);

        $outbound_shipment_item->forceDelete();

        flash()->success(trans('outbound_shipment_items.messages.deleted'));

        return redirect()->route('dashboard.outbound_shipment_items.trashed');
    }
}

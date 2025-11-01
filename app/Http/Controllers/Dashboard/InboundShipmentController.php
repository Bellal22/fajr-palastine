<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\InboundShipment;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\InboundShipmentRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InboundShipmentController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * InboundShipmentController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(InboundShipment::class, 'inbound_shipment');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inbound_shipments = InboundShipment::filter()->latest()->paginate();

        return view('dashboard.inbound_shipments.index', compact('inbound_shipments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.inbound_shipments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\InboundShipmentRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(InboundShipmentRequest $request)
    {
        $inbound_shipment = InboundShipment::create($request->validated());

        flash()->success(trans('inbound_shipments.messages.created'));

        return redirect()->route('dashboard.inbound_shipments.show', $inbound_shipment);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\InboundShipment $inbound_shipment
     * @return \Illuminate\Http\Response
     */
    public function show(InboundShipment $inbound_shipment)
    {
        return view('dashboard.inbound_shipments.show', compact('inbound_shipment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\InboundShipment $inbound_shipment
     * @return \Illuminate\Http\Response
     */
    public function edit(InboundShipment $inbound_shipment)
    {
        return view('dashboard.inbound_shipments.edit', compact('inbound_shipment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\InboundShipmentRequest $request
     * @param \App\Models\InboundShipment $inbound_shipment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(InboundShipmentRequest $request, InboundShipment $inbound_shipment)
    {
        $inbound_shipment->update($request->validated());

        flash()->success(trans('inbound_shipments.messages.updated'));

        return redirect()->route('dashboard.inbound_shipments.show', $inbound_shipment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\InboundShipment $inbound_shipment
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(InboundShipment $inbound_shipment)
    {
        $inbound_shipment->delete();

        flash()->success(trans('inbound_shipments.messages.deleted'));

        return redirect()->route('dashboard.inbound_shipments.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', InboundShipment::class);

        $inbound_shipments = InboundShipment::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.inbound_shipments.trashed', compact('inbound_shipments'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\InboundShipment $inbound_shipment
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(InboundShipment $inbound_shipment)
    {
        $this->authorize('viewTrash', $inbound_shipment);

        return view('dashboard.inbound_shipments.show', compact('inbound_shipment'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\InboundShipment $inbound_shipment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(InboundShipment $inbound_shipment)
    {
        $this->authorize('restore', $inbound_shipment);

        $inbound_shipment->restore();

        flash()->success(trans('inbound_shipments.messages.restored'));

        return redirect()->route('dashboard.inbound_shipments.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\InboundShipment $inbound_shipment
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(InboundShipment $inbound_shipment)
    {
        $this->authorize('forceDelete', $inbound_shipment);

        $inbound_shipment->forceDelete();

        flash()->success(trans('inbound_shipments.messages.deleted'));

        return redirect()->route('dashboard.inbound_shipments.trashed');
    }
}

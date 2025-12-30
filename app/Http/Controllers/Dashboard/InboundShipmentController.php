<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\InboundShipment;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\InboundShipmentRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Mpdf\Mpdf;

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
        $shipment_number = 'IS-' . date('YmdHis') . '-' . rand(1000, 9999);
        return view('dashboard.inbound_shipments.create', compact('shipment_number'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\InboundShipmentRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(InboundShipmentRequest $request)
    {
        $inboundShipment = InboundShipment::create([
            'supplier_id' => $request->supplier_id,
            'shipment_number' => $request->shipment_number,
            'inbound_type' => $request->inbound_type,
            'notes' => $request->notes,
        ]);
        // إذا كان النوع "صنف مفرد"
        if ($request->inbound_type === 'single_item' && $request->has('items')) {
            foreach ($request->items as $itemData) {
                $inboundShipment->items()->create([
                    'name' => $itemData['name'],
                    'description' => $itemData['description'] ?? null,
                    'weight' => $itemData['weight'] ?? null,
                    'quantity' => $itemData['quantity'],
                ]);
            }
        }
        // إذا كان النوع "طرد جاهز"
        if ($request->inbound_type === 'ready_package' && $request->has('packages')) {
            foreach ($request->packages as $packageData) {
                $package = $inboundShipment->readyPackages()->create([
                    'name' => $packageData['name'],
                    'description' => $packageData['description'] ?? null,
                    'weight' => $packageData['weight'] ?? null,
                    'quantity' => $packageData['quantity'] ?? 1,
                ]);
                // إذا كان للطرد محتويات
                if (isset($packageData['contents'])) {
                    foreach ($packageData['contents'] as $content) {
                        $package->contents()->create([
                            'item_id' => $content['item_id'],
                            'quantity' => $content['quantity'],
                        ]);
                    }
                }
            }
        }
        flash()->success(trans('inbound_shipments.messages.created'));
        return redirect()->route('dashboard.inbound_shipments.show', $inboundShipment);
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

    /**
     * Export inbound shipment as PDF.
     *
     * @param \App\Models\InboundShipment $inbound_shipment
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(InboundShipment $inbound_shipment)
    {
        $this->authorize('view', $inbound_shipment);

        $inbound_shipment->load(['supplier', 'items', 'readyPackages']);

        $html = view('dashboard.inbound_shipments.pdf', compact('inbound_shipment'))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'default_font' => 'dejavusans'
        ]);

        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $mpdf->WriteHTML($html);

        return $mpdf->Output('inbound-shipment-' . $inbound_shipment->shipment_number . '.pdf', 'D');
    }
}

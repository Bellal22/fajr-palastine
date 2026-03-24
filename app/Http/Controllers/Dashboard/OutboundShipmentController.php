<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\OutboundShipment;
use App\Models\OutboundShipmentItem;
use App\Models\ReadyPackage;
use App\Models\InternalPackage;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Dashboard\OutboundShipmentRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Barryvdh\DomPDF\Facade\Pdf;
use Mpdf\Mpdf;

class OutboundShipmentController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * OutboundShipmentController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(OutboundShipment::class, 'outbound_shipment');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $outbound_shipments = OutboundShipment::filter()->latest()->paginate();

        return view('dashboard.outbound_shipments.index', compact('outbound_shipments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.outbound_shipments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\OutboundShipmentRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(OutboundShipmentRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $outbound_shipment = OutboundShipment::create([
                'shipment_number' => $request->shipment_number,
                'project_id' => $request->project_id,
                'sub_warehouse_id' => $request->sub_warehouse_id,
                'notes' => $request->notes,
                'driver_name' => $request->driver_name,
            ]);

            // إضافة بنود الإرسالية
            if ($request->has('shipment_items')) {
                foreach ($request->shipment_items as $item) {
                    $shippableType = $item['type'] === 'ready_package'
                        ? \App\Models\ReadyPackage::class
                        : \App\Models\InternalPackage::class;

                    $shippable = $shippableType::find($item['package_id']);
                    
                    if (!$shippable || $shippable->remaining_quantity < $item['quantity']) {
                        $name = $shippable ? $shippable->name : ($item['package_id']);
                        throw new \Exception("الكمية المطلوبة من ($name) غير متوفرة. الرصيد المتاح: " . ($shippable ? $shippable->remaining_quantity : 0));
                    }

                    $outbound_shipment->items()->create([
                        'shippable_type' => $shippableType,
                        'shippable_id' => $item['package_id'],
                        'quantity' => $item['quantity'],
                        'weight' => $item['weight'] ?? null,
                    ]);

                    // Deduct from stock
                    $shippable->decrement('remaining_quantity', $item['quantity']);
                }
            }

            flash()->success(trans('outbound_shipments.messages.created'));

            return redirect()->route('dashboard.outbound_shipments.show', $outbound_shipment);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\OutboundShipment $outbound_shipment
     * @return \Illuminate\Http\Response
     */
    public function show(OutboundShipment $outbound_shipment)
    {
        return view('dashboard.outbound_shipments.show', compact('outbound_shipment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\OutboundShipment $outbound_shipment
     * @return \Illuminate\Http\Response
     */
    public function edit(OutboundShipment $outbound_shipment)
    {
        return view('dashboard.outbound_shipments.edit', compact('outbound_shipment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\OutboundShipmentRequest $request
     * @param \App\Models\OutboundShipment $outbound_shipment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(OutboundShipmentRequest $request, OutboundShipment $outbound_shipment)
    {
        try {
            return DB::transaction(function () use ($request, $outbound_shipment) {
                $outbound_shipment->update([
                    'shipment_number' => $request->shipment_number,
                    'project_id' => $request->project_id,
                    'sub_warehouse_id' => $request->sub_warehouse_id,
                    'notes' => $request->notes,
                    'driver_name' => $request->driver_name,
                ]);

                // Restore old stock before deleting items
                foreach ($outbound_shipment->items as $oldItem) {
                    if ($oldItem->shippable) {
                        $oldItem->shippable->increment('remaining_quantity', $oldItem->quantity);
                    }
                }

                // حذف البنود القديمة وإضافة الجديدة
                $outbound_shipment->items()->delete();

                if ($request->has('shipment_items')) {
                    foreach ($request->shipment_items as $item) {
                        $shippableType = $item['type'] === 'ready_package'
                            ? \App\Models\ReadyPackage::class
                            : \App\Models\InternalPackage::class;

                        $shippable = $shippableType::find($item['package_id']);

                        if (!$shippable || $shippable->remaining_quantity < $item['quantity']) {
                            $name = $shippable ? $shippable->name : ($item['package_id']);
                            throw new \Exception("الكمية المطلوبة من ($name) غير متوفرة. الرصيد المتاح: " . ($shippable ? $shippable->remaining_quantity : 0));
                        }

                        $outbound_shipment->items()->create([
                            'shippable_type' => $shippableType,
                            'shippable_id' => $item['package_id'],
                            'quantity' => $item['quantity'],
                            'weight' => $item['weight'] ?? null,
                        ]);

                        // Deduct from new stock
                        $shippable->decrement('remaining_quantity', $item['quantity']);
                    }
                }

                flash()->success(trans('outbound_shipments.messages.updated'));

                return redirect()->route('dashboard.outbound_shipments.show', $outbound_shipment);
            });
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['shipment_items' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\OutboundShipment $outbound_shipment
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(OutboundShipment $outbound_shipment)
    {
        return DB::transaction(function () use ($outbound_shipment) {
            // Restore stock before soft deleting
            foreach ($outbound_shipment->items as $item) {
                if ($item->shippable) {
                    $item->shippable->increment('remaining_quantity', $item->quantity);
                }
            }

            $outbound_shipment->delete();

            flash()->success(trans('outbound_shipments.messages.deleted'));

            return redirect()->route('dashboard.outbound_shipments.index');
        });
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', OutboundShipment::class);

        $outbound_shipments = OutboundShipment::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.outbound_shipments.trashed', compact('outbound_shipments'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\OutboundShipment $outbound_shipment
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(OutboundShipment $outbound_shipment)
    {
        $this->authorize('viewTrash', $outbound_shipment);

        return view('dashboard.outbound_shipments.show', compact('outbound_shipment'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\OutboundShipment $outbound_shipment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(OutboundShipment $outbound_shipment)
    {
        $this->authorize('restore', $outbound_shipment);

        try {
            return DB::transaction(function () use ($outbound_shipment) {
                // Deduct stock when restoring (and validate!)
                foreach ($outbound_shipment->items as $item) {
                    if ($item->shippable) {
                        if ($item->shippable->remaining_quantity < $item->quantity) {
                            throw new \Exception("لا يمكن استعادة الإرسالية: الكمية المطلوبة من (" . $item->shippable->name . ") غير متوفرة حالياً.");
                        }
                        $item->shippable->decrement('remaining_quantity', $item->quantity);
                    }
                }

                $outbound_shipment->restore();

                flash()->success(trans('outbound_shipments.messages.restored'));

                return redirect()->route('dashboard.outbound_shipments.trashed');
            });
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
            return back();
        }
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\OutboundShipment $outbound_shipment
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(OutboundShipment $outbound_shipment)
    {
        $this->authorize('forceDelete', $outbound_shipment);

        $outbound_shipment->forceDelete();

        flash()->success(trans('outbound_shipments.messages.deleted'));

        return redirect()->route('dashboard.outbound_shipments.trashed');
    }

    /**
     * Export outbound shipment as PDF.
     *
     * @param \App\Models\OutboundShipment $outbound_shipment
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(OutboundShipment $outbound_shipment)
    {
        $this->authorize('view', $outbound_shipment);

        $outbound_shipment->load(['project', 'subWarehouse', 'items.shippable']);

        $html = view('dashboard.outbound_shipments.pdf', compact('outbound_shipment'))->render();

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

        return $mpdf->Output('outbound-shipment-' . $outbound_shipment->shipment_number . '.pdf', 'D');
    }
}
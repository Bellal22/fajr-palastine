<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\SubWarehouse;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\SubWarehouseRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SubWarehouseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * SubWarehouseController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(SubWarehouse::class, 'sub_warehouse');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sub_warehouses = SubWarehouse::filter()->latest()->paginate();

        return view('dashboard.sub_warehouses.index', compact('sub_warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.sub_warehouses.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\SubWarehouseRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SubWarehouseRequest $request)
    {
        $sub_warehouse = SubWarehouse::create($request->all());

        flash()->success(trans('sub_warehouses.messages.created'));

        return redirect()->route('dashboard.sub_warehouses.show', $sub_warehouse);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\SubWarehouse $sub_warehouse
     * @return \Illuminate\Http\Response
     */
    public function show(SubWarehouse $sub_warehouse)
    {
        return view('dashboard.sub_warehouses.show', compact('sub_warehouse'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\SubWarehouse $sub_warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(SubWarehouse $sub_warehouse)
    {
        return view('dashboard.sub_warehouses.edit', compact('sub_warehouse'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\SubWarehouseRequest $request
     * @param \App\Models\SubWarehouse $sub_warehouse
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SubWarehouseRequest $request, SubWarehouse $sub_warehouse)
    {
        $sub_warehouse->update($request->all());

        flash()->success(trans('sub_warehouses.messages.updated'));

        return redirect()->route('dashboard.sub_warehouses.show', $sub_warehouse);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\SubWarehouse $sub_warehouse
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(SubWarehouse $sub_warehouse)
    {
        $sub_warehouse->delete();

        flash()->success(trans('sub_warehouses.messages.deleted'));

        return redirect()->route('dashboard.sub_warehouses.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', SubWarehouse::class);

        $sub_warehouses = SubWarehouse::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.sub_warehouses.trashed', compact('sub_warehouses'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\SubWarehouse $sub_warehouse
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(SubWarehouse $sub_warehouse)
    {
        $this->authorize('viewTrash', $sub_warehouse);

        return view('dashboard.sub_warehouses.show', compact('sub_warehouse'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\SubWarehouse $sub_warehouse
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(SubWarehouse $sub_warehouse)
    {
        $this->authorize('restore', $sub_warehouse);

        $sub_warehouse->restore();

        flash()->success(trans('sub_warehouses.messages.restored'));

        return redirect()->route('dashboard.sub_warehouses.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\SubWarehouse $sub_warehouse
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(SubWarehouse $sub_warehouse)
    {
        $this->authorize('forceDelete', $sub_warehouse);

        $sub_warehouse->forceDelete();

        flash()->success(trans('sub_warehouses.messages.deleted'));

        return redirect()->route('dashboard.sub_warehouses.trashed');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\SubWarehouse;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\SubWarehouseResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SubWarehouseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the sub_warehouses.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $sub_warehouses = SubWarehouse::filter()->simplePaginate();

        return SubWarehouseResource::collection($sub_warehouses);
    }

    /**
     * Display the specified sub_warehouse.
     *
     * @param \App\Models\SubWarehouse $sub_warehouse
     * @return \App\Http\Resources\SubWarehouseResource
     */
    public function show(SubWarehouse $sub_warehouse)
    {
        return new SubWarehouseResource($sub_warehouse);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $sub_warehouses = SubWarehouse::filter()->simplePaginate();

        return SelectResource::collection($sub_warehouses);
    }
}

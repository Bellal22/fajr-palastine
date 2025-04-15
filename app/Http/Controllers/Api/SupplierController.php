<?php

namespace App\Http\Controllers\Api;

use App\Models\Supplier;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\SupplierResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SupplierController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the suppliers.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $suppliers = Supplier::filter()->simplePaginate();

        return SupplierResource::collection($suppliers);
    }

    /**
     * Display the specified supplier.
     *
     * @param \App\Models\Supplier $supplier
     * @return \App\Http\Resources\SupplierResource
     */
    public function show(Supplier $supplier)
    {
        return new SupplierResource($supplier);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $suppliers = Supplier::filter()->simplePaginate();

        return SelectResource::collection($suppliers);
    }
}

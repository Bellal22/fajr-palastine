<?php

namespace App\Http\Controllers\Api;

use App\Models\AreaResponsible;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\AreaResponsibleResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AreaResponsibleController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the area_responsibles.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $area_responsibles = AreaResponsible::filter()->simplePaginate();

        return AreaResponsibleResource::collection($area_responsibles);
    }

    /**
     * Display the specified area_responsible.
     *
     * @param \App\Models\AreaResponsible $area_responsible
     * @return \App\Http\Resources\AreaResponsibleResource
     */
    public function show(AreaResponsible $area_responsible)
    {
        return new AreaResponsibleResource($area_responsible);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $area_responsibles = AreaResponsible::filter()->simplePaginate();

        return SelectResource::collection($area_responsibles);
    }
}

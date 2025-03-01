<?php

namespace App\Http\Controllers\Api;

use App\Models\Neighborhood;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\NeighborhoodResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NeighborhoodController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the neighborhoods.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $neighborhoods = Neighborhood::filter()->simplePaginate();

        return NeighborhoodResource::collection($neighborhoods);
    }

    /**
     * Display the specified neighborhood.
     *
     * @param \App\Models\Neighborhood $neighborhood
     * @return \App\Http\Resources\NeighborhoodResource
     */
    public function show(Neighborhood $neighborhood)
    {
        return new NeighborhoodResource($neighborhood);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $neighborhoods = Neighborhood::filter()->simplePaginate();

        return SelectResource::collection($neighborhoods);
    }
}

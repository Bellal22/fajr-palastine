<?php

namespace App\Http\Controllers\Api;

use App\Models\Location;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\LocationResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LocationController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the locations.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $locations = Location::filter()->simplePaginate();

        return LocationResource::collection($locations);
    }

    /**
     * Display the specified location.
     *
     * @param \App\Models\Location $location
     * @return \App\Http\Resources\LocationResource
     */
    public function show(Location $location)
    {
        return new LocationResource($location);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $locations = Location::filter()->simplePaginate();

        return SelectResource::collection($locations);
    }
}

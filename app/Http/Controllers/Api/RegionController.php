<?php

namespace App\Http\Controllers\Api;

use App\Models\Region;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\RegionResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RegionController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the regions.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $regions = Region::filter()->simplePaginate();

        return RegionResource::collection($regions);
    }

    /**
     * Display the specified region.
     *
     * @param \App\Models\Region $region
     * @return \App\Http\Resources\RegionResource
     */
    public function show(Region $region)
    {
        return new RegionResource($region);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $regions = Region::filter()->simplePaginate();

        return SelectResource::collection($regions);
    }
}

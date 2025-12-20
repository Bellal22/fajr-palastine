<?php

namespace App\Http\Controllers\Api;

use App\Models\Map;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\MapResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MapController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the maps.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $maps = Map::filter()->simplePaginate();

        return MapResource::collection($maps);
    }

    /**
     * Display the specified map.
     *
     * @param \App\Models\Map $map
     * @return \App\Http\Resources\MapResource
     */
    public function show(Map $map)
    {
        return new MapResource($map);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $maps = Map::filter()->simplePaginate();

        return SelectResource::collection($maps);
    }
}

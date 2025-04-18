<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\CityResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CityController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the cities.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $cities = City::filter()->simplePaginate();

        return CityResource::collection($cities);
    }

    /**
     * Display the specified city.
     *
     * @param \App\Models\City $city
     * @return \App\Http\Resources\CityResource
     */
    public function show(City $city)
    {
        return new CityResource($city);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $cities = City::filter()->simplePaginate();

        return SelectResource::collection($cities);
    }
}

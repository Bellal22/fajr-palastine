<?php

namespace App\Http\Controllers\Api;

use App\Models\SubCity;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\SubCityResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SubCityController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the sub_cities.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $sub_cities = SubCity::filter()->simplePaginate();

        return SubCityResource::collection($sub_cities);
    }

    /**
     * Display the specified sub_city.
     *
     * @param \App\Models\SubCity $sub_city
     * @return \App\Http\Resources\SubCityResource
     */
    public function show(SubCity $sub_city)
    {
        return new SubCityResource($sub_city);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $sub_cities = SubCity::filter()->simplePaginate();

        return SelectResource::collection($sub_cities);
    }
}

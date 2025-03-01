<?php

namespace App\Http\Controllers\Api;

use App\Models\Family;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\FamilyResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FamilyController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the families.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $families = Family::filter()->simplePaginate();

        return FamilyResource::collection($families);
    }

    /**
     * Display the specified family.
     *
     * @param \App\Models\Family $family
     * @return \App\Http\Resources\FamilyResource
     */
    public function show(Family $family)
    {
        return new FamilyResource($family);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $families = Family::filter()->simplePaginate();

        return SelectResource::collection($families);
    }
}

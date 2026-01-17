<?php

namespace App\Http\Controllers\Api;

use App\Models\Choose;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\ChooseResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ChooseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the chooses.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $chooses = Choose::filter()->simplePaginate();

        return ChooseResource::collection($chooses);
    }

    /**
     * Display the specified choose.
     *
     * @param \App\Models\Choose $choose
     * @return \App\Http\Resources\ChooseResource
     */
    public function show(Choose $choose)
    {
        return new ChooseResource($choose);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $chooses = Choose::filter()->simplePaginate();

        return SelectResource::collection($chooses);
    }
}

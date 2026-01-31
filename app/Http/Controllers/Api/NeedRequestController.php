<?php

namespace App\Http\Controllers\Api;

use App\Models\NeedRequest;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\NeedRequestResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NeedRequestController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the need_requests.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $need_requests = NeedRequest::filter()->simplePaginate();

        return NeedRequestResource::collection($need_requests);
    }

    /**
     * Display the specified need_request.
     *
     * @param \App\Models\NeedRequest $need_request
     * @return \App\Http\Resources\NeedRequestResource
     */
    public function show(NeedRequest $need_request)
    {
        return new NeedRequestResource($need_request);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $need_requests = NeedRequest::filter()->simplePaginate();

        return SelectResource::collection($need_requests);
    }
}

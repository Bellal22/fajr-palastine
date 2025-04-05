<?php

namespace App\Http\Controllers\Api;

use App\Models\Complaint;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\ComplaintResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ComplaintController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the complaints.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $complaints = Complaint::filter()->simplePaginate();

        return ComplaintResource::collection($complaints);
    }

    /**
     * Display the specified complaint.
     *
     * @param \App\Models\Complaint $complaint
     * @return \App\Http\Resources\ComplaintResource
     */
    public function show(Complaint $complaint)
    {
        return new ComplaintResource($complaint);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $complaints = Complaint::filter()->simplePaginate();

        return SelectResource::collection($complaints);
    }
}

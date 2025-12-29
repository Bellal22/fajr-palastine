<?php

namespace App\Http\Controllers\Api;

use App\Models\InternalPackage;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\InternalPackageResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InternalPackageController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the internal_packages.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $internal_packages = InternalPackage::filter()->simplePaginate();

        return InternalPackageResource::collection($internal_packages);
    }

    /**
     * Display the specified internal_package.
     *
     * @param \App\Models\InternalPackage $internal_package
     * @return \App\Http\Resources\InternalPackageResource
     */
    public function show(InternalPackage $internal_package)
    {
        return new InternalPackageResource($internal_package);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $internal_packages = InternalPackage::filter()->simplePaginate();

        return SelectResource::collection($internal_packages);
    }
}

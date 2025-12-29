<?php

namespace App\Http\Controllers\Api;

use App\Models\ReadyPackage;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\ReadyPackageResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReadyPackageController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the ready_packages.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $ready_packages = ReadyPackage::filter()->simplePaginate();

        return ReadyPackageResource::collection($ready_packages);
    }

    /**
     * Display the specified ready_package.
     *
     * @param \App\Models\ReadyPackage $ready_package
     * @return \App\Http\Resources\ReadyPackageResource
     */
    public function show(ReadyPackage $ready_package)
    {
        return new ReadyPackageResource($ready_package);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $ready_packages = ReadyPackage::filter()->simplePaginate();

        return SelectResource::collection($ready_packages);
    }
}

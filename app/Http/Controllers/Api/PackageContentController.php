<?php

namespace App\Http\Controllers\Api;

use App\Models\PackageContent;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\PackageContentResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PackageContentController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the package_contents.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $package_contents = PackageContent::filter()->simplePaginate();

        return PackageContentResource::collection($package_contents);
    }

    /**
     * Display the specified package_content.
     *
     * @param \App\Models\PackageContent $package_content
     * @return \App\Http\Resources\PackageContentResource
     */
    public function show(PackageContent $package_content)
    {
        return new PackageContentResource($package_content);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $package_contents = PackageContent::filter()->simplePaginate();

        return SelectResource::collection($package_contents);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\CouponType;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\CouponTypeResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CouponTypeController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the coupon_types.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $coupon_types = CouponType::filter()->simplePaginate();

        return CouponTypeResource::collection($coupon_types);
    }

    /**
     * Display the specified coupon_type.
     *
     * @param \App\Models\CouponType $coupon_type
     * @return \App\Http\Resources\CouponTypeResource
     */
    public function show(CouponType $coupon_type)
    {
        return new CouponTypeResource($coupon_type);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $coupon_types = CouponType::filter()->simplePaginate();

        return SelectResource::collection($coupon_types);
    }
}

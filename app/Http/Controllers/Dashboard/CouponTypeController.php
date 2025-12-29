<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\CouponType;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\CouponTypeRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CouponTypeController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * CouponTypeController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(CouponType::class, 'coupon_type');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coupon_types = CouponType::filter()->latest()->paginate();

        return view('dashboard.coupon_types.index', compact('coupon_types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.coupon_types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\CouponTypeRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CouponTypeRequest $request)
    {
        $coupon_type = CouponType::create($request->all());

        flash()->success(trans('coupon_types.messages.created'));

        return redirect()->route('dashboard.coupon_types.show', $coupon_type);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\CouponType $coupon_type
     * @return \Illuminate\Http\Response
     */
    public function show(CouponType $coupon_type)
    {
        return view('dashboard.coupon_types.show', compact('coupon_type'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\CouponType $coupon_type
     * @return \Illuminate\Http\Response
     */
    public function edit(CouponType $coupon_type)
    {
        return view('dashboard.coupon_types.edit', compact('coupon_type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\CouponTypeRequest $request
     * @param \App\Models\CouponType $coupon_type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CouponTypeRequest $request, CouponType $coupon_type)
    {
        $coupon_type->update($request->all());

        flash()->success(trans('coupon_types.messages.updated'));

        return redirect()->route('dashboard.coupon_types.show', $coupon_type);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\CouponType $coupon_type
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CouponType $coupon_type)
    {
        $coupon_type->delete();

        flash()->success(trans('coupon_types.messages.deleted'));

        return redirect()->route('dashboard.coupon_types.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', CouponType::class);

        $coupon_types = CouponType::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.coupon_types.trashed', compact('coupon_types'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\CouponType $coupon_type
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(CouponType $coupon_type)
    {
        $this->authorize('viewTrash', $coupon_type);

        return view('dashboard.coupon_types.show', compact('coupon_type'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\CouponType $coupon_type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(CouponType $coupon_type)
    {
        $this->authorize('restore', $coupon_type);

        $coupon_type->restore();

        flash()->success(trans('coupon_types.messages.restored'));

        return redirect()->route('dashboard.coupon_types.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\CouponType $coupon_type
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(CouponType $coupon_type)
    {
        $this->authorize('forceDelete', $coupon_type);

        $coupon_type->forceDelete();

        flash()->success(trans('coupon_types.messages.deleted'));

        return redirect()->route('dashboard.coupon_types.trashed');
    }
}

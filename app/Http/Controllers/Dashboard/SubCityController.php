<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\SubCity;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\SubCityRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SubCityController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * SubCityController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(SubCity::class, 'sub_city');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sub_cities = SubCity::filter()->latest()->paginate();

        return view('dashboard.sub_cities.index', compact('sub_cities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.sub_cities.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\SubCityRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SubCityRequest $request)
    {
        $sub_city = SubCity::create($request->all());

        flash()->success(trans('sub_cities.messages.created'));

        return redirect()->route('dashboard.sub_cities.show', $sub_city);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\SubCity $sub_city
     * @return \Illuminate\Http\Response
     */
    public function show(SubCity $sub_city)
    {
        return view('dashboard.sub_cities.show', compact('sub_city'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\SubCity $sub_city
     * @return \Illuminate\Http\Response
     */
    public function edit(SubCity $sub_city)
    {
        return view('dashboard.sub_cities.edit', compact('sub_city'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\SubCityRequest $request
     * @param \App\Models\SubCity $sub_city
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SubCityRequest $request, SubCity $sub_city)
    {
        $sub_city->update($request->all());

        flash()->success(trans('sub_cities.messages.updated'));

        return redirect()->route('dashboard.sub_cities.show', $sub_city);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\SubCity $sub_city
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(SubCity $sub_city)
    {
        $sub_city->delete();

        flash()->success(trans('sub_cities.messages.deleted'));

        return redirect()->route('dashboard.sub_cities.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', SubCity::class);

        $sub_cities = SubCity::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.sub_cities.trashed', compact('sub_cities'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\SubCity $sub_city
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(SubCity $sub_city)
    {
        $this->authorize('viewTrash', $sub_city);

        return view('dashboard.sub_cities.show', compact('sub_city'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\SubCity $sub_city
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(SubCity $sub_city)
    {
        $this->authorize('restore', $sub_city);

        $sub_city->restore();

        flash()->success(trans('sub_cities.messages.restored'));

        return redirect()->route('dashboard.sub_cities.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\SubCity $sub_city
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(SubCity $sub_city)
    {
        $this->authorize('forceDelete', $sub_city);

        $sub_city->forceDelete();

        flash()->success(trans('sub_cities.messages.deleted'));

        return redirect()->route('dashboard.sub_cities.trashed');
    }
}

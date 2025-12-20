<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Location;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\LocationRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LocationController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * LocationController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Location::class, 'location');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locations = Location::filter()->latest()->paginate();

        return view('dashboard.locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.locations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\LocationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LocationRequest $request)
    {
        $location = Location::create($request->all());

        flash()->success(trans('locations.messages.created'));

        return redirect()->route('dashboard.locations.show', $location);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Location $location
     * @return \Illuminate\Http\Response
     */
    public function show(Location $location)
    {
        return view('dashboard.locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Location $location
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {
        return view('dashboard.locations.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\LocationRequest $request
     * @param \App\Models\Location $location
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(LocationRequest $request, Location $location)
    {
        $location->update($request->all());

        flash()->success(trans('locations.messages.updated'));

        return redirect()->route('dashboard.locations.show', $location);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Location $location
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Location $location)
    {
        $location->delete();

        flash()->success(trans('locations.messages.deleted'));

        return redirect()->route('dashboard.locations.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Location::class);

        $locations = Location::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.locations.trashed', compact('locations'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\Location $location
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(Location $location)
    {
        $this->authorize('viewTrash', $location);

        return view('dashboard.locations.show', compact('location'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\Location $location
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Location $location)
    {
        $this->authorize('restore', $location);

        $location->restore();

        flash()->success(trans('locations.messages.restored'));

        return redirect()->route('dashboard.locations.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\Location $location
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Location $location)
    {
        $this->authorize('forceDelete', $location);

        $location->forceDelete();

        flash()->success(trans('locations.messages.deleted'));

        return redirect()->route('dashboard.locations.trashed');
    }
}

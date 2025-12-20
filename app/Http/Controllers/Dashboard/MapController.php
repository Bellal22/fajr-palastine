<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Map;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\MapRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MapController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * MapController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Map::class, 'map');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $maps = Map::filter()->latest()->paginate();

        return view('dashboard.maps.index', compact('maps'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.maps.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\MapRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(MapRequest $request)
    {
        $map = Map::create($request->all());

        flash()->success(trans('maps.messages.created'));

        return redirect()->route('dashboard.maps.show', $map);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Map $map
     * @return \Illuminate\Http\Response
     */
    public function show(Map $map)
    {
        return view('dashboard.maps.show', compact('map'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Map $map
     * @return \Illuminate\Http\Response
     */
    public function edit(Map $map)
    {
        return view('dashboard.maps.edit', compact('map'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\MapRequest $request
     * @param \App\Models\Map $map
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(MapRequest $request, Map $map)
    {
        $map->update($request->all());

        flash()->success(trans('maps.messages.updated'));

        return redirect()->route('dashboard.maps.show', $map);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Map $map
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Map $map)
    {
        $map->delete();

        flash()->success(trans('maps.messages.deleted'));

        return redirect()->route('dashboard.maps.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Map::class);

        $maps = Map::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.maps.trashed', compact('maps'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\Map $map
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(Map $map)
    {
        $this->authorize('viewTrash', $map);

        return view('dashboard.maps.show', compact('map'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\Map $map
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Map $map)
    {
        $this->authorize('restore', $map);

        $map->restore();

        flash()->success(trans('maps.messages.restored'));

        return redirect()->route('dashboard.maps.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\Map $map
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Map $map)
    {
        $this->authorize('forceDelete', $map);

        $map->forceDelete();

        flash()->success(trans('maps.messages.deleted'));

        return redirect()->route('dashboard.maps.trashed');
    }
}
<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Neighborhood;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\NeighborhoodRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NeighborhoodController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * NeighborhoodController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Neighborhood::class, 'neighborhood');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $neighborhoods = Neighborhood::filter()->latest()->paginate();

        return view('dashboard.neighborhoods.index', compact('neighborhoods'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.neighborhoods.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\NeighborhoodRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(NeighborhoodRequest $request)
    {
        $neighborhood = Neighborhood::create($request->all());

        flash()->success(trans('neighborhoods.messages.created'));

        return redirect()->route('dashboard.neighborhoods.show', $neighborhood);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Neighborhood $neighborhood
     * @return \Illuminate\Http\Response
     */
    public function show(Neighborhood $neighborhood)
    {
        return view('dashboard.neighborhoods.show', compact('neighborhood'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Neighborhood $neighborhood
     * @return \Illuminate\Http\Response
     */
    public function edit(Neighborhood $neighborhood)
    {
        return view('dashboard.neighborhoods.edit', compact('neighborhood'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\NeighborhoodRequest $request
     * @param \App\Models\Neighborhood $neighborhood
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(NeighborhoodRequest $request, Neighborhood $neighborhood)
    {
        $neighborhood->update($request->all());

        flash()->success(trans('neighborhoods.messages.updated'));

        return redirect()->route('dashboard.neighborhoods.show', $neighborhood);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Neighborhood $neighborhood
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Neighborhood $neighborhood)
    {
        $neighborhood->delete();

        flash()->success(trans('neighborhoods.messages.deleted'));

        return redirect()->route('dashboard.neighborhoods.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Neighborhood::class);

        $neighborhoods = Neighborhood::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.neighborhoods.trashed', compact('neighborhoods'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\Neighborhood $neighborhood
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(Neighborhood $neighborhood)
    {
        $this->authorize('viewTrash', $neighborhood);

        return view('dashboard.neighborhoods.show', compact('neighborhood'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\Neighborhood $neighborhood
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Neighborhood $neighborhood)
    {
        $this->authorize('restore', $neighborhood);

        $neighborhood->restore();

        flash()->success(trans('neighborhoods.messages.restored'));

        return redirect()->route('dashboard.neighborhoods.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\Neighborhood $neighborhood
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Neighborhood $neighborhood)
    {
        $this->authorize('forceDelete', $neighborhood);

        $neighborhood->forceDelete();

        flash()->success(trans('neighborhoods.messages.deleted'));

        return redirect()->route('dashboard.neighborhoods.trashed');
    }
}

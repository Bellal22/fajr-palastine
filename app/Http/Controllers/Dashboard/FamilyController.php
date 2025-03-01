<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Family;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\FamilyRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FamilyController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * FamilyController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Family::class, 'family');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $families = Family::filter()->latest()->paginate();

        return view('dashboard.families.index', compact('families'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.families.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\FamilyRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(FamilyRequest $request)
    {
        $family = Family::create($request->all());

        flash()->success(trans('families.messages.created'));

        return redirect()->route('dashboard.families.show', $family);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Family $family
     * @return \Illuminate\Http\Response
     */
    public function show(Family $family)
    {
        return view('dashboard.families.show', compact('family'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Family $family
     * @return \Illuminate\Http\Response
     */
    public function edit(Family $family)
    {
        return view('dashboard.families.edit', compact('family'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\FamilyRequest $request
     * @param \App\Models\Family $family
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(FamilyRequest $request, Family $family)
    {
        $family->update($request->all());

        flash()->success(trans('families.messages.updated'));

        return redirect()->route('dashboard.families.show', $family);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Family $family
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Family $family)
    {
        $family->delete();

        flash()->success(trans('families.messages.deleted'));

        return redirect()->route('dashboard.families.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Family::class);

        $families = Family::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.families.trashed', compact('families'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\Family $family
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(Family $family)
    {
        $this->authorize('viewTrash', $family);

        return view('dashboard.families.show', compact('family'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\Family $family
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Family $family)
    {
        $this->authorize('restore', $family);

        $family->restore();

        flash()->success(trans('families.messages.restored'));

        return redirect()->route('dashboard.families.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\Family $family
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Family $family)
    {
        $this->authorize('forceDelete', $family);

        $family->forceDelete();

        flash()->success(trans('families.messages.deleted'));

        return redirect()->route('dashboard.families.trashed');
    }
}

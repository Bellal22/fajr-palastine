<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Researcher;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\ResearcherRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ResearcherController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * ResearcherController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Researcher::class, 'researcher');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $researchers = Researcher::filter()->latest()->paginate();

        return view('dashboard.accounts.researchers.index', compact('researchers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.accounts.researchers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\ResearcherRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ResearcherRequest $request)
    {
        $researcher = Researcher::create($request->allWithHashedPassword());

        $researcher->setType($request->type);

        if ($request->user()->isAdmin()) {
            $researcher->syncPermissions($request->input('permissions', []));
        }

        $researcher->addAllMediaFromTokens();

        flash()->success(trans('researchers.messages.created'));

        return redirect()->route('dashboard.researchers.show', $researcher);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Researcher $researcher
     * @return \Illuminate\Http\Response
     */
    public function show(Researcher $researcher)
    {
        return view('dashboard.accounts.researchers.show', compact('researcher'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Researcher $researcher
     * @return \Illuminate\Http\Response
     */
    public function edit(Researcher $researcher)
    {
        return view('dashboard.accounts.researchers.edit', compact('researcher'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\ResearcherRequest $request
     * @param \App\Models\Researcher $researcher
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ResearcherRequest $request, Researcher $researcher)
    {
        $researcher->update($request->allWithHashedPassword());

        $researcher->setType($request->type);

        if ($request->user()->isAdmin()) {
            $researcher->syncPermissions($request->input('permissions', []));
        }

        $researcher->addAllMediaFromTokens();

        flash()->success(trans('researchers.messages.updated'));

        return redirect()->route('dashboard.researchers.show', $researcher);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Researcher $researcher
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Researcher $researcher)
    {
        $researcher->delete();

        flash()->success(trans('researchers.messages.deleted'));

        return redirect()->route('dashboard.researchers.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Researcher::class);

        $researchers = Researcher::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.accounts.researchers.trashed', compact('researchers'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\Researcher $researcher
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(Researcher $researcher)
    {
        $this->authorize('viewTrash', $researcher);

        return view('dashboard.accounts.researchers.show', compact('researcher'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\Researcher $researcher
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Researcher $researcher)
    {
        $this->authorize('restore', $researcher);

        $researcher->restore();

        flash()->success(trans('researchers.messages.restored'));

        return redirect()->route('dashboard.researchers.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\Researcher $researcher
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Researcher $researcher)
    {
        $this->authorize('forceDelete', $researcher);

        $researcher->forceDelete();

        flash()->success(trans('researchers.messages.deleted'));

        return redirect()->route('dashboard.researchers.trashed');
    }
}

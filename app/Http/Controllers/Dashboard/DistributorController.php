<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Distributor;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\DistributorRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DistributorController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * DistributorController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Distributor::class, 'distributor');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $distributors = Distributor::filter()->latest()->paginate();

        return view('dashboard.accounts.distributors.index', compact('distributors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.accounts.distributors.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\DistributorRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(DistributorRequest $request)
    {
        $distributor = Distributor::create($request->allWithHashedPassword());

        $distributor->setType(Distributor::MOBILE_WORKER_TYPE);

        if ($request->user()->isAdmin()) {
            $distributor->syncUserPermissions($request->input('permissions', []));
        }

        $distributor->addAllMediaFromTokens();

        flash()->success(trans('distributors.messages.created'));

        return redirect()->route('dashboard.distributors.show', $distributor);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Distributor $distributor
     * @return \Illuminate\Http\Response
     */
    public function show(Distributor $distributor)
    {
        return view('dashboard.accounts.distributors.show', compact('distributor'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Distributor $distributor
     * @return \Illuminate\Http\Response
     */
    public function edit(Distributor $distributor)
    {
        return view('dashboard.accounts.distributors.edit', compact('distributor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\DistributorRequest $request
     * @param \App\Models\Distributor $distributor
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(DistributorRequest $request, Distributor $distributor)
    {
        $distributor->update($request->allWithHashedPassword());

        if ($request->user()->isAdmin()) {
            $distributor->syncUserPermissions($request->input('permissions', []));
        }

        $distributor->addAllMediaFromTokens();

        flash()->success(trans('distributors.messages.updated'));

        return redirect()->route('dashboard.distributors.show', $distributor);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Distributor $distributor
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Distributor $distributor)
    {
        $distributor->delete();

        flash()->success(trans('distributors.messages.deleted'));

        return redirect()->route('dashboard.distributors.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Distributor::class);

        $distributors = Distributor::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.accounts.distributors.trashed', compact('distributors'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\Distributor $distributor
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(Distributor $distributor)
    {
        $this->authorize('viewTrash', $distributor);

        return view('dashboard.accounts.distributors.show', compact('distributor'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\Distributor $distributor
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Distributor $distributor)
    {
        $this->authorize('restore', $distributor);

        $distributor->restore();

        flash()->success(trans('distributors.messages.restored'));

        return redirect()->route('dashboard.distributors.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\Distributor $distributor
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Distributor $distributor)
    {
        $this->authorize('forceDelete', $distributor);

        $distributor->forceDelete();

        flash()->success(trans('distributors.messages.deleted'));

        return redirect()->route('dashboard.distributors.trashed');
    }
}

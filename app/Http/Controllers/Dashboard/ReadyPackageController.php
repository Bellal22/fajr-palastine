<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\ReadyPackage;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\ReadyPackageRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReadyPackageController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * ReadyPackageController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(ReadyPackage::class, 'ready_package');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ready_packages = ReadyPackage::filter()->latest()->paginate();

        return view('dashboard.ready_packages.index', compact('ready_packages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.ready_packages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\ReadyPackageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ReadyPackageRequest $request)
    {
        $ready_package = ReadyPackage::create($request->all());

        flash()->success(trans('ready_packages.messages.created'));

        return redirect()->route('dashboard.ready_packages.show', $ready_package);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ReadyPackage $ready_package
     * @return \Illuminate\Http\Response
     */
    public function show(ReadyPackage $ready_package)
    {
        return view('dashboard.ready_packages.show', compact('ready_package'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\ReadyPackage $ready_package
     * @return \Illuminate\Http\Response
     */
    public function edit(ReadyPackage $ready_package)
    {
        return view('dashboard.ready_packages.edit', compact('ready_package'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\ReadyPackageRequest $request
     * @param \App\Models\ReadyPackage $ready_package
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ReadyPackageRequest $request, ReadyPackage $ready_package)
    {
        $ready_package->update($request->all());

        flash()->success(trans('ready_packages.messages.updated'));

        return redirect()->route('dashboard.ready_packages.show', $ready_package);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ReadyPackage $ready_package
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ReadyPackage $ready_package)
    {
        $ready_package->delete();

        flash()->success(trans('ready_packages.messages.deleted'));

        return redirect()->route('dashboard.ready_packages.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', ReadyPackage::class);

        $ready_packages = ReadyPackage::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.ready_packages.trashed', compact('ready_packages'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\ReadyPackage $ready_package
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(ReadyPackage $ready_package)
    {
        $this->authorize('viewTrash', $ready_package);

        return view('dashboard.ready_packages.show', compact('ready_package'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\ReadyPackage $ready_package
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(ReadyPackage $ready_package)
    {
        $this->authorize('restore', $ready_package);

        $ready_package->restore();

        flash()->success(trans('ready_packages.messages.restored'));

        return redirect()->route('dashboard.ready_packages.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\ReadyPackage $ready_package
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(ReadyPackage $ready_package)
    {
        $this->authorize('forceDelete', $ready_package);

        $ready_package->forceDelete();

        flash()->success(trans('ready_packages.messages.deleted'));

        return redirect()->route('dashboard.ready_packages.trashed');
    }
}

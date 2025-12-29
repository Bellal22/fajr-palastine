<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\PackageContent;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\PackageContentRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PackageContentController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * PackageContentController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(PackageContent::class, 'package_content');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $package_contents = PackageContent::filter()->latest()->paginate();

        return view('dashboard.package_contents.index', compact('package_contents'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.package_contents.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\PackageContentRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PackageContentRequest $request)
    {
        $package_content = PackageContent::create($request->all());

        flash()->success(trans('package_contents.messages.created'));

        return redirect()->route('dashboard.package_contents.show', $package_content);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\PackageContent $package_content
     * @return \Illuminate\Http\Response
     */
    public function show(PackageContent $package_content)
    {
        return view('dashboard.package_contents.show', compact('package_content'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\PackageContent $package_content
     * @return \Illuminate\Http\Response
     */
    public function edit(PackageContent $package_content)
    {
        return view('dashboard.package_contents.edit', compact('package_content'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\PackageContentRequest $request
     * @param \App\Models\PackageContent $package_content
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PackageContentRequest $request, PackageContent $package_content)
    {
        $package_content->update($request->all());

        flash()->success(trans('package_contents.messages.updated'));

        return redirect()->route('dashboard.package_contents.show', $package_content);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\PackageContent $package_content
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(PackageContent $package_content)
    {
        $package_content->delete();

        flash()->success(trans('package_contents.messages.deleted'));

        return redirect()->route('dashboard.package_contents.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', PackageContent::class);

        $package_contents = PackageContent::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.package_contents.trashed', compact('package_contents'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\PackageContent $package_content
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(PackageContent $package_content)
    {
        $this->authorize('viewTrash', $package_content);

        return view('dashboard.package_contents.show', compact('package_content'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\PackageContent $package_content
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(PackageContent $package_content)
    {
        $this->authorize('restore', $package_content);

        $package_content->restore();

        flash()->success(trans('package_contents.messages.restored'));

        return redirect()->route('dashboard.package_contents.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\PackageContent $package_content
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(PackageContent $package_content)
    {
        $this->authorize('forceDelete', $package_content);

        $package_content->forceDelete();

        flash()->success(trans('package_contents.messages.deleted'));

        return redirect()->route('dashboard.package_contents.trashed');
    }
}

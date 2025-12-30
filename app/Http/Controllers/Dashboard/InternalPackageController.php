<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\InternalPackage;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\InternalPackageRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InternalPackageController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * InternalPackageController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(InternalPackage::class, 'internal_package');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $internal_packages = InternalPackage::filter()->latest()->paginate();

        return view('dashboard.internal_packages.index', compact('internal_packages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.internal_packages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\InternalPackageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(InternalPackageRequest $request)
    {
        $internal_package = InternalPackage::create([
            'name' => $request->name,
            'description' => $request->description,
            'weight' => $request->weight,
            'quantity' => $request->quantity ?? 1,
            'created_by' => auth()->id(),
        ]);

        // إضافة محتويات الطرد
        if ($request->has('contents')) {
            foreach ($request->contents as $content) {
                if (!empty($content['item_id'])) {
                    $internal_package->contents()->create([
                        'item_id' => $content['item_id'],
                        'quantity' => $content['quantity'],
                    ]);
                }
            }
        }

        flash()->success(trans('internal_packages.messages.created'));

        return redirect()->route('dashboard.internal_packages.show', $internal_package);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\InternalPackage $internal_package
     * @return \Illuminate\Http\Response
     */
    public function show(InternalPackage $internal_package)
    {
        $internal_package->load(['creator', 'contents.item']);

        return view('dashboard.internal_packages.show', compact('internal_package'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\InternalPackage $internal_package
     * @return \Illuminate\Http\Response
     */
    public function edit(InternalPackage $internal_package)
    {
        return view('dashboard.internal_packages.edit', compact('internal_package'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\InternalPackageRequest $request
     * @param \App\Models\InternalPackage $internal_package
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(InternalPackageRequest $request, InternalPackage $internal_package)
    {
        $internal_package->update([
            'name' => $request->name,
            'description' => $request->description,
            'weight' => $request->weight,
            'quantity' => $request->quantity ?? 1,
        ]);

        // حذف المحتويات القديمة وإضافة الجديدة
        $internal_package->contents()->delete();

        if ($request->has('contents')) {
            foreach ($request->contents as $content) {
                if (!empty($content['item_id'])) {
                    $internal_package->contents()->create([
                        'item_id' => $content['item_id'],
                        'quantity' => $content['quantity'],
                    ]);
                }
            }
        }

        flash()->success(trans('internal_packages.messages.updated'));

        return redirect()->route('dashboard.internal_packages.show', $internal_package);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\InternalPackage $internal_package
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(InternalPackage $internal_package)
    {
        $internal_package->delete();

        flash()->success(trans('internal_packages.messages.deleted'));

        return redirect()->route('dashboard.internal_packages.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', InternalPackage::class);

        $internal_packages = InternalPackage::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.internal_packages.trashed', compact('internal_packages'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\InternalPackage $internal_package
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(InternalPackage $internal_package)
    {
        $this->authorize('viewTrash', $internal_package);

        return view('dashboard.internal_packages.show', compact('internal_package'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\InternalPackage $internal_package
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(InternalPackage $internal_package)
    {
        $this->authorize('restore', $internal_package);

        $internal_package->restore();

        flash()->success(trans('internal_packages.messages.restored'));

        return redirect()->route('dashboard.internal_packages.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\InternalPackage $internal_package
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(InternalPackage $internal_package)
    {
        $this->authorize('forceDelete', $internal_package);

        $internal_package->forceDelete();

        flash()->success(trans('internal_packages.messages.deleted'));

        return redirect()->route('dashboard.internal_packages.trashed');
    }
}

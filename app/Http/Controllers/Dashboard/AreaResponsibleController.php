<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\AreaResponsible;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\AreaResponsibleRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AreaResponsibleController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * AreaResponsibleController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(AreaResponsible::class, 'area_responsible');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $area_responsibles = AreaResponsible::filter()->withCount('neighborhoods')->latest()->paginate();

        return view('dashboard.area_responsibles.index', compact('area_responsibles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cities = \App\Models\City::has('neighborhoods')->with('neighborhoods')->get();
        return view('dashboard.area_responsibles.create', compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\AreaResponsibleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AreaResponsibleRequest $request)
    {
        $area_responsible = AreaResponsible::create($request->all());
        $area_responsible->neighborhoods()->sync($request->input('neighborhoods', []));

        flash()->success(trans('area_responsibles.messages.created'));

        return redirect()->route('dashboard.area_responsibles.show', $area_responsible);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\AreaResponsible $area_responsible
     * @return \Illuminate\Http\Response
     */
    public function show(AreaResponsible $area_responsible)
    {
        $area_responsible->load('neighborhoods.city');
        return view('dashboard.area_responsibles.show', compact('area_responsible'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\AreaResponsible $area_responsible
     * @return \Illuminate\Http\Response
     */
    public function edit(AreaResponsible $area_responsible)
    {
        $cities = \App\Models\City::has('neighborhoods')->with('neighborhoods')->get();
        $area_responsible->load('neighborhoods');
        return view('dashboard.area_responsibles.edit', compact('area_responsible', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\AreaResponsibleRequest $request
     * @param \App\Models\AreaResponsible $area_responsible
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AreaResponsibleRequest $request, AreaResponsible $area_responsible)
    {
        $area_responsible->update($request->all());
        $area_responsible->neighborhoods()->sync($request->input('neighborhoods', []));

        flash()->success(trans('area_responsibles.messages.updated'));

        return redirect()->route('dashboard.area_responsibles.show', $area_responsible);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\AreaResponsible $area_responsible
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(AreaResponsible $area_responsible)
    {
        $area_responsible->delete();

        flash()->success(trans('area_responsibles.messages.deleted'));

        return redirect()->route('dashboard.area_responsibles.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', AreaResponsible::class);

        $area_responsibles = AreaResponsible::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.area_responsibles.trashed', compact('area_responsibles'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\AreaResponsible $area_responsible
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(AreaResponsible $area_responsible)
    {
        $this->authorize('viewTrash', $area_responsible);

        return view('dashboard.area_responsibles.show', compact('area_responsible'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\AreaResponsible $area_responsible
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(AreaResponsible $area_responsible)
    {
        $this->authorize('restore', $area_responsible);

        $area_responsible->restore();

        flash()->success(trans('area_responsibles.messages.restored'));

        return redirect()->route('dashboard.area_responsibles.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\AreaResponsible $area_responsible
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(AreaResponsible $area_responsible)
    {
        $this->authorize('forceDelete', $area_responsible);

        $area_responsible->forceDelete();

        flash()->success(trans('area_responsibles.messages.deleted'));

        return redirect()->route('dashboard.area_responsibles.trashed');
    }

    public function refreshCount(AreaResponsible $areaResponsible)
    {
        try {
            $oldCount = $areaResponsible->people_count;
            $newCount = $areaResponsible->updatePeopleCount();

            return response()->json([
                'success' => true,
                'old_count' => $oldCount,
                'new_count' => $newCount,
                'message' => 'تم تحديث العدد بنجاح'
            ]);
        } catch (\Exception $e) {
            logger()->error('خطأ في تحديث عدد مسؤول المنطقة يدوياً', [
                'area_responsible_id' => $areaResponsible->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine() // إضافة معلومات السطر
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في التحديث: ' . $e->getMessage()
            ], 500);
        }
    }
}
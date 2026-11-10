<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\KitchenMonitor;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\KitchenMonitorRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class KitchenMonitorController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * KitchenMonitorController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(KitchenMonitor::class, 'kitchen_monitor');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kitchenMonitors = KitchenMonitor::filter()->latest()->paginate();

        return view('dashboard.accounts.kitchen_monitors.index', compact('kitchenMonitors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.accounts.kitchen_monitors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KitchenMonitorRequest $request)
    {
        $kitchenMonitor = KitchenMonitor::create($request->allWithHashedPassword());

        $kitchenMonitor->setType($request->type ?: KitchenMonitor::KITCHEN_MONITOR_TYPE);

        $kitchenMonitor->addAllMediaFromTokens();

        flash()->success('تم إضافة متابع المطبخ بنجاح.');

        return redirect()->route('dashboard.kitchen_monitors.show', $kitchenMonitor);
    }

    /**
     * Display the specified resource.
     */
    public function show(KitchenMonitor $kitchenMonitor)
    {
        return view('dashboard.accounts.kitchen_monitors.show', compact('kitchenMonitor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KitchenMonitor $kitchenMonitor)
    {
        return view('dashboard.accounts.kitchen_monitors.edit', compact('kitchenMonitor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KitchenMonitorRequest $request, KitchenMonitor $kitchenMonitor)
    {
        $kitchenMonitor->update($request->allWithHashedPassword());

        $kitchenMonitor->setType($request->type ?: KitchenMonitor::KITCHEN_MONITOR_TYPE);

        $kitchenMonitor->addAllMediaFromTokens();

        flash()->success('تم تعديل بيانات متابع المطبخ بنجاح.');

        return redirect()->route('dashboard.kitchen_monitors.show', $kitchenMonitor);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KitchenMonitor $kitchenMonitor)
    {
        $kitchenMonitor->delete();

        flash()->success('تم حذف متابع المطبخ بنجاح.');

        return redirect()->route('dashboard.kitchen_monitors.index');
    }

    /**
     * Display a listing of the trashed resource.
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', KitchenMonitor::class);

        $kitchenMonitors = KitchenMonitor::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.accounts.kitchen_monitors.trashed', compact('kitchenMonitors'));
    }

    /**
     * Display the specified trashed resource.
     */
    public function showTrashed(KitchenMonitor $kitchenMonitor)
    {
        $this->authorize('viewTrash', $kitchenMonitor);

        return view('dashboard.accounts.kitchen_monitors.show', compact('kitchenMonitor'));
    }

    /**
     * Restore the trashed resource.
     */
    public function restore(KitchenMonitor $kitchenMonitor)
    {
        $this->authorize('restore', $kitchenMonitor);

        $kitchenMonitor->restore();

        flash()->success('تم استعادة الحساب بنجاح.');

        return redirect()->route('dashboard.kitchen_monitors.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     */
    public function forceDelete(KitchenMonitor $kitchenMonitor)
    {
        $this->authorize('forceDelete', $kitchenMonitor);

        $kitchenMonitor->forceDelete();

        flash()->success('تم حذف الحساب نهائياً.');

        return redirect()->route('dashboard.kitchen_monitors.trashed');
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Complaint;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\ComplaintRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ComplaintController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * ComplaintController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Complaint::class, 'complaint');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $complaints = Complaint::filter()->latest()->paginate();

        return view('dashboard.complaints.index', compact('complaints'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.complaints.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\ComplaintRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ComplaintRequest $request)
    {
        $complaint = Complaint::create($request->all());

        flash()->success(trans('complaints.messages.created'));

        return redirect()->route('dashboard.complaints.show', $complaint);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Complaint $complaint
     * @return \Illuminate\Http\Response
     */
    public function show(Complaint $complaint)
    {
        return view('dashboard.complaints.show', compact('complaint'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Complaint $complaint
     * @return \Illuminate\Http\Response
     */
    public function edit(Complaint $complaint)
    {
        return view('dashboard.complaints.edit', compact('complaint'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\ComplaintRequest $request
     * @param \App\Models\Complaint $complaint
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ComplaintRequest $request, Complaint $complaint)
    {
        $complaint->update($request->all());

        flash()->success(trans('complaints.messages.updated'));

        return redirect()->route('dashboard.complaints.show', $complaint);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Complaint $complaint
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Complaint $complaint)
    {
        $complaint->delete();

        flash()->success(trans('complaints.messages.deleted'));

        return redirect()->route('dashboard.complaints.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Complaint::class);

        $complaints = Complaint::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.complaints.trashed', compact('complaints'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\Complaint $complaint
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(Complaint $complaint)
    {
        $this->authorize('viewTrash', $complaint);

        return view('dashboard.complaints.show', compact('complaint'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\Complaint $complaint
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Complaint $complaint)
    {
        $this->authorize('restore', $complaint);

        $complaint->restore();

        flash()->success(trans('complaints.messages.restored'));

        return redirect()->route('dashboard.complaints.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\Complaint $complaint
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Complaint $complaint)
    {
        $this->authorize('forceDelete', $complaint);

        $complaint->forceDelete();

        flash()->success(trans('complaints.messages.deleted'));

        return redirect()->route('dashboard.complaints.trashed');
    }
}

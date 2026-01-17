<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Choose;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\ChooseRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ChooseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * ChooseController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Choose::class, 'choose');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $chooses = Choose::filter()->latest()->paginate();

        return view('dashboard.chooses.index', compact('chooses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.chooses.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\ChooseRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ChooseRequest $request)
    {
        $choose = Choose::create($request->all());

        flash()->success(trans('chooses.messages.created'));

        return redirect()->route('dashboard.chooses.show', $choose);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Choose $choose
     * @return \Illuminate\Http\Response
     */
    public function show(Choose $choose)
    {
        return view('dashboard.chooses.show', compact('choose'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Choose $choose
     * @return \Illuminate\Http\Response
     */
    public function edit(Choose $choose)
    {
        return view('dashboard.chooses.edit', compact('choose'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\ChooseRequest $request
     * @param \App\Models\Choose $choose
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ChooseRequest $request, Choose $choose)
    {
        $choose->update($request->all());

        flash()->success(trans('chooses.messages.updated'));

        return redirect()->route('dashboard.chooses.show', $choose);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Choose $choose
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Choose $choose)
    {
        $choose->delete();

        flash()->success(trans('chooses.messages.deleted'));

        return redirect()->route('dashboard.chooses.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Choose::class);

        $chooses = Choose::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.chooses.trashed', compact('chooses'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\Choose $choose
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(Choose $choose)
    {
        $this->authorize('viewTrash', $choose);

        return view('dashboard.chooses.show', compact('choose'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\Choose $choose
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Choose $choose)
    {
        $this->authorize('restore', $choose);

        $choose->restore();

        flash()->success(trans('chooses.messages.restored'));

        return redirect()->route('dashboard.chooses.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\Choose $choose
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Choose $choose)
    {
        $this->authorize('forceDelete', $choose);

        $choose->forceDelete();

        flash()->success(trans('chooses.messages.deleted'));

        return redirect()->route('dashboard.chooses.trashed');
    }
}

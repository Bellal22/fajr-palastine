<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Block;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\BlockRequest;
use App\Models\AreaResponsible;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BlockController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * BlockController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Block::class, 'block');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blocks = Block::filter()->latest()->paginate();

        return view('dashboard.blocks.index', compact('blocks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $areaResponsibles = AreaResponsible::pluck('name', 'id')->toArray();

        return view('dashboard.blocks.create', compact('areaResponsibles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\BlockRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(BlockRequest $request)
    {
        $block = Block::create($request->all());

        flash()->success(trans('blocks.messages.created'));

        return redirect()->route('dashboard.blocks.show', $block);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Block $block
     * @return \Illuminate\Http\Response
     */
    public function show(Block $block)
    {
        return view('dashboard.blocks.show', compact('block'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Block $block
     * @return \Illuminate\Http\Response
     */
    public function edit(Block $block)
    {
        return view('dashboard.blocks.edit', compact('block'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\BlockRequest $request
     * @param \App\Models\Block $block
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(BlockRequest $request, Block $block)
    {
        $block->update($request->all());

        flash()->success(trans('blocks.messages.updated'));

        return redirect()->route('dashboard.blocks.show', $block);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Block $block
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Block $block)
    {
        $block->delete();

        flash()->success(trans('blocks.messages.deleted'));

        return redirect()->route('dashboard.blocks.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Block::class);

        $blocks = Block::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.blocks.trashed', compact('blocks'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\Block $block
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(Block $block)
    {
        $this->authorize('viewTrash', $block);

        return view('dashboard.blocks.show', compact('block'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\Block $block
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Block $block)
    {
        $this->authorize('restore', $block);

        $block->restore();

        flash()->success(trans('blocks.messages.restored'));

        return redirect()->route('dashboard.blocks.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\Block $block
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Block $block)
    {
        $this->authorize('forceDelete', $block);

        $block->forceDelete();

        flash()->success(trans('blocks.messages.deleted'));

        return redirect()->route('dashboard.blocks.trashed');
    }
}

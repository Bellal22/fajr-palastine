<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Item;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\ItemRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ItemController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * ItemController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Item::class, 'item');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Item::filter()->latest()->paginate();

        return view('dashboard.items.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.items.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\ItemRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ItemRequest $request)
    {
        $item = Item::create($request->validated());

        flash()->success(trans('items.messages.created'));

        return redirect()->route('dashboard.items.show', $item);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Item $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        return view('dashboard.items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Item $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        return view('dashboard.items.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\ItemRequest $request
     * @param \App\Models\Item $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ItemRequest $request, Item $item)
    {
        $item->update($request->validated());

        flash()->success(trans('items.messages.updated'));

        return redirect()->route('dashboard.items.show', $item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Item $item
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Item $item)
    {
        $item->delete();

        flash()->success(trans('items.messages.deleted'));

        return redirect()->route('dashboard.items.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Item::class);

        $items = Item::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.items.trashed', compact('items'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\Item $item
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(Item $item)
    {
        $this->authorize('viewTrash', $item);

        return view('dashboard.items.show', compact('item'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\Item $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Item $item)
    {
        $this->authorize('restore', $item);

        $item->restore();

        flash()->success(trans('items.messages.restored'));

        return redirect()->route('dashboard.items.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\Item $item
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Item $item)
    {
        $this->authorize('forceDelete', $item);

        $item->forceDelete();

        flash()->success(trans('items.messages.deleted'));

        return redirect()->route('dashboard.items.trashed');
    }
}

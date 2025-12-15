<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\ItemResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ItemController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the items.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $items = Item::filter()->simplePaginate();

        return ItemResource::collection($items);
    }

    /**
     * Display the specified item.
     *
     * @param \App\Models\Item $item
     * @return \App\Http\Resources\ItemResource
     */
    public function show(Item $item)
    {
        return new ItemResource($item);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $items = Item::filter()->simplePaginate();

        return SelectResource::collection($items);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\Block;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\BlockResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BlockController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the blocks.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $blocks = Block::filter()->simplePaginate();

        return BlockResource::collection($blocks);
    }

    /**
     * Display the specified block.
     *
     * @param \App\Models\Block $block
     * @return \App\Http\Resources\BlockResource
     */
    public function show(Block $block)
    {
        return new BlockResource($block);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $blocks = Block::filter()->simplePaginate();

        return SelectResource::collection($blocks);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\GameWinning;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\GameWinningResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GameWinningController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the game_winnings.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $game_winnings = GameWinning::filter()->simplePaginate();

        return GameWinningResource::collection($game_winnings);
    }

    /**
     * Display the specified game_winning.
     *
     * @param \App\Models\GameWinning $game_winning
     * @return \App\Http\Resources\GameWinningResource
     */
    public function show(GameWinning $game_winning)
    {
        return new GameWinningResource($game_winning);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $game_winnings = GameWinning::filter()->simplePaginate();

        return SelectResource::collection($game_winnings);
    }
}

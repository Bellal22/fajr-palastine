<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\GameWinning;
use App\Models\Person;
use App\Models\CouponType;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\GameWinningRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GameWinningController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * GameWinningController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(GameWinning::class, 'game_winning');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $game_winnings = GameWinning::filter()->latest()->paginate();

        return view('dashboard.game_winnings.index', compact('game_winnings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Fetch only first 500 people to avoid memory issues, ideally we'd use AJAX search
        $people = Person::select('id', 'first_name', 'family_name')
            ->latest()
            ->limit(500)
            ->get()
            ->pluck('name', 'id');

        $couponTypes = CouponType::all()->pluck('name', 'id');

        return view('dashboard.game_winnings.create', compact('people', 'couponTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\GameWinningRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(GameWinningRequest $request)
    {
        $game_winning = GameWinning::create($request->all());

        flash()->success(trans('game_winnings.messages.created'));

        return redirect()->route('dashboard.game_winnings.show', $game_winning);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\GameWinning $game_winning
     * @return \Illuminate\Http\Response
     */
    public function show(GameWinning $game_winning)
    {
        return view('dashboard.game_winnings.show', compact('game_winning'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\GameWinning $game_winning
     * @return \Illuminate\Http\Response
     */
    public function edit(GameWinning $game_winning)
    {
        $people = Person::select('id', 'first_name', 'family_name')
            ->latest()
            ->limit(500)
            ->get()
            ->pluck('name', 'id');

        $couponTypes = CouponType::all()->pluck('name', 'id');

        return view('dashboard.game_winnings.edit', compact('game_winning', 'people', 'couponTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\GameWinningRequest $request
     * @param \App\Models\GameWinning $game_winning
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(GameWinningRequest $request, GameWinning $game_winning)
    {
        $game_winning->update($request->all());

        flash()->success(trans('game_winnings.messages.updated'));

        return redirect()->route('dashboard.game_winnings.show', $game_winning);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\GameWinning $game_winning
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(GameWinning $game_winning)
    {
        $game_winning->delete();

        flash()->success(trans('game_winnings.messages.deleted'));

        return redirect()->route('dashboard.game_winnings.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', GameWinning::class);

        $game_winnings = GameWinning::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.game_winnings.trashed', compact('game_winnings'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\GameWinning $game_winning
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(GameWinning $game_winning)
    {
        $this->authorize('viewTrash', $game_winning);

        return view('dashboard.game_winnings.show', compact('game_winning'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\GameWinning $game_winning
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(GameWinning $game_winning)
    {
        $this->authorize('restore', $game_winning);

        $game_winning->restore();

        flash()->success(trans('game_winnings.messages.restored'));

        return redirect()->route('dashboard.game_winnings.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\GameWinning $game_winning
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(GameWinning $game_winning)
    {
        $this->authorize('forceDelete', $game_winning);

        $game_winning->forceDelete();

        flash()->success(trans('game_winnings.messages.deleted'));

        return redirect()->route('dashboard.game_winnings.trashed');
    }

    /**
     * Show the verification form.
     */
    public function verifyForm()
    {
        return view('dashboard.game_winnings.verify');
    }

    /**
     * Handle the verification request.
     */
    public function verify(\Illuminate\Http\Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $winning = GameWinning::where('code', $request->code)->first();

        if (!$winning) {
            flash()->error(trans('game_winnings.messages.not_found'));
            return redirect()->back();
        }

        return view('dashboard.game_winnings.verify', compact('winning'));
    }

    /**
     * Redeem the winning.
     */
    public function redeem(GameWinning $game_winning)
    {
        if ($game_winning->status === 'redeemed') {
            flash()->warning(trans('game_winnings.messages.already_redeemed'));
            return redirect()->back();
        }

        $game_winning->update([
            'status' => 'redeemed',
            'delivered_at' => now(),
        ]);

        flash()->success(trans('game_winnings.messages.redeemed_success'));

        return redirect()->back();
    }
}

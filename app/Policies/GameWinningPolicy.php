<?php

namespace App\Policies;

use App\Models\User;
use App\Models\GameWinning;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class GameWinningPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any game winnings.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the game winning.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\GameWinning $game_winning
     * @return mixed
     */
    public function view(User $user, GameWinning $game_winning)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.game_winnings');
    }

    /**
     * Determine whether the user can create game winnings.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the game winning.
     *
     * @param \App\Models\User $user
     * @param \App\Models\GameWinning $game_winning
     * @return mixed
     */
    public function update(User $user, GameWinning $game_winning)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.game_winnings'))
            && ! $this->trashed($game_winning);
    }

    /**
     * Determine whether the user can delete the game winning.
     *
     * @param \App\Models\User $user
     * @param \App\Models\GameWinning $game_winning
     * @return mixed
     */
    public function delete(User $user, GameWinning $game_winning)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.game_winnings'))
            && ! $this->trashed($game_winning);
    }

    /**
     * Determine whether the user can view trashed game_winnings.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.game_winnings'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed game_winning.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\GameWinning $game_winning
     * @return mixed
     */
    public function viewTrash(User $user, GameWinning $game_winning)
    {
        return $this->view($user, $game_winning)
            && $game_winning->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\GameWinning $game_winning
     * @return mixed
     */
    public function restore(User $user, GameWinning $game_winning)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.game_winnings'))
            && $this->trashed($game_winning);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\GameWinning $game_winning
     * @return mixed
     */
    public function forceDelete(User $user, GameWinning $game_winning)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.game_winnings'))
            && $this->trashed($game_winning)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given game_winning is trashed.
     *
     * @param $game_winning
     * @return bool
     */
    public function trashed($game_winning)
    {
        return $this->hasSoftDeletes() && method_exists($game_winning, 'trashed') && $game_winning->trashed();
    }

    /**
     * Determine wither the model use soft deleting trait.
     *
     * @return bool
     */
    public function hasSoftDeletes()
    {
        return in_array(
            SoftDeletes::class,
            array_keys((new \ReflectionClass(GameWinning::class))->getTraits())
        );
    }
}

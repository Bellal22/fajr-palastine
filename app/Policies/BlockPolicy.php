<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Block;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class BlockPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any blocks.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.blocks');
    }

    /**
     * Determine whether the user can view the block.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Block $block
     * @return mixed
     */
    public function view(User $user, Block $block)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.blocks');
    }

    /**
     * Determine whether the user can create blocks.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.blocks');
    }

    /**
     * Determine whether the user can update the block.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Block $block
     * @return mixed
     */
    public function update(User $user, Block $block)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.blocks'))
            && ! $this->trashed($block);
    }

    /**
     * Determine whether the user can delete the block.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Block $block
     * @return mixed
     */
    public function delete(User $user, Block $block)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.blocks'))
            && ! $this->trashed($block);
    }

    /**
     * Determine whether the user can view trashed blocks.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.blocks'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed block.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Block $block
     * @return mixed
     */
    public function viewTrash(User $user, Block $block)
    {
        return $this->view($user, $block)
            && $block->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Block $block
     * @return mixed
     */
    public function restore(User $user, Block $block)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.blocks'))
            && $this->trashed($block);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Block $block
     * @return mixed
     */
    public function forceDelete(User $user, Block $block)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.blocks'))
            && $this->trashed($block)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given block is trashed.
     *
     * @param $block
     * @return bool
     */
    public function trashed($block)
    {
        return $this->hasSoftDeletes() && method_exists($block, 'trashed') && $block->trashed();
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
            array_keys((new \ReflectionClass(Block::class))->getTraits())
        );
    }
}

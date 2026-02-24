<?php

namespace App\Policies;

use App\Models\User;
use App\Models\NeedRequest;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class NeedRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any need requests.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view their own need requests.
     */
    public function viewAnyOwn(User $user)
    {
        return $user->isSupervisor();
    }

    /**
     * Determine whether the user can view the need request.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\NeedRequest $need_request
     * @return mixed
     */
    public function view(User $user, NeedRequest $need_request)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.need_requests');
    }

    /**
     * Determine whether the user can create need requests.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('manage.need_requests') && \App\Models\NeedRequestSetting::isEnabledFor($user->id);
    }

    /**
     * Determine whether the user can update the need request.
     *
     * @param \App\Models\User $user
     * @param \App\Models\NeedRequest $need_request
     * @return mixed
     */
    public function update(User $user, NeedRequest $need_request)
    {
        return ($user->isAdmin() || ($user->hasPermissionTo('manage.need_requests') && $need_request->supervisor_id === $user->id))
            && ! $this->trashed($need_request)
            && $need_request->isPending();
    }

    /**
     * Determine whether the user can review the request.
     */
    public function review(User $user, NeedRequest $need_request)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can nominate beneficiaries.
     */
    public function nominate(User $user, NeedRequest $need_request)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can export need requests.
     */
    public function export(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can manage settings.
     */
    public function manageSettings(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the need request.
     *
     * @param \App\Models\User $user
     * @param \App\Models\NeedRequest $need_request
     * @return mixed
     */
    public function delete(User $user, NeedRequest $need_request)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view trashed need_requests.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return $user->isAdmin() && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed need_request.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\NeedRequest $need_request
     * @return mixed
     */
    public function viewTrash(User $user, NeedRequest $need_request)
    {
        return $this->view($user, $need_request)
            && $need_request->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\NeedRequest $need_request
     * @return mixed
     */
    public function restore(User $user, NeedRequest $need_request)
    {
        return $user->isAdmin() && $this->trashed($need_request);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\NeedRequest $need_request
     * @return mixed
     */
    public function forceDelete(User $user, NeedRequest $need_request)
    {
        return $user->isAdmin()
            && $this->trashed($need_request)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given need_request is trashed.
     *
     * @param $need_request
     * @return bool
     */
    public function trashed($need_request)
    {
        return $this->hasSoftDeletes() && method_exists($need_request, 'trashed') && $need_request->trashed();
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
            array_keys((new \ReflectionClass(NeedRequest::class))->getTraits())
        );
    }
}

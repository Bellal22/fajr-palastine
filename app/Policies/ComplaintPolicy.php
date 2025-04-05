<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Complaint;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComplaintPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any complaints.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.complaints');
    }

    /**
     * Determine whether the user can view the complaint.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Complaint $complaint
     * @return mixed
     */
    public function view(User $user, Complaint $complaint)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.complaints');
    }

    /**
     * Determine whether the user can create complaints.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage.complaints');
    }

    /**
     * Determine whether the user can update the complaint.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Complaint $complaint
     * @return mixed
     */
    public function update(User $user, Complaint $complaint)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.complaints'))
            && ! $this->trashed($complaint);
    }

    /**
     * Determine whether the user can delete the complaint.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Complaint $complaint
     * @return mixed
     */
    public function delete(User $user, Complaint $complaint)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.complaints'))
            && ! $this->trashed($complaint);
    }

    /**
     * Determine whether the user can view trashed complaints.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.complaints'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed complaint.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Complaint $complaint
     * @return mixed
     */
    public function viewTrash(User $user, Complaint $complaint)
    {
        return $this->view($user, $complaint)
            && $complaint->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Complaint $complaint
     * @return mixed
     */
    public function restore(User $user, Complaint $complaint)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.complaints'))
            && $this->trashed($complaint);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Complaint $complaint
     * @return mixed
     */
    public function forceDelete(User $user, Complaint $complaint)
    {
        return ($user->isAdmin() || $user->hasPermissionTo('manage.complaints'))
            && $this->trashed($complaint)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given complaint is trashed.
     *
     * @param $complaint
     * @return bool
     */
    public function trashed($complaint)
    {
        return $this->hasSoftDeletes() && method_exists($complaint, 'trashed') && $complaint->trashed();
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
            array_keys((new \ReflectionClass(Complaint::class))->getTraits())
        );
    }
}

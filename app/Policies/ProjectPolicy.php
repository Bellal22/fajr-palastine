<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any projects.
     *
     * @param \App\Models\User|null $user
     * @return mixed
     */
    public function viewAny(?User $user)
    {
        if (!$user) {
            return false;
        }
        return $user->isAdmin() || $user->hasPermission('manage.projects');
    }

    /**
     * Determine whether the user can view the project.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Project $project
     * @return mixed
     */
    public function view(?User $user, Project $project)
    {
        if (!$user) {
            return false;
        }
        return $user->isAdmin() || $user->hasPermission('manage.projects');
    }

    /**
     * Determine whether the user can create projects.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermission('manage.projects');
    }

    /**
     * Determine whether the user can update the project.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Project $project
     * @return mixed
     */
    public function update(User $user, Project $project)
    {
        return ($user->isAdmin() || $user->hasPermission('manage.projects'))
            && ! $this->trashed($project);
    }

    /**
     * Determine whether the user can delete the project.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Project $project
     * @return mixed
     */
    public function delete(User $user, Project $project)
    {
        return ($user->isAdmin() || $user->hasPermission('manage.projects'))
            && ! $this->trashed($project);
    }

    /**
     * Determine whether the user can view trashed projects.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAnyTrash(User $user)
    {
        return ($user->isAdmin() || $user->hasPermission('manage.projects'))
            && $this->hasSoftDeletes();
    }

    /**
     * Determine whether the user can view the trashed project.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Project $project
     * @return mixed
     */
    public function viewTrash(?User $user, Project $project)
    {
        if (!$user) {
            return false;
        }
        return $this->view($user, $project)
            && $project->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Project $project
     * @return mixed
     */
    public function restore(User $user, Project $project)
    {
        return ($user->isAdmin() || $user->hasPermission('manage.projects'))
            && $this->trashed($project);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Project $project
     * @return mixed
     */
    public function forceDelete(User $user, Project $project)
    {
        return ($user->isAdmin() || $user->hasPermission('manage.projects'))
            && $this->trashed($project)
            && Settings::get('delete_forever');
    }

    /**
     * Determine wither the given project is trashed.
     *
     * @param $project
     * @return bool
     */
    public function trashed($project)
    {
        return $this->hasSoftDeletes() && method_exists($project, 'trashed') && $project->trashed();
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
            array_keys((new \ReflectionClass(Project::class))->getTraits())
        );
    }
}

<?php

namespace App\Observers;

use App\Models\Project;
use App\Support\ExternalApiSync;

class ProjectObserver
{
    /**
     * Handle the Project "created" event.
     *
     * @param \App\Models\Project $project
     * @return void
     */
    public function created(Project $project)
    {
        ExternalApiSync::syncProject($project);
    }

    /**
     * Handle the Project "updated" event.
     *
     * @param \App\Models\Project $project
     * @return void
     */
    public function updated(Project $project)
    {
        ExternalApiSync::syncProject($project);
    }
}

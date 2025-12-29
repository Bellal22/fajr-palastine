<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\ProjectResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the projects.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $projects = Project::filter()->simplePaginate();

        return ProjectResource::collection($projects);
    }

    /**
     * Display the specified project.
     *
     * @param \App\Models\Project $project
     * @return \App\Http\Resources\ProjectResource
     */
    public function show(Project $project)
    {
        return new ProjectResource($project);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $projects = Project::filter()->simplePaginate();

        return SelectResource::collection($projects);
    }
}

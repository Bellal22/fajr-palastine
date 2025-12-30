<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Project;
use App\Models\ReadyPackage;
use App\Models\InternalPackage;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\ProjectRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * ProjectController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Project::class, 'project');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::filter()->latest()->paginate();

        return view('dashboard.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.projects.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\ProjectRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProjectRequest $request)
    {
        $project = Project::create($request->only([
            'name',
            'description',
            'start_date',
            'end_date',
            'status'
        ]));

        $this->syncRelations($project, $request);

        flash()->success(trans('projects.messages.created'));
        return redirect()->route('dashboard.projects.show', $project);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        $project->load([
            'grantingEntities',
            'executingEntities',
            'couponTypes',
            'readyPackages',
            'internalPackages',
            // 'outboundShipments',
            // 'inboundShipments'
        ]);

        return view('dashboard.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        $project->load([
            'grantingEntities',
            'executingEntities',
            'couponTypes',
            'readyPackages',
            'internalPackages'
        ]);

        return view('dashboard.projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\ProjectRequest $request
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProjectRequest $request, Project $project)
    {
        $project->update($request->only([
            'name',
            'description',
            'start_date',
            'end_date',
            'status'
        ]));

        $this->syncRelations($project, $request);

        flash()->success(trans('projects.messages.updated'));
        return redirect()->route('dashboard.projects.show', $project);
    }

    /**
     * Sync all relations.
     *
     * @param \App\Models\Project $project
     * @param \App\Http\Requests\Dashboard\ProjectRequest $request
     * @return void
     */
    private function syncRelations(Project $project, ProjectRequest $request)
    {
        // 1. Sync Partners
        DB::table('project_partners')->where('project_id', $project->id)->delete();

        if ($request->filled('granting_entities')) {
            foreach ($request->granting_entities as $id) {
                DB::table('project_partners')->insert([
                    'project_id' => $project->id,
                    'supplier_id' => $id,
                    'type' => 'granting',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        if ($request->filled('executing_entities')) {
            foreach ($request->executing_entities as $id) {
                DB::table('project_partners')->insert([
                    'project_id' => $project->id,
                    'supplier_id' => $id,
                    'type' => 'executing',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // 2. Sync Coupon Types
        $couponTypesData = [];
        if ($request->filled('coupon_types')) {
            foreach ($request->coupon_types as $item) {
                if (isset($item['coupon_type_id']) && isset($item['quantity'])) {
                    $couponTypesData[$item['coupon_type_id']] = ['quantity' => $item['quantity']];
                }
            }
        }
        $project->couponTypes()->sync($couponTypesData);

        // 3. Sync Packages (Polymorphic)
        DB::table('project_packages')->where('project_id', $project->id)->delete();

        if ($request->filled('ready_packages')) {
            foreach ($request->ready_packages as $id) {
                DB::table('project_packages')->insert([
                    'project_id' => $project->id,
                    'packageable_id' => $id,
                    'packageable_type' => 'App\Models\ReadyPackage',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        if ($request->filled('internal_packages')) {
            foreach ($request->internal_packages as $id) {
                DB::table('project_packages')->insert([
                    'project_id' => $project->id,
                    'packageable_id' => $id,
                    'packageable_type' => 'App\Models\InternalPackage',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Project $project
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Project $project)
    {
        $project->delete();

        flash()->success(trans('projects.messages.deleted'));

        return redirect()->route('dashboard.projects.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Project::class);

        $projects = Project::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.projects.trashed', compact('projects'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(Project $project)
    {
        $this->authorize('viewTrash', $project);

        return view('dashboard.projects.show', compact('project'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Project $project)
    {
        $this->authorize('restore', $project);

        $project->restore();

        flash()->success(trans('projects.messages.restored'));

        return redirect()->route('dashboard.projects.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\Project $project
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Project $project)
    {
        $this->authorize('forceDelete', $project);

        $project->forceDelete();

        flash()->success(trans('projects.messages.deleted'));

        return redirect()->route('dashboard.projects.trashed');
    }
}

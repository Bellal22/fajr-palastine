@if(method_exists($project, 'trashed') && $project->trashed())
    @can('view', $project)
        <a href="{{ route('dashboard.projects.trashed.show', $project) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $project)
        <a href="{{ route('dashboard.projects.show', $project) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif
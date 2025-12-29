@if($project)
    @if(method_exists($project, 'trashed') && $project->trashed())
        <a href="{{ route('dashboard.projects.trashed.show', $project) }}" class="text-decoration-none text-ellipsis">
            {{ $project->name }}
        </a>
    @else
        <a href="{{ route('dashboard.projects.show', $project) }}" class="text-decoration-none text-ellipsis">
            {{ $project->name }}
        </a>
    @endif
@else
    ---
@endif
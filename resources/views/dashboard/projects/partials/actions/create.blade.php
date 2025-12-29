@can('create', \App\Models\Project::class)
    <a href="{{ route('dashboard.projects.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('projects.actions.create')
    </a>
@endcan

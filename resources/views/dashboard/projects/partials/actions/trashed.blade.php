@can('viewAnyTrash', \App\Models\Project::class)
    <a href="{{ route('dashboard.projects.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('projects.trashed')
    </a>
@endcan

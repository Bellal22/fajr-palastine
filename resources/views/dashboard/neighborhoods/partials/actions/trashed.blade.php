@can('viewAnyTrash', \App\Models\Neighborhood::class)
    <a href="{{ route('dashboard.neighborhoods.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('neighborhoods.trashed')
    </a>
@endcan

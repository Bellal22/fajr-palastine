@can('viewAnyTrash', \App\Models\Region::class)
    <a href="{{ route('dashboard.regions.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('regions.trashed')
    </a>
@endcan

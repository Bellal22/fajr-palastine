@can('viewAnyTrash', \App\Models\Map::class)
    <a href="{{ route('dashboard.maps.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('maps.trashed')
    </a>
@endcan

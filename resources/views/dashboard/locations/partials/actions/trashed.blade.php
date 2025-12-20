@can('viewAnyTrash', \App\Models\Location::class)
    <a href="{{ route('dashboard.locations.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('locations.trashed')
    </a>
@endcan

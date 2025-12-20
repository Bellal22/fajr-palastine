@can('create', \App\Models\Map::class)
    <a href="{{ route('dashboard.maps.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('maps.actions.create')
    </a>
@endcan

@can('create', \App\Models\Location::class)
    <a href="{{ route('dashboard.locations.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('locations.actions.create')
    </a>
@endcan

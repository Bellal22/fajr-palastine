@can('create', \App\Models\Region::class)
    <a href="{{ route('dashboard.regions.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('regions.actions.create')
    </a>
@endcan

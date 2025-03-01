@can('create', \App\Models\Neighborhood::class)
    <a href="{{ route('dashboard.neighborhoods.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('neighborhoods.actions.create')
    </a>
@endcan

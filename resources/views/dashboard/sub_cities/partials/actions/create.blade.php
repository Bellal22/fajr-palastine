@can('create', \App\Models\SubCity::class)
    <a href="{{ route('dashboard.sub_cities.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('sub_cities.actions.create')
    </a>
@endcan

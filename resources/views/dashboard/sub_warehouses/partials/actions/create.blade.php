@can('create', \App\Models\SubWarehouse::class)
    <a href="{{ route('dashboard.sub_warehouses.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('sub_warehouses.actions.create')
    </a>
@endcan

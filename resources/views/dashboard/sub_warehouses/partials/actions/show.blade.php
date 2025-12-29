@if(method_exists($sub_warehouse, 'trashed') && $sub_warehouse->trashed())
    @can('view', $sub_warehouse)
        <a href="{{ route('dashboard.sub_warehouses.trashed.show', $sub_warehouse) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $sub_warehouse)
        <a href="{{ route('dashboard.sub_warehouses.show', $sub_warehouse) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif
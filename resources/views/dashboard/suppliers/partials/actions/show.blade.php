@if(method_exists($supplier, 'trashed') && $supplier->trashed())
    @can('view', $supplier)
        <a href="{{ route('dashboard.suppliers.trashed.show', $supplier) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $supplier)
        <a href="{{ route('dashboard.suppliers.show', $supplier) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif
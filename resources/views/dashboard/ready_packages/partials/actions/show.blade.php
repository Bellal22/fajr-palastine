@if(method_exists($ready_package, 'trashed') && $ready_package->trashed())
    @can('view', $ready_package)
        <a href="{{ route('dashboard.ready_packages.trashed.show', $ready_package) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $ready_package)
        <a href="{{ route('dashboard.ready_packages.show', $ready_package) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif
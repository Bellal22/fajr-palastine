@if(method_exists($internal_package, 'trashed') && $internal_package->trashed())
    @can('view', $internal_package)
        <a href="{{ route('dashboard.internal_packages.trashed.show', $internal_package) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $internal_package)
        <a href="{{ route('dashboard.internal_packages.show', $internal_package) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif
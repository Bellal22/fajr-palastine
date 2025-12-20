@if(method_exists($location, 'trashed') && $location->trashed())
    @can('view', $location)
        <a href="{{ route('dashboard.locations.trashed.show', $location) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $location)
        <a href="{{ route('dashboard.locations.show', $location) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif